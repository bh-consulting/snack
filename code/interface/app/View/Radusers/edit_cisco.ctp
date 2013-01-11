<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo '<h1>' . __('Edit') . ' ' . $this->data['Raduser']['username'] . ' ' . __('(Cisco user)') . '</h1>';

echo $this->Form->create('Raduser', array('action' => 'edit_cisco'));
echo $this->Form->input('password');
echo $this->Form->input('confirm_password', array('type' => 'password', 'label' => __('Confirm password')));
echo $this->Form->input('nas-port-type', array(
    'options' => array(0 => 0, 5 => 5),
    'empty' => false,
    'label' => 'NAS Port Type',
    'selected' => isset($this->data['Raduser']['nas-port-type']) ? $this->data['Raduser']['nas-port-type'] : '0'
));
echo $this->element('check_common_fields');
echo $this->element('doubleListsSelector', array('leftTitle' => __('Groups'), 'rightTitle' => __('Selected groups'), 'contents' => $groups, 'selectedContents' => $selectedGroups));
echo $this->Form->input('groups', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('username', array('type' => 'hidden'));
echo $this->Form->end(__('Update'));
?>
