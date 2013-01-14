<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo '<h2>' . __('Edit') . ' ' . $this->data['Raduser']['username'] . ' (' . __('certificate user') . ') </h2>';

echo $this->Form->create('Raduser', array('action' => 'edit_cert'));
echo $this->Form->input('cert_gen', array('type' => 'checkbox', 'label' => __('Generate a new certificate')));
echo $this->element('check_common_fields');
echo $this->element('doubleListsSelector', array('leftTitle' => __('Groups'), 'rightTitle' => __('Selected groups'), 'contents' => $groups, 'selectedContents' => $selectedGroups));
echo $this->Form->input('groups', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('username', array('type' => 'hidden'));
echo $this->Form->end(__('Update'));
?>
