#/bin/sh env sh


#Script de mise à jour de l'outil VMB
#Créer une clef privée/public pour le projet

#Passage du pull avec la clef
echo "début de la mise à jour"
git pull https://atritas@bitbucket.org/Taurus17/vmb.git master


#Mise à jour du cache
echo "Nettoyage du code"
php app/console cache:clear --env=prod
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
