#!/bin/bash
var=""

usage() {
    echo -en "Usage:\t$1 [-n number of lines] [-p page number]\nContact: <groche@guigeek.org>\n"
}

display_voip() {
    regex="fcid:([A-Z0-9]+),legID:"
    if [[ $line =~ $regex ]]; then
        fcid="${BASH_REMATCH[1]}"
        string=$fcid
        echo $string
        display
    fi
}

check_variables() {
    if [[ -z $host ]]; then
        var="H0"
    else
        var="H1"
    fi
    if [[ -z $DATE ]]; then
        var="$var D0"
    else
        var="$var D1"
        datefrom=$(echo $dates |cut -d'/' -f1)
        dateto=$(echo $dates |cut -d'/' -f2)
    fi
    if [[ -z "$string" ]]; then
        var="$var S0"
    else
        var="$var S1"
    fi
    if [[ -z "$VOIP" ]]; then
        var="$var V0"
    else
        var="$var V1"
    fi
}

display() {
    #echo $var
    regex="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\+[0-9]{2}:[0-9]{2}\s+$host"
    if [[ "$var" == "H0 D0 S0 V0" ]]; then
        count=`grep -E "$regprio" $file | wc -l`
        echo "$count"
        last=$((count-($page-1)*$number))
        if (("$last"<="$number")); then
            first=1
        else 
            first=$((last-number))
        fi
        echo $first" "$last
        grep -E "$regprio" $file | sed -n "$first,$last p" | sort -r
    fi
    if [[ "$var" == "H0 D0 S1 V0" ]]; then
        count=`grep -E "$regprio" $file | grep -E "$string" | wc -l`
        last=$((count-($page-1)*$number))
        if (("$last"<="$number")); then
            first=1
        else 
            first=$((last-number))
        fi
        if [[ $string == "VOIP" ]]; then
            echo $((count/2))
        else
            echo $count
        fi
        grep -E "$regprio" $file | grep -E "$string" | sed -n "$first,$last p" | sort -r
    fi
    if [[ "$var" == "H0 D1 S0 V0" ]]; then
        count=$(grep -E "$regprio" $file | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | wc -l)
        echo $count
        last=$((count-($page-1)*$number))
        if (("$last"<="$number")); then
            first=1
        else 
            first=$((last-number))
        fi
        grep -E "$regprio" $file | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | sed -n "$first,$last p" | sort -r
    fi
    if [[ "$var" == "H1 D0 S0 V0" ]]; then
        count=`grep -E "$regprio" $file | grep -E "$host" | wc -l`
        echo $count
        last=$((count-($page-1)*$number))
        if (("$last"<="$number")); then
            first=1
        else 
            first=$((last-number))
        fi
        grep -E "$regprio" $file | grep -E "$host" | sed -n "$first,$last p" | sort -r
    fi
    if [[ "$var" == "H0 D1 S1 V0" ]]; then
        count=`grep -E "$regprio" $file | grep -E "$string" | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | wc -l`
        echo $count
        last=$((count-($page-1)*$number))
        if (("$last"<="$number")); then
            first=1
        else 
            first=$((last-number))
        fi
        grep -E "$regprio" $file | grep -E "$string" | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | sed -n "$first,$last p" | sort -r
    fi
    if [[ "$var" == "H1 D0 S1 V0" ]]; then
        count=`grep -E "$regprio" $file | grep -E "$string" | grep -E "$host" | wc -l`
        echo $count
        last=$((count-($page-1)*$number))
        if (("$last"<="$number")); then
            first=1
        else 
            first=$((last-number))
        fi
        grep -E "$regprio" $file | grep -E "$string" | grep -E "$host" | sed -n "$first,$last p" | sort -r
    fi
    if [[ "$var" == "H1 D1 S0 V0" ]]; then
        count=`grep -E "$regprio" $file | grep -E "$host" | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | wc -l`
        echo $count
        last=$((count-($page-1)*$number))
        if (("$last"<="$number")); then
            first=1
        else 
            first=$((last-number))
        fi
        grep -E "$regprio" $file | grep -E "$host" | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | sed -n "$first,$last p" | sort -r
    fi
    if [[ "$var" == "H1 D1 S1 V0" ]]; then
        count=`grep -E "$regprio" $file | grep -E "$host" | grep -E "$string" | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | wc -l`
        echo $count
        last=$((count-($page-1)*$number))
        if (("$last"<="$number")); then
            first=1
        else 
            first=$((last-number))
        fi
        grep -E "$regprio" $file | grep -E "$host" | grep -E "$string" | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | sed -n "$first,$last p" | sort -r
    fi
    if [[ "$var" == "H0 D0 S0 V1" ]]; then
        if [[ "$priority" == "err,crit,emerg" ]]; then
            count=`grep -E "VOIPAAA" $file | grep -E "DisconnectCause\s[0123]{1}[^0]" | wc -l`
            #echo $((count/2))
            echo $count
            last=$((count-($page-1)*$number))
            if (("$last"<="$number")); then
                first=1
            else 
                first=$((last-number))
            fi
            echo "$first $last"
            grep -E "VOIPAAA" $file | grep -B1 -E "DisconnectCause\s[0123]{1}[^0]" | sed -n "$first,$last p" | sort -r
        else
            count=`grep -E "VOIP" $file | wc -l`
            echo $count
            echo $page
            #echo $((count/2))
            last=$(($count-($page-1)*$number))
            if (("$last"<="$number")); then
                first=1
            else 
                first=$((last-number))
            fi
            echo "$first $last"
            grep -E "VOIP" $file | sed -n "$first,$last p" | sort -r
        fi
    fi
    if [[ "$var" == "H0 D0 S1 V1" ]]; then
        count=`grep -E "VOIP" $file | grep -E "c[gd]n:[0-9]*$string" | wc -l`
        echo $((count/2))
        res=`grep -E "VOIP" $file | grep -E "c[gd]n:[0-9]*$string" | sort -r `
        IFS_BAK=$IFS
        IFS=$'\n'
        for f in $res; do
            line=$f
            var="H0 D0 S1 V0"
            display_voip
        done
        IFS=$IFS_BAK
        IFS_BAK=
    fi
    if [[ "$var" == "H0 D1 S0 V1" ]]; then
        count=`grep -E "VOIP" $file | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | wc -l`
        echo $((count/2))
        res=`grep -E "VOIP" $file | awk -v datefrom="$datefrom" '$0 >= datefrom' | awk -v dateto="$dateto" '$0 <= dateto' | sort -r `
        IFS_BAK=$IFS
        IFS=$'\n'
        for f in $res; do
            line=$f
            var="H0 D0 S1 V0"
            display_voip
        done
        IFS=$IFS_BAK
        IFS_BAK=
    fi
# /[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\+[0-9]{2}:[0-9]{2})\s+([^\s]+)\s+([^\s]+):\s+\[(local[0-9]+)\.(debug|info|notice|warn|err|crit|alert|emerg)\]\s+(.*)/
#./scriptLogs.sh -n 50 --page 1 --facility local2 | awk '$0 >= "2014-06-20T12:00" && $0 <= "2014-06-20T14:00"'
}

# Init variables
facility=".*"
priority=".*"
number=-1
page=1
options=$(getopt -o hp:n:f: -l between-dates:,voip,host:,page:,priority:,string:,file:,facility: -- "$@")

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
        --voip)         VOIP=1; shift 2;;
        --between-dates) DATE=1; dates=$2;shift 2;;
        --)             shift; break ;;
        -*)             echo "$0: error - unrecognized option $1" 1>&2; exit 1;;
        *)              echo "$1" && usage $0 && exit 0;;
    esac
done
function regpriofac() 
{
    echo "$priority" | tr ',' '\n' | while read prio; do
        echo -n "\[$facility\.$prio\]|"
    done
}
regp=$(regpriofac)
regex='\\\[(.*)\\\.\.\s\.\.\\\]'
if [[ $regp =~ $regex ]]; then
    facility="${BASH_REMATCH[1]}"
    regprio="\[$facility\..*\]"
else
    regprio=${regp%?}
fi

if [ "$number" == "-1" ]; then
    number=$(grep -E "$regprio" $file | wc -l)
fi

check_variables
echo $var
display 
