<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
<h1>Add a Cisco user</h1>
<?php
echo $this->Form->create('Raduser');
echo $this->Form->input('username');
echo $this->Form->input('password');
echo $this->Form->input('confirm_password', array('type' => 'password'));
echo $this->Form->input('nas-port-type', array(
    'options' => array(0, 5),
    'empty' => false,
    'label' => 'NAS Port Type')
);
echo $this->element('check_common_fields');
echo $this->Form->end('Create');
?>

