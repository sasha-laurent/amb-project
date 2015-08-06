#!/usr/bin/env bash
# weekly_backup.sh
# Weekly backup script for the Mediations Builder Project
# Note: Must be owned by root, be executable, not be writable by group or other
# To open read+execute: chmod +rx weekly_backup.sh
# To copy the files use: 
# scp username@10.77.6.23:/var/www/edu/backup /local
TODAY=$(date +%F)
HOME_DIR=/var/www/edu
# TODO: Create backup directory if not found
# Compress and save upload directory
tar -czf $HOME_DIR/backup/upload/uploaded_files_$TODAY.tar.gz $HOME_DIR/web/upload
# Dump database data (set username and password in clear text?)
mysqldump vmb > $HOME_DIR/backup/data/amb_sql_$TODAY
# Remove backup files older than a month =~30 days
find $HOME_DIR/backup/ -mtime +30 -exec rm {} \;
# Exit without errors
exit 0