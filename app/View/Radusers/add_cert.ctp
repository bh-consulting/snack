<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');
Configure::load('parameters');
?>

<h1><?php echo __('Add a user with a certificate'); ?></h1>
<?php
echo $this->Form->create('Raduser', array('novalidate' => true));

$certificate = '<fieldset>';
$certificate .= '<legend>' . __('Certificate') . '</legend>';

$certificate .= $this->Form->input('username');
$certificate .= $this->Form->input('password');
$certificate .= $this->Form->input(
    'country',
    array('default' => Configure::read('Parameters.countryName'))
);
$certificate .= $this->Form->input(
    'province',
    array('default' => Configure::read('Parameters.stateOrProvinceName'))
);
$certificate .= $this->Form->input(
    'locality',
    array('default' => Configure::read('Parameters.localityName'))
);
$certificate .= $this->Form->input(
    'organization',
    array('default' => Configure::read('Parameters.organizationName'))
);

$certificate .= '</fieldset>';

$checks = '<fieldset>';
$checks .= '<legend>' . __('Checks') . '</legend>';

$checks .= $this->Form->input(
    'calling-station-id',
    array('label' => __('MAC address'))
);
$checks .= $this->element('check_common_fields');
$checks .= $this->element(
    'doubleListsSelector',
    array(
        'leftTitle' => __('Groups'),
        'rightTitle' => __('Selected groups'),
        'contents' => $groups,
        'selectedContents' => array(),
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

$finish = $this->Form->end(array(
    'label' => __('Create'),
    'class' => 'next finish',
    'style' => 'display:none;'
));

echo $this->element('wizard', array(
    'steps' => array(
        __('Certificate') => $certificate,
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
