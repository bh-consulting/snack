#!/bin/bash
# Author Guillaume Roche <groche@guigeek.org>
#
USER_HOME=/home/snack
DATABASEFILE=$USER_HOME/interface/app/Config/database.php
PASS=`grep password $DATABASEFILE | head -n 1 | cut -d'>' -f2 | cut -d"'" -f2`
datenow=$(date "+%Y-%m-%d %H:%m")

# Convert NAS-Port-Type
users=$(mysql -uradius -p${PASS} radius -N -B -e "select username from raduser;" | awk -F " " '{ print $1 }')
for user in $users
do
	echo $user
	value=$(mysql -uradius -p${PASS} radius -N -B -e "select value from radcheck where username='$user' and attribute='NAS-Port-Type';")
	if [[ $value =~ "|" ]]; then
		array=(${value//|/ })
		for i in "${!array[@]}"
		do
			if [[ $i == 0 ]]; then
				#echo "update radcheck set value='${array[i]}' where username='$user' and attribute='NAS-Port-Type';"
				mysql -uradius -p${PASS} radius -N -B -e "update radcheck set value='${array[i]}' where username='$user' and attribute='NAS-Port-Type';"
				#echo "update radcheck set op='+=' where username='$user' and attribute='NAS-Port-Type';"
				mysql -uradius -p${PASS} radius -N -B -e "update radcheck set op='+=' where username='$user' and attribute='NAS-Port-Type';"
			else
				#echo "insert into radcheck(username, attribute, op, value) values ('$user', 'NAS-Port-Type', '+=', '${array[i]}');"
				mysql -uradius -p${PASS} radius -N -B -e "insert into radcheck(username, attribute, op, value) values ('$user', 'NAS-Port-Type', '+=', '${array[i]}');"
			fi
		done
	fi	
done

# Convert EAP-Type
users=$(mysql -uradius -p${PASS} radius -N -B -e "select username from radcheck where attribute='EAP-Type' and value='EAP-TLS';")
for user in $users
do
	echo "Convert EAP-Type for $user"
	mysql -uradius -p${PASS} radius -N -B -e "update radcheck set value='TLS' where username='$user' and attribute='EAP-Type' and value='EAP-TLS';"
done

# Change path for scripts path
sed -e "s/'scriptsPath' => '\/home\/snack\/scripts',/'scriptsPath' => '\/home\/snack\/interface\/tools',/" -i /home/snack/interface/app/Config/parameters.php