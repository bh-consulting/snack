#!/bin/bash
LOG=/home/snack/logs/snack.log

usage() {
    echo -en "Usage:\t$1 [-n number of lines] [-p page number]\nContact: <groche@guigeek.org>\n"
}

display_voip() {
    regex="fcid:([A-Z0-9]+),legID:"
    if [[ $line =~ $regex ]]; then
        fcid="${BASH_REMATCH[1]}"
        string=$fcid
        display
    fi
}

display() {
    #echo $number
    #echo $page
    #echo "$Facility"
    #echo "$priority"
    #echo $date1
    #echo $date2
    first=$((($page-1)*$number+1))
    last=$(($first+$number-1))
    #echo $first $last
    regex="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\+[0-9]{2}:[0-9]{2}\s+$host"
    #echo $regex
    if [[ -z $host ]]; then
        if [[ -z $DATE ]]; then
            grep -E "\[$facility\.$priority\]" $file | grep -E "$string" | sed -n "$first,$last p" | sort -r
        else
            grep -E "\[$facility\.$priority\]" $file | grep -E "$string" | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | sed -n "$first,$last p" | sort -r
        fi
    else
        if [[ -z $DATE ]]; then
            grep -E "\[$facility\.$priority\]" $file | grep -E "$string" | grep -E "$regex" | sed -n "$first,$last p" | sort -r
        else
            grep -E "\[$facility\.$priority\]" $file | grep -E "$string" | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | grep -E "$regex" | sed -n "$first,$last p" | sort -r
        fi
    fi
# /[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\+[0-9]{2}:[0-9]{2})\s+([^\s]+)\s+([^\s]+):\s+\[(local[0-9]+)\.(debug|info|notice|warn|err|crit|alert|emerg)\]\s+(.*)/
#./scriptLogs.sh -n 50 --page 1 --facility local2 | awk '$0 >= "2014-06-20T12:00" && $0 <= "2014-06-20T14:00"'
}

# Init variables
facility=".*"
priority=".*"
string=".*"

options=$(getopt -o hcp:n:f: -l between-dates:,voip:,host:,page:,priority:,string:,file:,facility: -- "$@")
if [ $? -ne 0 ]; then
    usage $(basename $0)
    exit 1
fi
eval set -- "$options"

while [ $# -gt 0 ]
do
    case "$1" in
        -h|--help)      usage $0 && exit 0;;
        -n)             number=$2; shift 2;;
        -f|--file)      file=$2; shift 2;;
        --page)         page=$2; shift 2;;
        --host)         host=$2; shift 2;;
        --priority)     priority=$2; shift 2;;        
        --facility)     facility=$2; shift 2;;
        --string)       string=$2; shift 2;;
        --voip)         VOIP=1; string=$2; shift 2;;
        --between-dates) DATE=1; datefrom=$2; dateto=$4; shift 2;;
        -c)             COUNT=1; shift 2;;
        --)             shift; break ;;
        -*)             echo "$0: error - unrecognized option $1" 1>&2; exit 1;;
        *)              usage $0 && exit 0;;
    esac
done

if [[ -z $COUNT && -z $VOIP ]]; then
    display 
fi
if ! [[ -z $VOIP ]]; then
    if [[ -z $COUNT ]]; then
        if [[ $string == "0" ]]; then
            string="VOIP"
            display
        else
            res=`grep -E "\[$facility\.$priority\]" $file | grep -E "VOIP" | grep -E "c[gd]n:[0-9]*$string" | sort -r `
            IFS_BAK=$IFS
            IFS=$'\n'
            for f in $res; do
                line=$f
                display_voip
            done
            IFS=$IFS_BAK
            IFS_BAK=
        fi
    fi
fi
if ! [[ -z $COUNT ]]; then
    regex="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\+[0-9]{2}:[0-9]{2}\s+$host"
    if ! [[ -z $VOIP ]]; then
        #cmd=$cmd'grep -E "VOIP" | grep -E "c[gd]n:[0-9]*'$string'" | wc -l'
        #cmd="$cmd grep -E \"VOIP\" | grep -E \"c[gd]n:[0-9]*$string\" | wc -l"
        #echo ""
        if [[ -z $DATE ]]; then
            res=`grep -E "\[$facility\.$priority\]" $file | grep -E "VOIP" | grep -E "c[gd]n:[0-9]*$string" | wc -l`
            echo $((res/2))
        fi
    else
        if [[ -z $DATE ]]; then
            grep -E "\[$facility\.$priority\]" $file | grep -E "$string" | grep -E "$regex" | wc -l
        else
            grep -E "\[$facility\.$priority\]" $file | grep -E "$string" | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | grep -E "$regex" | wc -l
        fi
        #grep -E "\[$facility\.$priority\]" $file | grep -E "$string" | wc -l
        exit
    fi
fi

