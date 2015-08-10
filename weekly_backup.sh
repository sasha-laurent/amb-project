#!/usr/bin/env bash
# weekly_backup.sh
# Weekly backup script for the Mediations Builder Project
#
# Note: Must be owned by root, be executable, not be writable by group or other
# To open read+execute: chmod +rx weekly_backup.sh
#
# Ideas: 
# - DB credentials should be parsed from parameters.yml (or given as arguments)
# - Success/fail status for each op output to $HOME_DIR/backup/log/dated_log.txt
#		- Bonus if we commit log files
# - /var/www/{site placeholder} should be an argument
#
# To copy the files use: (should be output to )
# scp username@10.77.6.23:/var/www/edu/backup /local

TODAY=$(date +%F)
HOME_DIR=/var/www/edu

# Create backup directory if not found
if [[ ! -d "$HOME_DIR/backup/" ]]; then
	mkdir "$HOME_DIR/backup/" || { exit "[FAIL] Can't create backup directory."; }
fi

# Compress and save upload directory
tar -czf $HOME_DIR/backup/upload/uploaded_files_$TODAY.tar.gz $HOME_DIR/web/upload
# Dump database data
mysqldump vmb -u root -pJGIRZrRk > $HOME_DIR/backup/data/amb_sql_$TODAY
# Remove backup files older than a month =~30 days
find $HOME_DIR/backup/ -mtime +30 -exec rm {} \;
# Exit without errors
exit 0
