#!/bin/bash
# Author Guillaume Roche <groche@guigeek.org>
#
HOME_SNACK=/home/snack
DATABASEFILE=$HOME_SNACK/interface/app/Config/database.php
DB="radius"
NAME=snack-conf_`grep Issuer $HOME_SNACK/cert/cacert.pem  | cut -d"=" -f5`_`TZ='Europe/Paris' date "+%Y-%m-%d_%H-%M"`
#NAME=snack-conf-`TZ='Europe/Paris' date "+%Y-%m-%d_%H-%M"`
LOG=/tmp/log-export
RES=0
usage()
{
    echo -en "Usage:\t$1 [-l|--log] [-e|--encrypt key]\n\nContact: <groche@guigeek.org>\n"
}

backup()
{
    mkdir /tmp/$NAME
    echo "" > $LOG
    rsync -avr --include="snack/" --include="snack/backups.git/" --include="snack/backups.git/**"  --include="snack/cert/" --include="snack/cert/**" --include="snack/interface/" --include="snack/interface/app/" --include="snack/interface/app/Config/" --include="snack/interface/app/Config/*"  --exclude="*" $HOME_SNACK /tmp/$NAME >> $LOG 2>> $LOG
    if [ $? != 0 ]; then
        RES=1
    else
        RES=0
    fi
    LOGIN=`grep login $DATABASEFILE | head -n 1 | cut -d'>' -f2 | cut -d"'" -f2`
    PASS=`grep password $DATABASEFILE | head -n 1 | cut -d'>' -f2 | cut -d"'" -f2`
    #mysqldump -u $LOGIN --password=$PASS $DB > $NAME/radius.sql
    if [ -z $log ]; then
        mysqldump -u$LOGIN -p$PASS $DB --ignore-table=$DB.logs > /tmp/$NAME/radius.sql
    else
        mysqldump -u$LOGIN -p$PASS $DB > /tmp/$NAME/radius.sql
    fi
    if [ $? != 0 ]; then
        RES=2
    fi
    if [ -z $key ]; then
        cd /tmp && tar cvzf $NAME.tar.gz $NAME >> $LOG 2>> $LOG
        chown www-data:root $NAME.tar.gz
    else
        cd /tmp && tar cvzf - $NAME | openssl des3 -salt -k $key | dd of=$NAME.tar.gz.gpg >> $LOG 2>> $LOG
        chown www-data:root $NAME.tar.gz.gpg
    fi    
    # Decompress/Decrypt dd if=snack-conf-20140304.tar.gz.gpg |openssl des3 -d -k testtest |tar xvzf -
    rm -rf /tmp/$NAME
}

options=$(getopt -o hle: -l help,log,encrypt: -- "$@")
if [ $? -ne 0 ]; then
    usage $(basename $0)
    exit 1
fi
eval set -- "$options"

while [ $# -gt 0 ]
do
    case "$1" in
        -h|--help)      usage $0 && exit 0;;
        -l|--log)       log=1; shift ;;
        -e|--encrypt)   key=$2; shift 2;;
        --)             shift; break ;;
        -*)             echo "$0: error - unrecognized option $1" 1>&2; exit 1;;
        *)              usage $0 && exit 0;;
    esac
done
backup
if [ $RES != 0 ]; then
    rm -rf /tmp/$NAME.*
    exit 1
fi
mv /tmp/$NAME.* $HOME_SNACK/interface/app/webroot/conf
rm -rf /tmp/$NAME.*
echo $NAME.tar.gz
exit 0
