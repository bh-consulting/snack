<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo '<h1>Edit ' . $this->data['Raduser']['username'] . ' (certificate user)</h1>';

echo $this->Form->create('Raduser', array('action' => 'edit_cert'));
echo $this->Form->input('cert_gen', array('type' => 'checkbox', 'label' => 'Generate a new certificate'));
echo $this->element('check_common_fields');
echo $this->Form->input('groups', array('type' => 'select', 'label' => 'Groups', 'multiple' => 'multiple'));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('username', array('type' => 'hidden'));
echo $this->Form->end('Update');
?>

