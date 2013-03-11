<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo '<h1>' . __('Edit') . ' ' . $this->data['Raduser']['username'];

echo $this->Form->create('Raduser', array('action' => 'edit_snack'));

echo '<fieldset>';
echo '<legend>' . __('User info') . '</legend>';

echo $this->Form->input('passwd', array('type' => 'password', 'label' => __('Password')));
echo $this->Form->input('confirm_password', array('type' => 'password', 'label' => __('Confirm password')));
echo '</fieldset>';

echo $this->element('snack_role_input');

echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('username', array('type' => 'hidden'));
echo $this->Form->end(__('Update'));
