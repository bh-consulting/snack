<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo '<h1>' . __('Add a Cisco Phone') . '</h1>';

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

$checks .= $this->Form->input('username');

$checks .= $this->Form->input('is_mac', array(
    'type' => 'checkbox',
    'between' => '',
    'after'   => '',
    'class' => '', 
    'before' => '<label class="col-sm-4 control-label">'.__('MAC Address').'</label><div class="col-sm-1">',
    'between' => '',
    'after'   => '</div>',
    'label' => false,
));

$myLabelOptions = array('text' => __('Password'));
$checks .= $this->Form->input('passwd', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$checks .= $this->Form->input('confirm_password', array('type' => 'password'));

$checks .= $this->element('check_common_fields');
$checks .= '<div class="col-sm-2"></div>';
$checks .= $this->element(
    'doubleListsSelector',
    array(
        'leftTitle' => __('Groups'),
        'rightTitle' => __('Selected groups'),
        'contents' => $groups,
        'selectedContents' => array()
    )
);
$checks .= $this->Form->input(
    'groups',
    array(
        'type' => 'select',
        'id' => 'select-right',
        'label' => '',
        'class' => 'hidden',
        'multiple' => 'multiple'
    )
);
$checks .= '</fieldset>';

$replies = '<fieldset>';
$replies .= '<legend>' . __('Replies') . '</legend>';
$replies .= $this->element('reply_common_fields_phone');
$replies .= '</fieldset>';

$finish = $this->Form->end(array(
    'label' => __('Create'),
    'class' => 'next finish btn btn-primary',
    'style' => 'display:none;',
));

echo $this->element('wizard', array(
    'steps' => array(
        __('Checks') => $checks,
        __('Replies') => $replies,
    ),
    'finishButton' => $finish,
));

$this->start('script');
echo $this->Html->script('wizard_focus');
$this->end();
?>
