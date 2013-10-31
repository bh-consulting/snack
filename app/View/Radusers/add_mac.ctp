<? 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');
?>
<h1> <? echo __('Add a passive user with MAC address'); ?></h1>
<?php
echo $this->Form->create('Raduser', array('novalidate' => true));

$checks = '<fieldset>';
$checks .='<legend>' . __('Checks') . '</legend>';
$checks .= $this->Form->input('mac', array('label' => __('MAC address')));
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

$replies = '<fieldset>';
$replies .= '<legend>' . __('Replies') . '</legend>';
$replies .= $this->element('reply_common_fields');
$replies .= '</fieldset>';

$finish = $this->Form->end(array(
    'label' => __('Create'),
    'class' => 'next finish',
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
