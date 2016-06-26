#!/bin/bash
# Author Guillaume Roche <groche@guigeek.org>
#

HOME_SNACK=/home/snack
DATABASEFILE=$HOME_SNACK/interface/app/Config/database.php
DBPASS=$(grep password $DATABASEFILE | head -n 1 | cut -d'>' -f2 | cut -d"'" -f2)
COREFILE=$HOME_SNACK/interface/app/Config/core.php
DB="radius"

usage()
{
    echo -en "Usage:\t$1 password\n\nContact: <groche@guigeek.org>\n"
}

if [ $# -ne 1 ]; then
    usage $(basename $0)
    exit 1
fi

pass=$1
key=$(grep "Security.salt" $COREFILE | cut -d"'" -f4)
cryptpass=$(echo -n "$key${pass}" | sha1sum | awk '{print $1}')
mysql -uradius -p${DBPASS} radius -N -B -e "update snackuser set password='$cryptpass' where username='admin';"