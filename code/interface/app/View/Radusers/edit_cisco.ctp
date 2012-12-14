<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo '<h1>Edit ' . $this->data['Raduser']['username'] . ' (Cisco user)</h1>';

echo $this->Form->create('Raduser', array('action' => 'edit_cisco'));
echo $this->Form->input('password');
echo $this->Form->input('confirm_password', array('type' => 'password', 'label' => 'Confirm password'));
echo $this->Form->input('nas-port-type', array(
    'options' => array(0 => 0, 5 => 5),
    'empty' => false,
    'label' => 'NAS Port Type',
    'selected' => $this->data['Raduser']['nas-port-type'])
);
echo $this->element('check_common_fields');
echo $this->Form->input('groups', array('type' => 'select', 'label' => 'Groups', 'multiple' => 'multiple'));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('username', array('type' => 'hidden'));
echo $this->Form->end('Update');
?>

