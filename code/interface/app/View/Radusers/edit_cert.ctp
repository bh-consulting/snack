<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');
Configure::load('parameters');

echo '<h1>'
    . __('Edit') . ' '
    . $username
    . ' (' . __('certificate user') . ')'
    . '</h1>';

echo $this->Form->create('Raduser', array(
    'action' => 'edit_cert',
    'novalidate' => true,
));

$certs = '<fieldset>';
$certs .= '<legend>' . __('Certificate') . '</legend>';
$certs .= $this->Form->input(
    'cert_gen',
    array('type' => 'checkbox', 'label' => __('Generate a new certificate'))
);
$certs .= $this->Form->input(
    'country',
    array('default' => Configure::read('Parameters.countryName'))
);
$certs .= $this->Form->input(
    'province',
    array('default' => Configure::read('Parameters.stateOrProvinceName'))
);
$certs .= $this->Form->input(
    'locality',
    array('default' => Configure::read('Parameters.localityName'))
);
$certs .= $this->Form->input(
    'organization',
    array('default' => Configure::read('Parameters.organizationName'))
);
$certs .= '</fieldset>';

$checks = '<fieldset>';
$checks .= '<legend>' . __('Checks') . '</legend>';
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

$cisco = $this->element('cisco_common_fields', array('type' => 'cert'));

$replies = '<fieldset>';
$replies .= '<legend>' . __('Replies') . '</legend>';
$replies .= $this->element('reply_common_fields');
$replies .= '</fieldset>';

$role = $this->element('snack_role_input');

$finish = $this->Form->input('id', array('type' => 'hidden'));
$finish .= $this->Form->input('was_cisco', array('type' => 'hidden'));

$finish .= $this->Form->end(array(
    'label' => __('Update'),
    'class' => 'next finish',
    'style' => 'display:none;',
));

echo $this->element('wizard', array(
    'steps' => array(
        __('Certificate') => $certs,
        __('Checks') => $checks,
        __('Cisco') => $cisco,
        __('Replies') => $replies,
        __('Role') => $role,
    ),
    'finishButton' => $finish,
));

?>
