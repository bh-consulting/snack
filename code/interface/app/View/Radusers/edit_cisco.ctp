<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo '<h1>Edit ' . $this->data['Raduser']['username'] . ' Cisco user</h1>';

echo $this->Form->create('Raduser', array('action' => 'edit'));
echo $this->Form->input('password');
echo $this->Form->input('password2', array('type' => 'password', 'label' => 'Confirm password'));
echo $this->Form->input('comment');
echo $this->Form->input('nas-port-type', array(
    'options' => array(0, 5),
    'empty' => false,
    'label' => 'NAS Port Type')
);
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->end('Create');
?>

