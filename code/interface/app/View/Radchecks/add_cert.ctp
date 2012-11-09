<h1>Add a user with a certificate</h1>
<?php
echo $this->Form->create('Radcheck', array('enctype' => 'multipart/form-data'));
echo $this->Form->input('username');
echo $this->Form->file('file');
echo $this->Form->end('Create');
?>



