#!/bin/bash
# Maintainer Guillaume Roche groche@guigeek.org
#
SCRPATH=/home/snack/scripts

usage() {
    echo -en "Usage:\t$1 host info login password enablepassword\nContact: <groche@guigeek.org>\n"
}

command() {
    res=-1
    backuptype=0
    res=$(nc -vnz -w 5 $host 22 >/dev/null 2>/dev/null; echo $?)
    if [[ "$res" == "0" ]]; then
        if [[ "$info" == "model" ]]; then
            $SCRPATH/ssh.expect $host $login $pass $enablepassword "show ver" | grep "Model number" | cut -d":" -f2 | cut -d" " -f2 | tail -1
        fi
        if [[ "$info" == "version" ]]; then
            $SCRPATH/ssh.expect $host $login $pass $enablepassword "show ver" | grep "Cisco IOS Software" | cut -d " " -f8 | cut -d"," -f1
        fi
        if [[ "$info" == "image" ]]; then
            $SCRPATH/ssh.expect $host $login $pass $enablepassword "show ver" | grep "Cisco IOS Software" | cut -d "(" -f2 | cut -d")" -f1
        fi
        if [[ "$info" == "serial" ]]; then
            $SCRPATH/ssh.expect $host $login $pass $enablepassword "show ver" | grep "Motherboard serial number" | cut -d":" -f2 | cut -d" " -f2 | tail -1
        fi
    else
        res=$(nc -vnz -w 5 $host 23 >/dev/null 2>/dev/null; echo $?)
        if [[ "$res" == "0" ]]; then
            if [[ "$info" == "model" ]]; then
                $SCRPATH/telnet.pl $host $login $pass $enablepassword "show ver" | grep "Model number" | cut -d":" -f2 | cut -d" " -f2 | tail -1
            fi
            if [[ "$info" == "version" ]]; then
                $SCRPATH/telnet.pl $host $login $pass $enablepassword "show ver" | grep "Cisco IOS Software" | cut -d " " -f8 | cut -d"," -f1
            fi
            if [[ "$info" == "image" ]]; then
                $SCRPATH/telnet.pl $host $login $pass $enablepassword "show ver" | grep "Cisco IOS Software" | cut -d "(" -f2 | cut -d")" -f1
            fi
            if [[ "$info" == "serial" ]]; then
                $SCRPATH/telnet.pl $host $login $pass $enablepassword "show ver" | grep "Motherboard serial number" | cut -d":" -f2 | cut -d" " -f2 | tail -1
            fi
        fi
    fi
}

times=3
host=$1
info=$2
login=$3
pass=$4
enablepassword=$5
command
