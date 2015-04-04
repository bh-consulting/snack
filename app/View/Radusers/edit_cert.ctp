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

$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Raduser', array(
    'novalidate' => true, 
    'autocomplete' => 'off',
    'class' => 'form-horizontal',
    'inputDefaults' => array(
        'div' => 'form-group',
        'label' => array(
            'class' => $mainLabelOptions
        ),
        'between' => '<div class="col-sm-4">',
        'after'   => '</div>',
        'class' => 'form-control'
    ),
));

$certs = '<fieldset>';
$certs .= '<legend>' . __('Certificate') . '</legend>';
$certs .= $this->Form->input('cert_gen', array(
    'type' => 'checkbox',
    'between' => '',
    'after'   => '',
    'class' => ' form-control', 
    'before' => '<label class="col-sm-4 control-label">'.__('Generate a new certificate').'</label><div class="col-sm-1">',
    'between' => '',
    'after'   => '</div>',
    'label' => false,
));

$certs .= $this->Form->input('password');
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
$myLabelOptions = array('text' => __('MAC address'));
$checks .= $this->Form->input('calling-station-id', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$checks .= $this->element('check_common_fields');
$checks .= '<div class="col-sm-2"></div>';
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

//$cisco = $this->element('cisco_common_fields', array('type' => 'cert'));

$replies = '<fieldset>';
$replies .= '<legend>' . __('Replies') . '</legend>';
$replies .= $this->element('reply_common_fields');
$replies .= '</fieldset>';

$role = $this->element('snack_role_input');

$finish = $this->Form->input('id', array('type' => 'hidden'));
//$finish .= $this->Form->input('was_cisco', array('type' => 'hidden'));
$finish .= $this->Form->input('was_user', array('type' => 'hidden'));

$finish .= $this->Form->end(array(
    'label' => __('Update'),
    'class' => 'next finish btn btn-primary',
    'style' => 'display:none;',
));

echo $this->element('wizard', array(
    'steps' => array(
        __('Certificate') => $certs,
        __('Checks') => $checks,
        //__('Cisco') => $cisco,
        __('Replies') => $replies,
        __('Role') => $role,
    ),
    'finishButton' => $finish,
));

$this->start('script');
echo $this->Html->script('wizard_focus');
$this->end();
?>
