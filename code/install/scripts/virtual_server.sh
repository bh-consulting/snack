#!/bin/bash

sed\
	-e 'N; s/\(#\s*clients\s*=\s*per_socket_clients.*\)\(}\)/\1\tvirtual_server = bh.consulting.net\n\2/'\
	-i /etc/freeradius/radiusd.conf

exit 0
