#!/bin/bash

echo toto > /tmp/mysql

read sqline <<SQL
    INSERT INTO\\
    backups(datetime, nas, action, users)\\
    VALUES(NOW(), '$NAS_IP_ADDRESS', '%s', '$USER_NAME')\\
SQL

function backup() {
    echo /usr/bin/mysql -uradius -pradiusroxx radius -e "$(printf "$sqline" $1)"
    /usr/bin/mysql -uradius -pradiusroxx radius -e "$(printf "$sqline" $1)"

    /usr/bin/snmpset -t 5 -c private -v 2c\
	$NAS_IP_ADDRESS 1.3.6.1.4.1.9.2.1.55.192.168.1.146 s $NAS_IP_ADDRESS
}

case "$ACCT_STATUS_TYPE" in
    Start)
	backup login
    ;;

    Stop)
	backup logoff
    ;;
esac

exit 0
