<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo '<h1>' . __('Edit') . ' ' . $this->data['Raduser']['username'] . '</h1>';

echo $this->Form->create('Raduser', array('action' => 'edit_snack'));

$userInfo = '<fieldset>';
$userInfo .= '<legend>' . __('User info') . '</legend>';

$userInfo .= $this->Form->input('passwd', array('type' => 'password', 'label' => __('Password')));
$userInfo .= $this->Form->input('confirm_password', array('type' => 'password', 'label' => __('Confirm password')));
$userInfo .= '</fieldset>';

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
        __('User info') => $userInfo,
        __('Role') => $role,
    ),
    'finishButton' => $finish,
));

?>