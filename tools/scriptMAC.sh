#!/bin/bash
USER=bhc
BACK=10.254.20.254
FILE=mac.txt

ssh $USER@$BACK show ip arp > $FILE

while read ligne
do
    IP=`echo $ligne | cut -d" " -f2`
    MAC=`echo $ligne | cut -d" " -f4`
    regexip='^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$'
    regexmac='^[0-9a-f]{4}.[0-9a-f]{4}.[0-9a-f]{4}$'
    if [[ $IP =~ $regexip ]]; then
        if [[ $MAC =~ $regexmac ]]; then
            #echo "$IP $MAC"
	    MAC=`echo $MAC | cut -d"." -f1``echo $MAC | cut -d"." -f2``echo $MAC | cut -d"." -f3`
	    NAME=`host $IP | cut -d" " -f5`
	    if [[ "$NAME" != "record" ]]; then
		echo "$IP $MAC $NAME"
		echo "Raduser,$MAC,user,,,,1,\"$NAME\""
	    else
		echo "$IP $MAC"
		echo "Raduser,$MAC,user,,,,1,"
	    fi
	    echo "Radcheck,$MAC,NAS-Port-Type,=~,Ethernet|Wireless-802.11"
	    echo "Radcheck,$MAC,Cleartext-Password,:=,$MAC"
	    echo "Radcheck,$MAC,EAP-Type,:=,MD5-CHALLENGE"
        fi
    fi
done < $FILE


