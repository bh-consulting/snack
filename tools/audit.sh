#!/bin/bash
# Maintainer Guillaume Roche groche@guigeek.org
#
SCRPATH=/home/snack/interface/tools

usage() {
    echo -en "Usage:\t$1 host login password enablepassword\nContact: <groche@guigeek.org>\n"
}

audit() { 
    res=-1
    host=$1
    times=$2
    audittype=0
    res=$(nc -vnz -w 5 $host 22 >/dev/null 2>/dev/null; echo $?)
    if [[ "$res" == "0" ]]; then
        audittype="ssh"
        $SCRPATH/audit-ssh.expect $host $login $pass $enablepassword
        resaudit=$?
    else
        res=$(nc -vnz -w 5 $host 23 >/dev/null 2>/dev/null; echo $?)
        if [[ "$res" == "0" ]]; then
            audittype="telnet"
            $SCRPATH/audit-telnet.pl $host $login $pass $enablepassword
            resaudit=$?
        fi
    fi
    echo $resaudit
    if [[ "$resaudit" != "0" ]]; then
	    if [[ $times -gt 0 ]]; then
            times=$(( $times - 1 ))
            audit $host $times
        else
             echo "result=error"
        fi
    else
        echo "result=success;$audittype"
    fi
}

times=3
host=$1
login=$2
pass=$3
enablepassword=$4
audit $host $times
