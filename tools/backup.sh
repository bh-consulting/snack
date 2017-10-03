#!/bin/bash
# Maintainer Guillaume Roche groche@guigeek.org
#
SCRPATH=/home/snack/scripts
GITPATH=/home/snack/backups.git


usage() {
    echo -en "Usage:\t$1 host login password enablepassword\nContact: <groche@guigeek.org>\n"
}

backup() { 
    res=-1
    host=$1
    times=$2
    backuptype=0
    res=$(nc -vnz -w 5 $host 22 >/dev/null 2>/dev/null; echo $?)
    if [[ "$res" == "0" ]]; then
        backuptype="ssh"
        $SCRPATH/ssh.expect $host $login $pass $enablepassword "show run" | tail -n +3 > /tmp/$host
        #tail -n +3 /tmp/$host > $GITPATH/$host
    else
        res=$(nc -vnz -w 5 $host 23 >/dev/null 2>/dev/null; echo $?)
        if [[ "$res" == "0" ]]; then
            backuptype="telnet"
            $SCRPATH/telnet.pl $host $login $pass $enablepassword "show run" > /tmp/$host
        fi
    fi
    sed '/closed by remote host/d' -i /tmp/$host
    #chown -R snack:snack $GITPATH
    #chmod -R 770 $GITPATH
    if [[ "$res" == "0" ]]; then
	sed -i '$ d' /tmp/$host
        result=$( sed -e '/^\s*$/d' /tmp/$host | grep "^[^c|^C]" | tail -1)
        #echo $result
        if [[ $result =~ "end" || $result =~ "exit" ]]; then
	    mv /tmp/$host $GITPATH/$host
            cd $GITPATH
            sed -i '/^! [Last|NVRAM]/d' $host
            /usr/bin/git add $host
            /usr/bin/git commit -m AUTO-COMMIT $host
            commit=$(/usr/bin/git log --pretty=oneline -1 HEAD | cut -d\  -f1)
            echo "result=success"
            echo "commit=$commit"
            echo "backuptype=$backuptype"
        else
            if [[ $times -gt 0 ]]; then
                times=$(( $times - 1 ))
                backup $host $times
            else
                echo "result=error"
            fi
        fi
    else
        echo "result=error"
    fi
}

times=3
host=$1
login=$2
pass=$3
enablepassword=$4
backup $host $times
