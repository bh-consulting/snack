#!/bin/bash

:> /tmp/backup_trap-write-mem.log

while read i; do
    echo $i >> /tmp/backup_trap-write-mem.log
done

exit 0
