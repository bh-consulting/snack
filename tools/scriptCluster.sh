#!/bin/bash
SYNC=/tmp/snacksync.lock
while [ -f $SYNC ]
do
    echo "Synchro en cours ... en attente"
    sleep 5
done
touch $SYNC
LOG=ha-`TZ='Europe/Paris' date "+%Y-%m-%d_%H-%M"`.log
USER_HOME=/home/snack
ROLE=`grep role $USER_HOME/interface/app/Config/parameters.php | cut -d"'" -f4`
if [ "$ROLE" == 'slave' ]; then
    MASTER=`grep master_ip $USER_HOME/interface/app/Config/parameters.php | cut -d"'" -f4`
    IP=`grep ipAddress $USER_HOME/interface/app/Config/parameters.php | cut -d"'" -f4`   
    NAME=`ssh root@$MASTER $USER_HOME/interface/tools/scriptSnackExport.sh`
    scp root@$MASTER:/home/snack/interface/app/conf/$NAME /tmp
    ssh root@$MASTER rm /home/snack/interface/app/conf/$NAME
    sudo /home/snack/interface/tools/scriptSnackImport.sh --file /tmp/$NAME
    rm -f /tmp/$NAME
    #rm -f /tmp/log
    sed \
        -e "s/\('ipAddress' => '\)[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*/\1$IP/" \
        -i $USER_HOME/interface/app/Config/parameters.php
    sed \
        -e "s/\('role' => '\)master/\1$ROLE/" \
        -i $USER_HOME/interface/app/Config/parameters.php
    sed \
        -e "s/\('master_ip' => '\)[0-9]*\.*[0-9]*\.*[0-9]*\.*[0-9]*/\1$MASTER/" \
        -i $USER_HOME/interface/app/Config/parameters.php
    mv /tmp/log-import /tmp/$LOG
    echo "IP:$IP" >> /tmp/$LOG
    scp /tmp/$LOG root@$MASTER:$USER_HOME/interface/app/tmp/ha
    rm /tmp/$LOG
fi
rm $SYNC