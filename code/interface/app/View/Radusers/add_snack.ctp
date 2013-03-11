<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo '<h1>' . __('Add an admin user') . '</h1>';

echo $this->Form->create('Raduser');

echo '<fieldset>';
echo '<legend>' . __('User info') . '</legend>';
echo $this->element('tab_panes', array(
    'items' => array(
        __('New') => $this->Form->input('username'),
        __('Existing') => $this->Form->input(
            'existing_user',
            array(
                'type' => 'select',
                'options' => $users,
                'empty' => true,
                'label' => __('Existing user')
            )
        ),
    ),
));

echo $this->Form->input('passwd', array('type' => 'password', 'label' => __('Password')));
echo $this->Form->input('confirm_password', array('type' => 'password', 'label' => __('Confirm password')));
echo '</fieldset>';

echo $this->element('snack_role_input');

echo $this->Form->end(__('Create'));
