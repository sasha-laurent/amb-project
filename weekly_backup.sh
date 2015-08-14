#!/usr/bin/env bash
# weekly_backup.sh
# Weekly backup script for the Mediations Builder Project
#
# Note: Must be owned by root, be executable, not be writable by group or other
# To open read+execute: chmod +rx weekly_backup.sh
#
# Ideas: 
# - Success/fail status for each op output to $HOME_DIR/backup/log/dated_log.txt
#	-> Bonus if we commit log files
# - /var/www/{site placeholder} should be an argument
# To copy the files use: (should be output in help option)
# scp username@10.77.6.23:/var/www/edu/backup /local_folder
# DB credentials should be parsed from app/config/parameters.yml (or given as arguments)
# pass = awk -F: '/database_password:\s\w/{print $2}' $HOME_DIR/app/config/parameters.yml
TODAY=$(date +%F)
HOME_DIR=/var/www/edu

if [[ ! -d "$HOME_DIR/backup/" ]]; then
	echo "Creating backup directories if not found"
	mkdir "$HOME_DIR/backup/" || { exit "[FAIL] Can't create backup directory." ;}
	mkdir "$HOME_DIR/backup/ontologies/"
	mkdir "$HOME_DIR/backup/upload/"
	mkdir "$HOME_DIR/backup/data/"
fi

TAR_OPTS=--ignore-failed-read
echo "Compressing and saving ontologies."
tar -czf $HOME_DIR/backup/ontologies/ont_$TODAY.tar.gz $HOME_DIR/web/bundles/telecomvmb/json $TAR_OPTS
echo "Compressing and saving upload directory."
tar -czf $HOME_DIR/backup/upload/uploaded_files_$TODAY.tar.gz $HOME_DIR/web/upload $TAR_OPTS

echo "Dumping database data."
mysqldump vmb -u root -pJGIRZrRk > $HOME_DIR/backup/data/amb_sql_$TODAY

echo "Removing backup files older than a month" 
find $HOME_DIR/backup/ -mtime +30 -exec rm {} \;

echo "Ok means Ola Kala."
exit 0
