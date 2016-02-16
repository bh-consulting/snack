#!/bin/bash
LOGIN=
PASSWORD=
ENABLEPASSWORD=
GW=10.254.20.254
FILE=mac.txt
TMPMACFILE=/tmp/mac.out
TMPROUTEFILE=/tmp/route.out
LOGFILE=/tmp/mac.log
HOME=/home/snack
DATABASEFILE=$HOME/interface/app/Config/database.php
PASS=`grep password $DATABASEFILE | head -n 1 | cut -d'>' -f2 | cut -d"'" -f2`

usage()
{
    echo -e "Usage:\t$1 -v vlan"
    echo -e "\tvlan: must be a number (for all vlans type 0)"
    echo -e "Contact: <groche@guigeek.org>"
}

scan()
{
    echo "" > $LOGFILE
    $HOME/interface/tools/command.sh $GW arp $LOGIN $PASSWORD $ENABLEPASSWORD > $TMPMACFILE
    $HOME/interface/tools/command.sh $GW route $LOGIN $PASSWORD $ENABLEPASSWORD > $TMPROUTEFILE
    while read ligne
        do
            REGEX="Internet\s+([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})\s+-*[0-9]*\s+([0-9a-f]{4}\.[0-9a-f]{4}\.[0-9a-f]{4})\s+ARPA\s+Vlan([0-9]+)"
            if [[ $ligne =~ $REGEX ]]; then
                ip="${BASH_REMATCH[1]}"
                mac="${BASH_REMATCH[2]}"
                macaddr=""
                vlan="${BASH_REMATCH[3]}"
                REGEX2="([0-9a-f]{4})\.([0-9a-f]{4})\.([0-9a-f]{4})"
                if [[ $mac =~ $REGEX2 ]]; then
                    macaddr="${BASH_REMATCH[1]}${BASH_REMATCH[2]}${BASH_REMATCH[3]}"
                else
                    echo "err : $mac"
                fi
                name=$(host $ip | cut -d" " -f5)
                if [[ $name =~ "NXDOMAIN" ]]; then
                    name=""
                fi
                 if [[ $name =~ "record" ]]; then
                    name=""
                fi
                grep "$ip/32 is directly connected" $TMPROUTEFILE > /dev/null
                if [[ $? -ne 0 ]]; then
                    if [[ $vlan -eq $vl ]]; then
                        user=`mysql -uradius -p${PASS} radius -N -B -e "select username from raduser where username='$macaddr';" | awk -F " " '{ print $1 }'`
                        if [[ $user != $macaddr ]]; then
                            echo "$ip $macaddr $vlan $name" >> $LOGFILE
                            mysql -uradius -p${PASS} radius -N -B -e "insert into raduser(username, role, comment, is_mac) values ('$macaddr','user','', 1);"
                            mysql -uradius -p${PASS} radius -N -B -e "insert into radcheck(username, attribute, op, value) values ('$macaddr', 'Cleartext-Password', ':=', '$mac');"
                            mysql -uradius -p${PASS} radius -N -B -e "insert into radcheck(username, attribute, op, value) values ('$macaddr', 'NAS-Port-Type', '=~', 'Ethernet|Wireless-802.11');"
                            mysql -uradius -p${PASS} radius -N -B -e "insert into radcheck(username, attribute, op, value) values ('$macaddr', 'EAP-Type', ':=', 'MD5-CHALLENGE');"
                            mysql -uradius -p${PASS} radius -N -B -e "insert into radreply(username, attribute, op, value) values ('$macaddr', 'Tunnel-Type', ':=', 'VLAN');"
                            mysql -uradius -p${PASS} radius -N -B -e "insert into radreply(username, attribute, op, value) values ('$macaddr', 'Tunnel-Medium-Type', ':=', 'IEEE-802');"
                            mysql -uradius -p${PASS} radius -N -B -e "insert into radreply(username, attribute, op, value) values ('$macaddr', 'Tunnel-Private-Group-Id', ':=', '$vlan');"
                        fi
                    elif [[ $vl -eq 0 ]]; then
                        echo "$ip $macaddr $vlan $name" >> $LOGFILE
                    fi
                fi
            fi
    done < $TMPMACFILE
}

clean()
{
    rm $TMPMACFILE
    rm $TMPROUTEFILE
    rm $LOGFILE
}

options=$(getopt -o hv: -l help,vlan: -- "$@")
if [ $# -eq 0 ]; then
    usage $(basename $0)
    exit 1
fi
eval set -- "$options"

while [ $# -gt 0 ]
do
    case "$1" in
        -h|--help)      usage $0 && exit 0;;
        -v|--vlan)      vl=$2;scan; break;;
        --)             shift; break ;;
        -*)             echo "$0: error - unrecognized option $1" 1>&2; exit 1;;
        *)              usage $0 && exit 0;;
    esac
done

#clean