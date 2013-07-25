#!/bin/bash

PAQUETNAME=snack
VERSION=1.0
DATABASE=radius

whiptail \
	--title "SNACK ${VERSION}" \
	--yes-button "Start" \
	--no-button "Cancel" \
	--yesno "\nWelcome !\n\nYou are upgrading SNACK.\n\nAll errors are logged in /tmp/snack_upgrade_errors.log\nStdout are logged in /tmp/snack_upgrade_out.log" \
	15 70

if [ $? != 0 ]; then
	whiptail \
		--title "SNACK ${VERSION}" \
		--msgbox "\n\nUser has canceled the installation!" \
		10 70
	exit 1
fi

PASSWORD_DB_ROOT=$(whiptail \
	--title "SNACK ${VERSION}" \
	--passwordbox "\n\nWhat is the MySQL password for 'ROOT' user?" \
	10 70 3>&1 1>&2 2>&3)

if [ $? != 0 ]; then
	whiptail \
		--title "SNACK ${VERSION}" \
		--msgbox "\n\nUser has canceled the installation!" \
		10 70
	exit 1
fi

RES=`echo "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE table_name = 'raduser' AND TABLE_SCHEMA = 'radius' AND COLUMN_NAME = 'is_phone';" | mysql -u root --password=$PASSWORD_DB_ROOT | tail -1`
echo $RES
if [ "$RES" -ne "1" ]; then
    echo 20 | whiptail \
	--title "SNACK ${VERSION}" \
	--gauge "\n\n Modifying Mysql for Radius..." 10 70 0    
fi
mysql -u root -p $DATABASE --password=$PASSWORD_DB_ROOT -e "ALTER TABLE raduser add is_phone boolean default '0';"
if [ "$RES" -ne "1" ]; then
    echo 100 | whiptail \
	--title "SNACK ${VERSION}" \
	--gauge "\n\n Modifying Mysql for Radius..." 10 70 0    
fi
if [ "$RES" -ne "1" ]; then
    echo 20 | whiptail \
	--title "SNACK ${VERSION}" \
	--gauge "\n\n Copying new files of web interface..." 10 70 0    
fi
rsync -avr --exclude 'Config' ../interface/app /home/snack/interface
if [ "$RES" -ne "1" ]; then
    echo 40 | whiptail \
	--title "SNACK ${VERSION}" \
	--gauge "\n\n Copying new files of web interface..." 10 70 0    
fi
chown -R root:snack /home/snack/interface/app
if [ "$RES" -ne "1" ]; then
    echo 60 | whiptail \
	--title "SNACK ${VERSION}" \
	--gauge "\n\n Copying new files of web interface..." 10 70 0    
fi
chmod 777 -R /home/snack/interface/app/tmp
if [ "$RES" -ne "1" ]; then
    echo 80 | whiptail \
	--title "SNACK ${VERSION}" \
	--gauge "\n\n Copying new files of web interface..." 10 70 0    
fi
chown www-data:www-data /home/snack/interface/app/Config/parameters.php
if [ "$RES" -ne "1" ]; then
    echo 100 | whiptail \
	--title "SNACK ${VERSION}" \
	--gauge "\n\n Copying new files of web interface..." 10 70 0    
fi
whiptail \
	--title "SNACK ${VERSION}" \
	--ok-button "Finish" \
	--msgbox "\n\nUpgrade done!" \
	10 70

exit 0
