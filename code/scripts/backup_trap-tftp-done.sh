#!/bin/bash

read NAS_IP_ADDRESS
for i in {1..3}; do read void; done
read OID_SNMP
read OID_RUNNING
read OID_NETWORKTFTP

read sqline <<SQL
    UPDATE backups\\
    SET commit='%s'\\
    WHERE nas='$NAS_IP_ADDRESS'\\
    AND commit IS NULL\\
SQL

if [[\
    "$OID_SNMP" =~ iso.3.6.1.4.1.9.9.43.1.1.6.1.3.[0-9]+\ 2 \
    && "$OID_RUNNING" =~ iso.3.6.1.4.1.9.9.43.1.1.6.1.4.[0-9]+\ 3 \
    && "$OID_NETWORKTFTP" =~ iso.3.6.1.4.1.9.9.43.1.1.6.1.5.[0-9]+\ 6 \
]]; then

    cd ~snack/backups-clone.git
    /usr/bin/git add $NAS_IP_ADDRESS
    /usr/bin/git commit -m AUTO-COMMIT $NAS_IP_ADDRESS

    commit=$(/usr/bin/git log --pretty=oneline -1 HEAD | cut -d' ' -f1)
    /usr/bin/mysql -uradius -pradiusroxx radius -e "$(printf "$sqline" $commit)" &> /tmp/comsql
fi

exit 0
