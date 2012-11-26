<? 
$this->extend('/Common/radius_sidebar');
?>
<h1>Add a user with MAC address</h1>
<?php
echo $this->Form->create('Raduser');
echo $this->Form->input('mac', array('label' => 'MAC address'));
echo $this->Form->input('comment');
echo $this->Form->end('Create');
?>

