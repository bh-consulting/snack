#!/bin/bash
#Author Guillaume Roche <groche@guigeek.org>
#

WEEKN=`date +%Y-%W`
HOMESNACK=/home/snack

mv $HOMESNACK/logs/snacklog $HOMESNACK/logs/snacklog-$WEEKN
service syslog-ng restart
#WEEKTOARCHIVE=$((WEEKN-4))
#if [ -f $HOMESNACK/logs/snacklog-$WEEKTOARCHIVE ]; then
#    cd $HOMESNACK/logs && tar jcvf snacklog-$WEEKTOARCHIVE.tar.bz2 snacklog-$WEEKTOARCHIVE
#    rm $HOMESNACK/logs/snacklog-$WEEKTOARCHIVE
#fi
