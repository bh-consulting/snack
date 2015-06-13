<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo '<h1>' . __('Edit') . ' ' . $username
    . ' ' . __('(login / password user)') . '</h1>';

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

$checks = '<fieldset>';
$checks .= '<legend>' . __('Checks') . '</legend>';
$myLabelOptions = array('text' => __('Password'));
$checks .= $this->Form->input('passwd', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$myLabelOptions = array('text' => __('Confirm Password'));
$checks .= $this->Form->input('confirm_password', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'type' => 'password',
));

//$checks .= $this->Form->input('ttls', array(
$checks .= $this->Form->input('cleartext', array(
    'type' => 'checkbox',
    'between' => '',
    'after'   => '',
    'class' => '', 
    //'before' => '<label class="col-sm-4 control-label">'.__('Check server certificate').'</label><div class="col-sm-1">',
    'before' => '<label class="col-sm-4 control-label">'.__('ClearText Password (for older supplicant Not Secure)').'</label><div class="col-sm-1">',
    'between' => '',
    'after'   => '</div>',
    'label' => false,
    //'readonly' => true,
));

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

$cisco = $this->element('cisco_common_fields', array('type' => 'loginpass'));

$replies = '<fieldset>';
$replies .= '<legend>' . __('Replies') . '</legend>';
$replies .= $this->element('reply_common_fields');
$replies .= '</fieldset>';

$finish = $this->Form->input('id', array('type' => 'hidden'));
$finish .= $this->Form->end(array(
    'label' => __('Update'),
    'class' => 'next finish btn btn-primary',
    'style' => 'display:none;',
));

echo $this->element('wizard', array(
    'steps' => array(
        __('Checks') => $checks,
        __('Cisco') => $cisco,
        __('Replies') => $replies,
    ),
    'finishButton' => $finish,
));

$this->start('script');
echo $this->Html->script('wizard_focus');
$this->end();
?>
