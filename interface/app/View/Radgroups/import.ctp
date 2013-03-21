<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('groups_active', 'active');

foreach ($results as $result) {
    echo $result . '</br>';
}
?>
