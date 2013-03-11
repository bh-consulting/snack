<?php

$patternDate = '/^(?<year>20\d{2})-'
    . '(?<mon>0[1-9]|1[12])-'
    . '(?<day>0[1-9]|[1-2]\d|3[01])\s+'
    . '(?<hour>[01]\d|2[0-3]):(?<min>[0-5]\d):(?<sec>[0-5]\d)$/';

if (preg_match(
    	$patternDate,
    	$date,
    	$fields)
   ) {

   printf(
        "%02d/%02d/%d à %02dh%02d et %02ds",
        $fields['day'],
        $fields['mon'],
        $fields['year'],
        $fields['hour'],
        $fields['min'],
        $fields['sec']
   );
} else
    echo $date;

?>
