<h1>Add user</h1>
<?
echo $this->Form->create(null, array('action' => 'add'));
?>
<div class="select">
<?
echo $this->Form->select('user_type', array('cisco' => 'Cisco',
    'loginpass' => 'Login/Password',
    'cert' => 'Certificate',
    'mac' => 'MAC address',
    'csv' => 'Upload CSV'));
?>
</div>
<?
echo $this->Form->end('Create');
?>
