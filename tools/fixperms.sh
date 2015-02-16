#!/bin/bash
# fixperms.sh
OUT=/tmp/snack_out.log
LOG=/tmp/snack_errors.log
USER_HOME=/home/snack
DEST_PATH=$USER_HOME/cert

HOME_MODE=0070
ONLY_RADIUS_ACCESS=0700
INTERFACE_ACCESS=0770
INTERFACE_THROUGH=0710
READ_ONLY=0440
ONLY_INTERFACE_ACCESS=0700

INTERFACE_USER=www-data
RADIUS_USER=freerad

CA_KEY=cakey.pem
CA_REQ=careq.pem
CA_CERT=cacert.pem
CA_CERT_CER=cacert.cer

RADIUS_KEY=radius_key.pem
RADIUS_REQ=radius_req.pem
RADIUS_CERT=radius_cert.pem

BACKUP_CONFIG_SCRIPT=$USER_HOME/scripts/backupConfig.sh
BACKUP_CREATE_SCRIPT=$USER_HOME/scripts/backup_create.sh
BACKUP_TRAPS_SCRIPT=$USER_HOME/scripts/backup_traps.sh
TFTP_FOLDER=$USER_HOME/backups.git

chown www-data:www-data /home/snack/interface/app/Config/parameters.php

# Setting approriates permissions
# Affectation des permissions

{
chown $INTERFACE_USER $DEST_PATH/private/$CA_KEY
chmod $INTERFACE_ACCESS $DEST_PATH/private/$CA_KEY
chmod $READ_ONLY $DEST_PATH/private/$CA_CERT
} >> $OUT 2>>$LOG

chown $INTERFACE_USER: $DEST_PATH/$CA_CERT
chown $INTERFACE_USER: $DEST_PATH/$CA_CERT_CER


# Setting approriates permissions
# Affectation des permissions

{
chmod 770 $DEST_PATH
chmod 710 $DEST_PATH/certs
chmod 770 $DEST_PATH/newcerts
chmod 770 $DEST_PATH/crl
chmod 710 $DEST_PATH/private
chmod 770 $DEST_PATH/users  
chown $RADIUS_USER $DEST_PATH/private/$RADIUS_KEY
chown $RADIUS_USER $DEST_PATH/private/$RADIUS_CERT
chmod $ONLY_RADIUS_ACCESS $DEST_PATH/private/$RADIUS_KEY
chmod $ONLY_RADIUS_ACCESS $DEST_PATH/private/$RADIUS_CERT
} >> $OUT 2>>$LOG

# Setting approriates permissions
# Affectation des permissions

{
chown $RADIUS_USER $DEST_PATH/crl/crl.pem
chmod $INTERFACE_ACCESS $DEST_PATH/crl/crl.pem
} >> $OUT 2>>$LOG

# Setting approriates permissions
# Affectation des permissions

{
chown $RADIUS_USER $DEST_PATH/random
chown $RADIUS_USER $DEST_PATH/dh
chmod $ONLY_RADIUS_ACCESS $DEST_PATH/random
chmod $ONLY_RADIUS_ACCESS $DEST_PATH/dh
} >> $OUT 2>>$LOG


# Setting approriates permissions
# Affecter les permissions

{
chown -R $INTERFACE_USER $USER_HOME/scripts
chmod -R $ONLY_INTERFACE_ACCESS $USER_HOME/scripts
} >> $OUT 2>>$LOG

chown -R :snack $USER_HOME
chmod 770 $USER_HOME/logs
chown -R snack:snack $TFTP_FOLDER
chmod -R 0770 $TFTP_FOLDER

{
chmod +x $USER_HOME/scripts
chown snmp:snack $BACKUP_CREATE_SCRIPT
chmod 0550 $BACKUP_CREATE_SCRIPT
chown snmp:snack $BACKUP_TRAPS_SCRIPT
chmod 0550 $BACKUP_TRAPS_SCRIPT
chown snack:snack $USER_HOME/scripts/backup.sh
chmod 770 $USER_HOME/scripts/backup.sh
chown snack:snack $USER_HOME/scripts/telnet.pl
chmod 770 $USER_HOME/scripts/telnet.pl
chown snack:snack $USER_HOME/scripts/ssh.expect
chmod 770 $USER_HOME/scripts/ssh.expect
} >> $OUT 2>>$LOG

{
chown -R root:root /etc/sudoers.d
usermod -a -G winbindd_priv freerad
}


