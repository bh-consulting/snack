<? 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo '<h1>' . __('Edit') . ' ' . $this->data['Raduser']['username'] . ' ' . __('(MAC user)') . '</h1>';

echo $this->Form->create('Raduser', array(
    'action' => 'edit_mac',
    'novalidate' => true,
));

$checks = '<fieldset>';
$checks .= '<legend>' . __('Checks') . '</legend>';
$checks .= $this->element('check_common_fields');
$checks .= $this->element('doubleListsSelector', array('leftTitle' => 'Groups', 'rightTitle' => 'Selected groups', 'contents' => $groups, 'selectedContents' => $selectedGroups));
$checks .= $this->Form->input('groups', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));
$checks .= '</fieldset>';

$replies = '<fieldset>';
$replies .= '<legend>' . __('Replies') . '</legend>';
$replies .= $this->element('reply_common_fields');
$replies .= '</fieldset>';

$role = $this->element('snack_role_input');

$finish = $this->Form->input('id', array('type' => 'hidden'));
$finish .= $this->Form->input('username', array('type' => 'hidden'));
$finish .= $this->Form->end(array(
    'label' => __('Update'),
    'class' => 'next finish',
    'style' => 'display:none;',
));

echo $this->element('wizard', array(
    'steps' => array(
        __('Checks') => $checks,
        __('Replies') => $replies,
        __('Role') => $role,
    ),
    'finishButton' => $finish,
));

?>
