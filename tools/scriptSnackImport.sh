#!/bin/bash
#Author Guillaume Roche <groche@guigeek.org>
#
HOME_SNACK=/home/snack
DATABASEFILE=$HOME_SNACK/interface/app/Config/database.php
DB="radius"
TEMP=/tmp
LOG=/tmp/log-import
force=0
usage()
{
    echo -en "Usage:\t$1 [-d|--decrypt key] [--force] FILE\n\nContact: <groche@guigeek.org>\n"
}

restore()
{
    #Decompress/Decrypt
    if [ -z $key ]; then
	tar zxvf $FILE -C $TEMP > $LOG
    else
        dd if=$FILE | openssl des3 -d -k $key | tar xvzf - -C $TEMP
    fi
    DIR=`more $LOG | tail -1 | cut -d"/" -f1`
    LOGIN=`grep login $DATABASEFILE | head -n 1 | cut -d'>' -f2 | cut -d"'" -f2`
    PASS=`grep password $DATABASEFILE | head -n 1 | cut -d'>' -f2 | cut -d"'" -f2`
    VER1=$(cat /home/snack/interface/app/VERSION.txt)
    VER2=$(cat $TEMP/$DIR/VERSION.txt)
    if [ $force == 0 ]; then
        if [ "$VER1" == "$VER2" ]; then
            rsync --stats -avr --exclude="radius.sql" $TEMP/$DIR/snack /home >> $LOG
            echo "RSYNC RES :$?" >> $LOG 
            mysql -u$LOGIN -p$PASS $DB < $TEMP/$DIR/radius.sql
            echo "MYSQL RES :$?" >> $LOG 
            sed \
                 -e "s/\('password' =>\) '.*'/\1 '${PASS}'/"\
                 -i $HOME_SNACK/interface/app/Config/database.php
        else
            echo "VERSIONS MISMATCH" >> $LOG
        fi
    else
        rsync --stats -avr --exclude="radius.sql" $TEMP/$DIR/snack /home >> $LOG
        echo "RSYNC RES :$?" >> $LOG 
        mysql -u$LOGIN -p$PASS $DB < $TEMP/$DIR/radius.sql
        echo "MYSQL RES :$?" >> $LOG 
        sed \
             -e "s/\('password' =>\) '.*'/\1 '${PASS}'/"\
             -i $HOME_SNACK/interface/app/Config/database.php
    fi
}

options=$(getopt -o hd: -l help,force,file:,decrypt: -- "$@")
if [ $? -ne 0 ]; then
    usage $(basename $0)
    exit 1
fi
eval set -- "$options"

while [ $# -gt 0 ]
do
    case "$1" in
        -h|--help)      usage $0 && exit 0;;
        -d|--decrypt)   key=$2; shift 2;;
        --file)         FILE=$2; shift 2;;
        --force)        force=1; shift 2;;
        --)             shift; break ;;
        -*)             echo "$0: error - unrecognized option $1" 1>&2; exit 1;;
        *)              usage $0 && exit 0;;
    esac
done

if [ -z $FILE ]; then
    usage
    exit
fi
restore

rm -rf $TEMP/$DIR
