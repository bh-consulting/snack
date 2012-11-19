#!/bin/bash

echo -n "Mot de passe root MySQL : "
read mdp_root

echo -n "Mot de passe de l'administrateur : "
read mdp_radius

echo "	create database radius;\
	grant all on radius.* to radius@localhost identified by '$mdp_radius';\
	flush privileges;\
" | mysql -uroot -p$mdp_root

mysql -uroot -p$mdp_root radius < /etc/freeradius/sql/mysql/schema.sql
mysql -uroot -p$mdp_root radius < /etc/freeradius/sql/mysql/nas.sql

echo "INSERT INTO radcheck(UserName, Attribute, op, Value) VALUES ('admin', 'Cleartext-Password', ':=', '$mdp_radius');" | mysql -uroot -p$mdp_root radius

exit 0
