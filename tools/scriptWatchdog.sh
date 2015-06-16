#!/bin/bash
SNACKNAME=`grep "Issuer: C=FR, ST=France, O=B.H. Consulting, CN=" /home/snack/cert/cacert.pem | cut -d"=" -f5`
EMAIL=`grep configurationEmail /home/snack/interface/app/Config/parameters.php | cut -d"'" -f4`
MAILFROM=`grep smtp_email_from /home/snack/interface/app/Config/parameters.php | cut -d"'" -f4`
PASSWORD=`grep password /home/snack/interface/app/Config/database.php | tac | tail -1 | cut -d"'" -f4`
MAIL=/tmp/mail
NOTIF=/home/snack/interface/app/tmp/notifications.txt
NOTIFPREC=/home/snack/interface/app/tmp/notifications-prec.txt

function check-freeradius {
    if [ -f /tmp/watchdog-freeradius ]; then
        echo "Freeradius en cours de redémarrage";
    else
        echo "" > /tmp/watchdog-freeradius
        res=$(netstat -lu | grep radius | wc -l)
        if [[ "$res" != "2" ]]; then
            datenow=$(date "+%Y-%m-%d %H:%m")
            echo "[$datenow] [ERR] SNACK Freeradius restart" >> $NOTIF
            service freeradius stop
            service freeradius start
        fi
        rm -f /tmp/watchdog-freeradius        
     fi
}

function check-mysql {
    if [ -f /tmp/watchdog-mysql ]; then
        echo "MySQL en cours de redémarrage";
    else
        echo "" > /tmp/watchdog-mysql
        mysql -uradius -p${PASSWORD} -e "SELECT NOW()" > /dev/null
        if [ $? -ne 0 ]; then
            datenow=$(date "+%Y-%m-%d %H:%m")
            echo "[$datenow] [ERR] SNACK Mysql restart" >> $NOTIF
            service mysql stop
            service mysql start
        fi
        rm -f /tmp/watchdog-mysql        
     fi
}

function check-apache {
    if [ -f /tmp/watchdog-apache ]; then
        echo "Apache en cours de redémarrage";
    else
        echo "" > /tmp/watchdog-apache
        curl --insecure --connect-timeout 2 https://localhost/ -o /dev/null > /dev/null 2> /dev/null
        if [ $? -ne 0 ]; then
            datenow=$(date "+%Y-%m-%d %H:%m")
            echo "[$datenow] [ERR] SNACK Apache2 restart" >> $NOTIF
            service apache2 stop
            service apache2 start
        fi
        rm -f /tmp/watchdog-apache
    fi
}

function check-elasticsearch {
    if [ -f /tmp/watchdog-elasticsearch ]; then
        echo "Elasticsearch en cours de redémarrage";
    else
        echo "" > /tmp/watchdog-elasticsearch
        curl "http://localhost:9200/_cluster/health?pretty" -o /dev/null > /dev/null 2> /dev/null
        if [ $? -ne 0 ]; then
            datenow=$(date "+%Y-%m-%d %H:%m")
            echo "[$datenow] [ERR] SNACK Elasticsearch restart" >> $NOTIF
            service elasticsearch stop
            service elasticsearch start
        fi
        rm -f /tmp/watchdog-elasticsearch
    fi
}

function check-tdagent {
    if [ -f /tmp/watchdog-tdagent ]; then
        echo "Td-agent en cours de redémarrage";
    else
        echo "" > /tmp/watchdog-tdagent
        netstat -lun | grep 5140
        if [ $? -ne 0 ]; then
            datenow=$(date "+%Y-%m-%d %H:%m")
            echo "[$datenow] [ERR] SNACK Td-agent restart" >> $NOTIF
            service td-agent stop
            service td-agent start
        fi
        rm -f /tmp/watchdog-tdagent
    fi
}

function check-tftpd {
    if [ -f /tmp/watchdog-tftpd ]; then
        echo "Tftpd en cours de redémarrage";
    else
        echo "" > /tmp/watchdog-tftpd
        ps -e -o comm,etime | grep tftp
        if [ $? -ne 0 ]; then
            datenow=$(date "+%Y-%m-%d %H:%m")
            echo "[$datenow] [ERR] SNACK Tftpd restart" >> $NOTIF
            service tftpd-hpa stop
            service tftpd-hpa start
        fi
        rm -f /tmp/watchdog-tftpd
    fi
}

function check-disk-used {
    output=""
    output2=""
    output=`df -h | grep /home | head -1  | awk -F " " '{ print $5 }' | cut -d'%' -f1`
    output2=`df -h | grep / | head -1  | awk -F " " '{ print $5 }' | cut -d'%' -f1`
    re='^[0-9]+$'
    if [[ $output =~ $re ]] ; then
        if [ $output -ge 90 ]; then
            datenow=$(date "+%Y-%m-%d %H:%m")
            echo "[$datenow] [ERR] SNACK Space Disk /home CRITICAL" >> $NOTIF
        fi
    fi
    if [[ $output2 =~ $re ]] ; then
        if [ $output2 -ge 90 ]; then
            datenow=$(date "+%Y-%m-%d %H:%m")
            echo "[$datenow] [ERR] SNACK Space Disk / CRITICAL" >> $NOTIF
        fi
    fi
}

SYNC=/tmp/snacksync.lock
while [ -f $SYNC ]
do
    echo "Synchro en cours ... en attente"
    sleep 5
done
touch $SYNC
if [ -f $NOTIF ]; then
    cp $NOTIF $NOTIFPREC
fi
echo "" > $NOTIF
chmod 660 $NOTIF
chown root:snack $NOTIF
chmod 660 $NOTIFPREC
chown root:snack $NOTIFPREC

check-freeradius
check-mysql
check-apache
check-elasticsearch
check-tdagent
check-tftpd
check-disk-used
/home/snack/interface/tools/scriptProvAD.sh >> $NOTIF
sudo -u www-data  /home/snack/interface/app/Console/cake SnackSendReports
rm $SYNC