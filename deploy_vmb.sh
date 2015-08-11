#/bin/sh env sh
# deploy_vmb.sh
# Script de mise à jour du code de production

# Utiliser une clef privée/public pour le projet
# En créer une pour l'utilisateur www-data?
# Ajouter command="/bin/git",from="bitbucket.org",no-port-forwarding,no-agent-forwarding,no-X11-forwarding,no-pty au hash rsa pub
echo "début de la mise à jour"
git pull

echo "Migration Base de Données" 
# TODO: Voir comment on pourrait mieux migrer les changements DB
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
<<<<<<< HEAD
 
=======

>>>>>>> fc6c4d5ad1a34e3072292e80e7214be4b5ffb2b0
echo "Mise à jour effectuée"
