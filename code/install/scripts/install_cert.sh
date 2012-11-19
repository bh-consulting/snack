#!/bin/sh

#WARNING! THIS SCRIPT GENERATE A FILE IN THE CURRENT DIR!

#CAplPATH=/usr/lib/ssl/misc
CAplPATH=install/files/usr/lib/ssl/misc
DESTROOTPATH=/var/www
DESTFOLDER=CA
DESTPATH=$DESTROOTPATH/$DESTFOLDER
SSLCNF=/etc/ssl/openssl.cnf
RADCERTVALIDITY=365
sed\
	-e "s|./demoCA|$DESTPATH|"\
	-e '/countryName_default/c countryName_default		= FR'\
	-e '/stateOrProvinceName_default/c stateOrProvinceName_default	= France'\
	-i $SSLCNF
if grep -qs "localityName_default" $SSLCNF 
then
	sed\
		-e '/localityName_default/c localityName_default		= Nancy'\
		-i $SSLCNF
else
	sed\
		-e '/Locality Name/a localityName_default		= Nancy'\
		-i $SSLCNF
fi
sed\
	-e '/0.organizationName_default/c 0.organizationName_default	= BHConsulting'\
	-i $SSLCNF



if test ! -d $DESTROOTPATH
then
	mkdir -p $DESTROOTPATH
fi
$CAplPATH/CA.pl -newca

openssl req -config /etc/ssl/openssl.cnf -new -keyout $DESTPATH/private/radius_key.pem -out newreq.pem -days $RADCERTVALIDITY
openssl ca -config /etc/ssl/openssl.cnf -policy policy_anything -out $DESTPATH/private/radius_cert.pem -infiles newreq.pem
