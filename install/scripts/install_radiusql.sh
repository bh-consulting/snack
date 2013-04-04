#!/bin/bash

echo -n "Mot de passe de l'utilisateur radius : "
read mdp_radius

sed\
	-e "s/radpass/$mdp_radius/"\
	-e "s/#\(readclients\)/\1/"\
	-i /etc/freeradius/sql.conf

sed\
	-e 's/#\(\s*\$INCLUDE sql\.conf\)/\1/'\
	-e 's/#\(\s*\$INCLUDE sql\/mysql\/counter.conf\)/\1/'\
    -e 's/\(\s*auth\s*=\s*\)no/\1yes/'\
	-i radiusd.conf

exit 0
