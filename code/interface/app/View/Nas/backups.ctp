<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');
?>

<h1><?php echo __('Backups (%s)', $this->data['Nas']['nasname']); ?></h1>
<?php
    echo implode('<br />', $git);
?>
