#!/bin/bash

echo laaa > /tmp/mardi

## FUNCTIONS

function extract_db() {
    awk -F \' "/$1.*=>.*'/ {print \$4}"\
	~snack/interface/app/Config/database.php | head -n1
}

## VARIABLES

oid_ccmHistoryEventCommandSource=iso.3.6.1.4.1.9.9.43.1.1.6.1.3
oid_ccmHistoryEventConfigSource=iso.3.6.1.4.1.9.9.43.1.1.6.1.4
oid_ccmHistoryEventConfigDestination=iso.3.6.1.4.1.9.9.43.1.1.6.1.5

db_login=$(extract_db login)
db_password=$(extract_db password)
db_name=$(extract_db database)
db_host=$(extract_db host)
db_prefix=$(extract_db prefix)

read NAS_IP_ADDRESS
for i in {1..3}; do read void; done
read ccmHistoryEventCommandSource
read ccmHistoryEventConfigSource
read ccmHistoryEventConfigDestination

read sql_tftpdone <<SQL
    UPDATE ${db_prefix}backups\\
    SET commit='%s'\\
    WHERE commit IS NULL\\
    AND nas='$NAS_IP_ADDRESS'\\
    ORDER BY id\\
    LIMIT 1\\
SQL

read sql_sessionusers <<SQL
    SELECT DISTINCT username\\
    FROM ${db_prefix}radacct\\
    WHERE acctstoptime IS NULL\\
    ORDER BY radacctid\\
SQL

## PROGRAM

# Trap write mem event
if [[\
    "$ccmHistoryEventCommandSource" =~ $oid_ccmHistoryEventCommandSource.[0-9]+\ 1 \
    && "$ccmHistoryEventConfigSource" =~ $oid_ccmHistoryEventConfigSource.[0-9]+\ 3 \
    && "$ccmHistoryEventConfigDestination" =~ $oid_ccmHistoryEventConfigDestination.[0-9]+\ 4 \
]]; then

    users=$(/usr/bin/mysql -B -h $db_host -u $db_login -p$db_password $db_name\
	-e "$sql_sessionusers" | tail -n+2  | paste -sd ,)

    export NAS_IP_ADDRESS
    export USER_NAME=$users
    export ACCT_STATUS_TYPE=Write

    ~snack/scripts/backup_create.sh

# Trap tftp done
elif [[\
    "$ccmHistoryEventCommandSource" =~ $oid_ccmHistoryEventCommandSource.[0-9]+\ 2 \
    && "$ccmHistoryEventConfigSource" =~ $oid_ccmHistoryEventConfigSource.[0-9]+\ 3 \
    && "$ccmHistoryEventConfigDestination" =~ $oid_ccmHistoryEventConfigDestination.[0-9]+\ 6 \
]]; then

    cd ~snack/backups.git/

    /usr/bin/git add $NAS_IP_ADDRESS
    /usr/bin/git commit -m AUTO-COMMIT $NAS_IP_ADDRESS

    commit=$(/usr/bin/git log --pretty=oneline -1 HEAD | cut -d\  -f1)

    /usr/bin/mysql -h $db_host -u $db_login -p$db_password $db_name\
	-e "$(printf "$sql_tftpdone" $commit)"
fi

exit 0
