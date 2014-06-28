#!/bin/bash

CRON=/etc/cron.d/snack

usage()
{
    echo -en "Usage:\t$1 [-l|--list] [-m|--modify fsdfs]\n\nContact: <groche@guigeek.org>\n"
}


list()
{
    cat $CRON
}

modify()
{
    echo "$str $script";
    sed -e "s/.*\(\s*www-data\s*.*$script\s*$\)/$str\t* * *\t\1/" -i /etc/cron.d/snack
    sed -e "s/.*\(\s*root\s*.*$script\s*$\)/$str\t* * *\t\1/" -i /etc/cron.d/snack
}

options=$(getopt -o hlm: -l help,list,modify: -- "$@")
if [ $? -ne 0 ]; then
    usage $(basename $0)
    exit 1
fi
eval set -- "$options"

while [ $# -gt 0 ]
do
    case "$1" in
        -h|--help)      usage $0 && exit 0;;
        -l|--list)      list; shift ;;
        -m|--modify)    str=$4; script=$2; modify; shift 2;;
        --)             shift; break ;;
        -*)             echo "$0: error - unrecognized option $1" 1>&2; exit 1;;
        *)              usage $0 && exit 0;;
    esac
done


