#!/bin/sh

#WARNING! THIS SCRIPT GENERATE A FILE IN THE CURRENT DIR!

#CAplPATH=/usr/lib/ssl/misc
CAplPATH=install/files/usr/lib/ssl/misc
DESTROOTPATH=/var/www
DESTFOLDER=cert
DESTPATH=$DESTROOTPATH/$DESTFOLDER
SSLCNF=/etc/ssl/openssl.cnf
RADCERTVALIDITY=3650 #ten years
CACERTVALIDITY=3650 #ten years
DIRMODE=604

CAKEY=cakey.pem
CAREQ=careq.pem
CACERT=cacert.pem

#Get the name of the client firm
read -p "Enter the name of the client (CA common name)." CLIENTNAME



#Modification of the configuration openssl.cnf
sed\
	-e "s|./demoCA|$DESTPATH|"\
#	-e '/countryName_default/c countryName_default		= FR'\
#	-e '/stateOrProvinceName_default/c stateOrProvinceName_default	= France'\
	-i $SSLCNF
#if grep -qs "localityName_default" $SSLCNF 
#then
#	sed\
#		-e '/localityName_default/c localityName_default		= Nancy'\
#		-i $SSLCNF
#else
#	sed\
#		-e '/Locality Name/a localityName_default		= Nancy'\
#		-i $SSLCNF
#fi
#sed\
#	-e '/0.organizationName_default/c 0.organizationName_default	= BHConsulting'\
#	-i $SSLCNF
#


#Creation of the needed repertories

if test ! -d $DESTROOTPATH
then
	mkdir -p $DESTROOTPATH
fi

mkdir $DESTPATH -m $DIRMODE;
mkdir "${DESTPATH}/certs" -m $DIRMODE;
mkdir "${DESTPATH}/crl" -m $DIRMODE ;
mkdir "${DESTPATH}/newcerts" -m $DIRMODE;
mkdir "${DESTPATH}/private" -m $DIRMODE;
touch $DESTPATH/index.txt
echo "01" > $DESTPATH/crlnumber

#Creation of the Authority Certificate (CA)

#openssl req -config /etc/ssl/openssl.cnf -new -keyout $DESTPATH/private/$CAKEY -out $DESTPATH/$CAREQ

openssl genrsa -out $DESTPATH/private/$CAKEY 4096 

openssl req -new -key $DESTPATH/private/$CAKEY \
	-subj /countryName=FR/stateOrProvinceName=France/localityName=Nancy/organizationName=BHConsulting/commonName=$CLIENTNAME/ \
	-out $DESTPATH/$CAREQ


openssl ca -config /etc/ssl/openssl.cnf \
	-create_serial -out $DESTPATH/private/$CACERT -days $CACERTVALIDITY -batch \
	-keyfile $DESTPATH/private/$CAKEY -selfsign \
	-extensions v3_ca \
	-infiles $DESTPATH/$CAREQ


#This instruction is replaced by the tree above instructions.
#$CAplPATH/CA.pl -newca




#Creation of the radius certificate

openssl req -config /etc/ssl/openssl.cnf -new -keyout $DESTPATH/private/radius_key.pem \
	-subj /countryName=FR/stateOrProvinceName=France/localityName=Nancy/organizationName=BHConsulting/commonName=$HOSTNAME/ \
	-out $DESTPATH/private/newreq.pem -days $RADCERTVALIDITY
openssl ca -config /etc/ssl/openssl.cnf -policy policy_anything -out $DESTPATH/private/radius_cert.pem -infiles $DESTPATH/private/newreq.pem
