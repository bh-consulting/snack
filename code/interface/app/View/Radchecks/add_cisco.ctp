<h1>Add a Cisco user</h1>
<?php
echo $this->Form->create('Radcheck');
echo $this->Form->input('username');
echo $this->Form->input('password');
echo $this->Form->input('nas-port-type', array(
    'options' => array(0, 5),
    'empty' => false,
    'label' => 'NAS Port Type')
);
echo $this->Form->end('Create');
?>

