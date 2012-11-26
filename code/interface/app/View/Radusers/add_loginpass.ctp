<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
<h1>Add a user with login / password</h1>
<?php
echo $this->Form->create('Raduser');
echo $this->Form->input('username');
echo $this->Form->input('password');
echo $this->Form->input('confirm_password', array('type' => 'password'));
echo $this->Form->input('comment');
echo $this->Form->end('Create');
?>


