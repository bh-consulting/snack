<h1>Add user</h1>
<?php
echo $this->Form->create('Radcheck');
echo $this->Form->select('user_type', array('cisco' => 'Cisco',
    'loginpass' => 'Login/Password',
    'cert' => 'Certificate',
    'mac' => 'MAC address',
    'csv' => 'Upload CSV'));
echo $this->Form->end('Create');
?>
