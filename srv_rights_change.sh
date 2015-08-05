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