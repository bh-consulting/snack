<?php
if (isset($port)) {
    if( preg_match('/(\w{2}).*(\d+\/\d*)/', $port, $matches)) {
        echo $matches[1].$matches[2];
    }
}

?>