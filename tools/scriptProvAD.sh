#!/bin/bash

USER_HOME=/home/snack

DATABASEFILE=$USER_HOME/interface/app/Config/database.php
PASS=`grep password $DATABASEFILE | head -n 1 | cut -d'>' -f2 | cut -d"'" -f2`

ADIP=`grep adip /home/snack/interface/app/Config/parameters.php | cut -d"'" -f4`
ADMINUSERNAME=`grep adminusername /home/snack/interface/app/Config/parameters.php | cut -d"'" -f4`
ADMINPASSWORD=`grep adminpassword /home/snack/interface/app/Config/parameters.php | cut -d"'" -f4`
ADGROUPSYNC=`grep adgroupsync /home/snack/interface/app/Config/parameters.php | cut -d"'" -f4`

RES=`wbinfo --group-info=$ADGROUPSYNC | cut -d":" -f4`
arr=$(echo $RES | tr "," "\n")

for user in $arr
do
    #echo $ligne
    #if [[ $ligne =~ $REGEX ]]; then
    #    name=`echo -n ${BASH_REMATCH[1]} | tr -d '\r'`
    users=`mysql -uradius -p${PASS} radius -N -B -e "select username from raduser where username='$user' and is_windowsad=1;" | awk -F " " '{ print $1 }'`
    if [[ $users == "" ]]; then
        echo "insert $user"
        mysql -uradius -p${PASS} radius -N -B -e "insert into raduser(username, role, comment, is_windowsad) values ('$user','user','', 1);"
    fi
done

users=`mysql -uradius -p${PASS} radius -N -B -e "select username from raduser where is_windowsad=1;"`

for user in $users
do
    #echo $user
    found=0
    for user2 in $arr
    do
        if [[ $user == $user2 ]]; then
            found=1 
        fi
    done
    if [[ $found == 0 ]]; then
        echo "$user not found"
        echo "delete $user"
        # Delete User
        mysql -uradius -p${PASS} radius -N -B -e "delete from raduser where username='$user';"
        mysql -uradius -p${PASS} radius -N -B -e "delete from radcheck where username='$user';"
        mysql -uradius -p${PASS} radius -N -B -e "delete from radreply where username='$user';"
        mysql -uradius -p${PASS} radius -N -B -e "delete from radusergroup where username='$user';"
    fi
done
