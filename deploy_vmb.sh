#/bin/sh env sh
# deploy_vmb.sh
# Script de mise à jour du code de production
# Utilisation d'une clef privée/public pour git
# Ajouter command="/bin/git",from="bitbucket.org",no-port-forwarding,no-agent-forwarding,no-X11-forwarding,no-pty pour securiser un peu l'acces sur le serveurhash rsa pub
# Options d'erreurs, IFS= Internal Field Separator
set -euo pipefail
IFS=$'\n\t'

cd /var/www/edu/
echo "Début de la mise à jour"

NOW=$(date +%c)
git add .
if [[ `git status --porcelain` ]]; then
	git commit -am "Automatic server commit $NOW"
fi
git pull 
git push

echo "Migration Base de Données" 
# TODO: Voir comment on pourrait mieux migrer les changements DB
php app/console doctrine:schema:update --dump-sql
php app/console doctrine:schema:update --force

#Mise à jour du cache
echo "Nettoyage du code"
php app/console cache:clear --env=prod
php app/console cache:warmup --env=prod

echo "Changement de propriétaires"
chown -R www-data:www-data app/cache/
chown -R www-data:www-data app/logs/
chown -R www-data:www-data app/config/parameters.yml
chown -R www-data:www-data app/Resources/translations/
chown -R www-data:www-data web/upload/

echo "Changement de droits"
chmod -R 775 app/cache
chmod -R 775 app/logs
chmod -R 775 app/config/parameters.yml
chmod -R 775 app/Resources/translations/
chmod -R 775 web/upload/

echo "Mise à jour effectuée"
