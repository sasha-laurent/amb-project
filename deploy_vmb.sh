#/bin/sh env sh


#Script de mise à jour de l'outil VMB
#Créer une clef privée/public pour le projet

#Passage du pull avec la clef
echo "début de la mise à jour"
git pull

#Mise à jour du cache
echo "Changement de propriétaires"
chown -R root:www-data app/cache
chown -R root:www-data app/logs
chown -R root:www-data app/config/parameters.yml
chown -R root:www-data /var/www/edu/app/Resources/translations/

echo "Changement de droits"
chmod -R 775 app/cache
chmod -R 775 app/logs
chmod -R 775 app/config/parameters.yml
chmod -R 775 /var/www/edu/app/Resources/translations/

echo "Mise à jour effectuée"
