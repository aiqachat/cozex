#!/bin/bash

basepath=$(cd `dirname $0`; pwd)
chmod a+x "$basepath/yii"
command="php $basepath/yii queue/listen 1"

result=$(ps -ef | grep "`echo $command`" | grep -v "grep")

if [ ! -n "$result" ]
then
  echo "Starting the process."
  str=$(nohup $command >/dev/null 2>&1 &)
  echo -e "\033[32mOk.\033[0m"
else
  echo "The process has been started."
fi

result=$(crontab -l|grep -i "* * * * * $basepath/queue.sh"|grep -v grep)
if [ ! -n "$result" ]
then
  echo -e "\033[32mCreating queue crontab.\033[0m"
  echo "Export crontab data"
  crontab -l > createcrontemp
  echo "Add new crontab line"
  echo "* * * * * $basepath/queue.sh" >> createcrontemp
  echo "Import crontab data"
  crontab createcrontemp
  echo "Delete temp file"
  rm -f createcrontemp
  echo -e "\033[32mCreating queue crontab success.\033[0m"
else
  echo "The queue crontab has been add ."
fi
