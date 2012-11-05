<h1>Add user</h1>
<?php
echo $this->Form->create('Radcheck');
echo $this->Form->input('username');
echo $this->Form->input('attribute');
echo $this->Form->input('op');
echo $this->Form->input('value');
echo $this->Form->end('Save user');
?>
