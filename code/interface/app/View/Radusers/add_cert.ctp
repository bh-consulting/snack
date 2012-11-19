<? 
$this->extend('/Common/radius_sidebar');
?>

<h1>Add a user with a certificate</h1>
<?php
echo $this->Form->create('Raduser');
echo $this->Form->input('username');
echo $this->Form->input('comment');
echo $this->Form->end('Create');
?>

