<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

foreach ($results as $result) {
    echo $result . '</br>';
}
?>
