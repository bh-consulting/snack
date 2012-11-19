<? 
$this->extend('/Common/radius_sidebar');
?>
<h1>Edit User</h1>
<?php
echo $this->Form->create('Raduser', array('action' => 'edit'));
echo $this->Form->input('username');
echo $this->Form->input('attribute');
echo $this->Form->input('op');
echo $this->Form->input('value');
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->end('Save User');
?>
