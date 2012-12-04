<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
<h1>Add a user with MAC address</h1>
<?php
echo $this->Form->create('Raduser');
echo $this->Form->input('mac', array('label' => 'MAC address'));
echo $this->element('check_common_fields');
echo $this->Form->end('Create');
?>

