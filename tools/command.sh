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
        if [[ "$info" == "aaasrv" ]]; then
            $SCRPATH/ssh.expect $host $login $pass $enablepassword "show aaa servers" | grep "RADIUS"
        fi
        if [[ "$info" == "testaaasrv" ]]; then
            $SCRPATH/ssh.expect $host $login $pass $enablepassword "$cmd" | tail -1
        fi
        if [[ "$info" == "clock" ]]; then
            $SCRPATH/ssh.expect $host $login $pass $enablepassword "show clock" | tail -1
        fi
        if [[ "$info" == "cdp" ]]; then
            $SCRPATH/ssh.expect $host $login $pass $enablepassword "show cdp neigh det"
        fi
        if [[ "$info" == "hostname" ]]; then
            $SCRPATH/ssh.expect $host $login $pass $enablepassword "show ver" | grep "uptime" | cut -d" " -f1 2> /dev/null
        fi
        if [[ "$info" == "mac" ]]; then
            $SCRPATH/ssh.expect $host $login $pass $enablepassword "show mac address-table" | grep $cmd
        fi
        if [[ "$info" == "arp" ]]; then
            $SCRPATH/ssh.expect $host $login $pass $enablepassword "show ip arp"
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
            if [[ "$info" == "aaasrv" ]]; then
                $SCRPATH/telnet.pl $host $login $pass $enablepassword "show aaa servers" | grep "RADIUS"
            fi
            if [[ "$info" == "testaaasrv" ]]; then
                $SCRPATH/telnet.pl $host $login $pass $enablepassword "$cmd" | tail -1
            fi
            if [[ "$info" == "clock" ]]; then
                $SCRPATH/telnet.pl $host $login $pass $enablepassword "show clock" | tail -1
            fi
            if [[ "$info" == "cdp" ]]; then
                $SCRPATH/telnet.pl $host $login $pass $enablepassword "show cdp neigh det"
            fi
            if [[ "$info" == "hostname" ]]; then
                $SCRPATH/telnet.pl $host $login $pass $enablepassword "show ver" | grep "uptime" | cut -d" " -f1 2> /dev/null
            fi
            if [[ "$info" == "mac" ]]; then
                $SCRPATH/telnet.pl $host $login $pass $enablepassword "show mac address-table" | grep $cmd
            fi
            if [[ "$info" == "arp" ]]; then
                $SCRPATH/telnet.pl $host $login $pass $enablepassword "show ip arp"
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
cmd="$6"
command
