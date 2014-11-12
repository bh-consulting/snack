#!/bin/bash
SNACKNAME=`grep "Issuer: C=FR, ST=France, O=B.H. Consulting, CN=" /home/snack/cert/cacert.pem | cut -d"=" -f5`
EMAIL=`grep configurationEmail /home/snack/interface/app/Config/parameters.php | cut -d"'" -f4`
MAILFROM=`grep smtp_email_from /home/snack/interface/app/Config/parameters.php | cut -d"'" -f4`
PASSWORD=`grep password /home/snack/interface/app/Config/database.php | tac | tail -1 | cut -d"'" -f4`
MAIL=/tmp/mail
NOTIF=/home/snack/interface/app/tmp/notifications.txt
echo "" > $NOTIF
chmod 660 $NOTIF
chown root:snack $NOTIF

function check-freeradius {
    if [ -f /tmp/watchdog-freeradius ]; then
        echo "Freeradius en cours de redémarrage";
    else
        echo "" > /tmp/watchdog-freeradius
        ADMINPASS=`mysql -uradius -p${PASSWORD} radius -NBe "SELECT value from radcheck where username='admin'"`
        res=$(( echo "User-Name = \"admin\"";   echo "Cleartext-Password = \"$ADMINPASS\"";   echo "EAP-Code = Response";   echo "EAP-Id = 210";   echo "EAP-Type-Identity = \"admin\"";   echo "Message-Authenticator = 0x00"; ) | radeapclient -x 127.0.0.1 auth loopsecret | tail -1)
        if [[ "$res" =~ 'EAP-Code = Failure' ]]; then
            echo "From: $MAILFROM" > $MAIL
            echo "To: $EMAIL" >> $MAIL
            echo "Subject: [$SNACKNAME][ERR] SNACK Freeradius restart" >> $MAIL
            datenow=$(date "+%Y-%m-%d %H:%m")
            echo "[$datenow] [ERR] SNACK Freeradius restart" >> $NOTIF
            service freeradius stop
            service freeradius start
            sendmail $EMAIL < $MAIL
            #rm $MAIL
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
            echo "From: $MAILFROM" > $MAIL
            echo "To: $EMAIL" >> $MAIL
            echo "Subject: [$SNACKNAME][ERR] SNACK Mysql restart" >> $MAIL 
            service mysql stop
            DATE=`date +%Y%m%d%H%M%S`
            mv /var/log/mysql/error.log /var/log/mysql/error.log.$DATE
            cat /var/log/mysql/error.log.$DATE >> $MAIL
            service mysql start
            sendmail $EMAIL < $MAIL
            rm $MAIL
        fi
        rm -f /tmp/watchdog-mysql        
     fi
}

function check-apache {
    if [ -f /tmp/watchdog-apache ]; then
        echo "Apache en cours de redémarrage";
    else
        echo "" > /tmp/watchdog-apache
        wget --spider -T 2 -t 2 http://localhost/ -o /dev/null
        if [ $? -ne 0 ]; then
            echo "From: $MAILFROM" > $MAIL
            echo "To: $EMAIL" >> $MAIL
            echo "Subject: [$SNACKNAME][ERR] SNACK Apache2 restart" >> $MAIL 
            service apache2 stop
            DATE=`date +%Y%m%d%H%M%S`
            mv /var/log/apache2/error.log /var/log/apache2/error.log.$DATE
            mv /var/log/apache2/other_vhosts_access.log /var/log/apache2/other_vhosts_access.log.$DATE
            echo "error.log" >> $MAIL 
            cat /var/log/apache2/error.log.$DATE >> $MAIL 
            echo "other_vhosts_access.log" >> $MAIL 
            cat /var/log/apache2/other_vhosts_access.log.$DATE >> $MAIL 
            service apache2 start
            sendmail $EMAIL < $MAIL
            rm $MAIL
        fi
        rm -f /tmp/watchdog-apache
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
            echo "From: $MAILFROM" > $MAIL
            echo "To: $EMAIL" >> $MAIL
            echo "Subject: [$SNACKNAME][ERR] SNACK Space Disk /home CRITICAL" >> $MAIL
            datenow=$(date "+%Y-%m-%d %H:%m")
            echo "[$datenow] [ERR] SNACK Space Disk /home CRITICAL" >> $NOTIF
            sendmail $EMAIL < $MAIL
        fi
    fi
    if [[ $output2 =~ $re ]] ; then
        if [ $output2 -ge 90 ]; then
            echo "From: $MAILFROM" > $MAIL
            echo "To: $EMAIL" >> $MAIL
            echo "Subject: [$SNACKNAME][ERR] SNACK Space Disk / CRITICAL" >> $MAIL
            datenow=$(date "+%Y-%m-%d %H:%m")
            echo "[$datenow] [ERR] SNACK Space Disk / CRITICAL" >> $NOTIF
            sendmail $EMAIL < $MAIL
        fi
    fi
}

check-freeradius
check-mysql
check-apache
check-disk-used 
/home/snack/interface/tools/scriptProvAD.sh >> $NOTIF
sudo -u www-data  /home/snack/interface/app/Console/cake SnackSendReports