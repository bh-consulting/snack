<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo '<h1>Edit ' . $this->data['Raduser']['username'] . ' (login / password user)</h1>';

echo $this->Form->create('Raduser', array('action' => 'edit_loginpass'));
echo $this->Form->input('password');
echo $this->Form->input('confirm_password', array('type' => 'password', 'label' => 'Confirm password'));
echo $this->element('check_common_fields');
echo $this->Form->input('groups', array('type' => 'select', 'label' => 'Groups', 'multiple' => 'multiple', 'selected' => array_values($groups_selected)));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('username', array('type' => 'hidden'));
echo $this->Form->end('Update');
?>

