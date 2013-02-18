#!/bin/bash

:> /tmp/backup_trap-tftp-done.log

while read i; do
    echo $i >> /tmp/backup_trap-tftp-done.log
done

exit 0
