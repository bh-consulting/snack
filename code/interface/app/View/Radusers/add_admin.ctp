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
        __('Existing') => $this->Form->input('users', array('type' => 'select')),
    ),
));

echo $this->Form->input('passwd', array('type' => 'password', 'label' => __('Password')));
echo $this->Form->input('confirm_password', array('type' => 'password'));
echo '</fieldset>';

echo '<fieldset>';
echo '<legend>' . __('Admin rights') . '</legend>';
echo $this->Form->input('create_right');
echo $this->Form->input('crud_right');
echo 'Citation CdC : - créer utilisateur
– créer, modifier, supprimer + accès aux certificats';
echo '</fieldset>';

echo $this->Form->end(__('Create'));
