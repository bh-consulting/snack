<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');
?>
<h1><? echo __('Import Users'); ?></h1>
<?php
foreach ($results as $result) {
    echo $result;
}
?>
