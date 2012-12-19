<? 
$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');

echo '<h1>Edit ' . $this->data['Radgroup']['groupname'] . ' group</h1>';

echo $this->Form->create('Radgroup', array('action' => 'edit'));
echo $this->element('check_common_fields');
echo $this->Form->input('users', array('type' => 'select', 'label' => 'Users', 'multiple' => 'multiple'));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('groupname', array('type' => 'hidden'));
echo $this->Form->end('Update');
?>

