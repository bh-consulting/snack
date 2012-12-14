<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo '<h1>Edit ' . $this->data['Raduser']['username'] . ' (MAC user)</h1>';

echo $this->Form->create('Raduser', array('action' => 'edit_mac'));
echo $this->element('check_common_fields');
echo $this->Form->input('groups', array('type' => 'select', 'label' => 'Groups', 'multiple' => 'multiple', 'selected' => array_values($groups_selected)));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('username', array('type' => 'hidden'));
echo $this->Form->end('Update');
?>
