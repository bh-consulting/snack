<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo '<h1>' . __('Edit') . ' ' . $this->data['Raduser']['username'] . ' ' . __('(MAC user)') . '</h1>';

echo $this->Form->create('Raduser', array('action' => 'edit_mac'));
echo $this->element('check_common_fields');
echo $this->element('doubleListsSelector', array('leftTitle' => 'Groups', 'rightTitle' => 'Selected groups', 'contents' => $groups, 'selectedContents' => $selectedGroups));
echo $this->Form->input('groups', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('username', array('type' => 'hidden'));
echo $this->Form->end(__('Update'));
?>
