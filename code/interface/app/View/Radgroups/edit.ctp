<?php

$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('groups_active', 'active');

echo '<h1>' . __('Edit') . ' ' . $this->data['Radgroup']['groupname'] . ' ' . __('group') . '</h1>';

echo $this->Form->create('Radgroup', array(
    'action' => 'edit',
    'novalidate' => true,
));

$info = '<fieldset>';
$info .= '<legend>' . __('Info') . '</legend>';
$info .= $this->element('doubleListsSelector', array('leftTitle' => __('Users'), 'rightTitle' => __('Selected users'), 'contents' => $users, 'selectedContents' => $selectedUsers));
$info .= $this->Form->input('users', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));
$info .= '</fieldset>';

$checks = '<fieldset>';
$checks .= '<legend>' . __('Checks') . '</legend>';
$checks .= $this->element('check_common_fields');
$checks .= '</fieldset>';

$replies = '<fieldset>';
$replies .= '<legend>' . __('Replies') . '</legend>';
$replies .= $this->element('reply_common_fields');
$replies .= '</fieldset>';

$finish = $this->Form->input('id', array('type' => 'hidden'));
$finish .= $this->Form->input('groupname', array('type' => 'hidden'));

$finish .= $this->Form->end(array(
    'label' => __('Update'),
    'class' => 'next finish',
    'style' => 'display:none;',
));

echo $this->element('wizard', array(
    'steps' => array(
        __('Info') => $info,
        __('Checks') => $checks,
        __('Replies') => $replies,
    ),
    'finishButton' => $finish,
));

?>

