#!/bin/bash

########################
# 备份数据 mysql/web
# ./backup.sh live
########################

source settings.sh

DATE=`date +%Y%m%d-%H%M`

MysqlName='mysql.tar.gz'
WebName='web.tar.gz'

BackupPath=$BACKUP/$DATE
mkdir $BackupPath -p

echo "Backup Mysql"

drush sql-dump | gzip > $BackupPath/$MysqlName

echo "Backup Web"
tar -zcvf $BackupPath/$WebName $WEB

echo "Backup Success"
echo "Backup Path is: $ROOT/$BackupPath/"