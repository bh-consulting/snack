#!/bin/bash

## FUNCTIONS

function extract_db() {
    awk -F \' "/$1.*=>.*'/ {print \$4}"\
	~snack/interface/app/Config/database.php | head -n1
}

function backup() {
    if [ $1 = restore ]
    then
    	/usr/bin/mysql -h $db_host -u $db_login -p$db_password $db_name\
		-e "$(printf "$sqline_restore" $1 ${USER_NAME//\"} $RESTORE_VALUE)"
    elif [ $1 = boot ]
    then
    	/usr/bin/mysql -h $db_host -u $db_login -p$db_password $db_name\
		-e "$(printf "$sqline" $1 ${USER_NAME//\"})"
    	/usr/bin/mysql -h $db_host -u $db_login -p$db_password $db_name\
		-e "$(printf "$sqlcloses_sessions")"
	/usr/bin/radzap -N $NAS_IP_ADDRESS 127.0.0.1 $secret
		

    else
    	/usr/bin/mysql -h $db_host -u $db_login -p$db_password $db_name\
		-e "$(printf "$sqline" $1 ${USER_NAME//\"})"
    fi

    /usr/bin/snmpset -t 5 -c private -v 2c $NAS_IP_ADDRESS\
	$oid_writeNet.$ip_address s $NAS_IP_ADDRESS

}

## VARIABLES

oid_writeNet=iso.3.6.1.4.1.9.2.1.55

ip_address=$(awk -F \' '/ipAddress/ {print $4}'\
    ~snack/interface/app/Config/parameters.php)

db_login=$(extract_db login)
db_password=$(extract_db password)
db_name=$(extract_db database)
db_host=$(extract_db host)
db_prefix=$(extract_db prefix)

secret=loopsecret

read sqline <<SQL
    INSERT INTO\\
    ${db_prefix}backups(datetime, nas, action, users)\\
    VALUES(NOW(), '$NAS_IP_ADDRESS', '%s', '%s')\\
SQL

read sqline_restore <<SQL
    INSERT INTO\\
    ${db_prefix}backups(datetime, nas, action, users,restore)\\
    VALUES(NOW(), '$NAS_IP_ADDRESS', '%s', '%s', '%s')\\
SQL

read sqlcloses_sessions <<SQL
    UPDATE radacct\\
    SET ACCTSTOPTIME=(\\
	SELECT datetime FROM backups\\
	WHERE action='reload'\\
	ORDER BY datetime DESC\\
	LIMIT 1)\\
    WHERE acctstoptime IS NULL\\
SQL


## PROGRAM

case "$ACCT_STATUS_TYPE" in
    Interim-Update|Start)
	backup login
    ;;

    Stop)
	backup logoff
    ;;

    Write)
	backup wrmem
    ;;

    Restore)
	backup restore
    ;;
    Reload)
	backup boot
    ;;
esac

exit 0
