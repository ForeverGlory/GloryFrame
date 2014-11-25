#!/bin/bash

########################
# 环境变量
# source settings.sh
########################

ROOT=$(dirname `dirname $0`)
cd $ROOT

ALIAS=none
if [ $1 ]; then
  ALIAS=$1
fi
drush use @$ALIAS

BUILD=builds
BACKUP=$BUILD/backup
WEB=$BUILD/web
LOG=$BUILD/logs
Config=configs
SCRIPT=scripts