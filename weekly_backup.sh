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
#
# To copy the files use: (should be output in help option)
# scp username@10.77.6.23:/var/www/edu/backup /local_folder

TODAY=$(date +%F)
HOME_DIR=/var/www/edu

# Create backup directory if not found
# TODO: Also create upload/ and data/ dirs
if [[ ! -d "$HOME_DIR/backup/" ]]; then
	mkdir "$HOME_DIR/backup/" || { exit "[FAIL] Can't create backup directory." ;}
	mkdir "$HOME_DIR/backup/ontologies/"
	mkdir "$HOME_DIR/backup/upload/"
	mkdir "$HOME_DIR/backup/data/"
fi

# Compress and save ontologies
tar -czf $HOME_DIR/backup/ontologies/ont_$TODAY.tar.gz $HOME_DIR/web/bundles/telecomvmb/json
# Compress and save upload directory
tar -czf $HOME_DIR/backup/upload/uploaded_files_$TODAY.tar.gz $HOME_DIR/web/upload

#DB credentials should be parsed from app/config/parameters.yml (or given as arguments)
pass = awk -F: '/database_password:\s\w/{print $2}': $HOME_DIR/app/config/parameters.yml
# Dump database data
mysqldump vmb > $HOME_DIR/backup/data/amb_sql_$TODAY

# Remove backup files older than a month =~30 days
find $HOME_DIR/backup/ -mtime +30 -exec rm {} \;
# Exit without errors
exit 0