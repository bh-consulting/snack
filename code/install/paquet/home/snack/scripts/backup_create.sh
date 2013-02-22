#!/bin/bash

oid_writeNet=iso.3.6.1.4.1.9.2.1.55

read sqline <<SQL
    INSERT INTO\\
    backups(datetime, nas, action, users)\\
    VALUES(NOW(), '$NAS_IP_ADDRESS', '%s', '%s')\\
SQL

function backup() {
    /usr/bin/mysql -uradius -pradiusroxx radius\
	-e "$(printf "$sqline" $1 ${USER_NAME//\"})"

    /usr/bin/snmpset -t 5 -c private -v 2c $NAS_IP_ADDRESS\
	$oid_writeNet.192.168.1.146 s $NAS_IP_ADDRESS
}

case "$ACCT_STATUS_TYPE" in
    Start)
	backup login
    ;;

    Stop)
	backup logoff
    ;;

    Write)
	backup wrmem
    ;;
esac

exit 0
