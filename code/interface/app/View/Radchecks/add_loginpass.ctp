<h1>Add a user with login / password</h1>
<?php
echo $this->Form->create('Radcheck');
echo $this->Form->input('username');
echo $this->Form->input('password');
echo $this->Form->end('Create');
?>


