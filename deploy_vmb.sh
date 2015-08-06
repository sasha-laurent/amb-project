#/bin/sh env sh
#Script de mise à jour de l'outil VMB

#Créer une clef privée/public pour le projet

#Passage du pull avec la clef
echo "début de la mise à jour"
git pull https://bitbucket.org/Taurus17/vmb.git production

echo "Migration Base de Données"
php app/console doctrine:schema:update --force

#Mise à jour du cache
echo "Nettoyage du code"
php app/console cache:clear --env=prod
php app/console cache:warmup

echo "Mise à jour effectuée"
