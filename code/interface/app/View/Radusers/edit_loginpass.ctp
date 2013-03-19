<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo '<h1>' . __('Edit') . ' ' . $username
    . ' ' . __('(login / password user)') . '</h1>';

echo $this->Form->create('Raduser', array(
    'action' => 'edit_loginpass',
    'novalidate' => true,
));

$checks = '<fieldset>';
$checks .= '<legend>' . __('Checks') . '</legend>';
$checks .= $this->Form->input('passwd', array('type' => 'password', 'label' => __('Password')));
$checks .= $this->Form->input(
    'confirm_password',
    array(
	'type' => 'password',
	'label' => 'Confirm password',
    )
);
$checks .= $this->Form->input(
    'ttls',
    array(
	'type' => 'checkbox',
	'label' => __('Check server certificate'),
	'class' => 'switchbtn'
    )
);
$checks .= $this->Form->input('calling-station-id', array('label' => __('MAC address')));
$checks .= $this->element('check_common_fields');
$checks .= $this->element(
    'doubleListsSelector',
    array(
	'leftTitle' => __('Groups'),
	'rightTitle' => __('Selected groups'),
	'contents' => $groups,
	'selectedContents' => $selectedGroups,
    )
);
$checks .= $this->Form->input(
    'groups',
    array(
	'type' => 'select',
	'id' => 'select-right',
	'label' => '',
	'class' => 'hidden',
	'multiple' => 'multiple',
    )
);
$checks .= '</fieldset>';

$cisco = $this->element('cisco_common_fields', array('type' => 'loginpass'));

$replies = '<fieldset>';
$replies .= '<legend>' . __('Replies') . '</legend>';
$replies .= $this->element('reply_common_fields');
$replies .= '</fieldset>';

$role = $this->element('snack_role_input');

$finish = $this->Form->input('id', array('type' => 'hidden'));
$finish .= $this->Form->end(array(
    'label' => __('Update'),
    'class' => 'next finish',
    'style' => 'display:none;',
));

echo $this->element('wizard', array(
    'steps' => array(
        __('Checks') => $checks,
        __('Cisco') => $cisco,
        __('Replies') => $replies,
        __('Role') => $role,
    ),
    'finishButton' => $finish,
));

$this->start('script');
echo $this->Html->script('wizard_focus');
$this->end();
?>
