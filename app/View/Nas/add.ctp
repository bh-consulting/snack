<?php

$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');
?>

<h1><? echo __('Add a NAS'); ?></h1>

<?php
echo $this->Form->create('Nas', array('autocomplete' => 'off'));
echo $this->Form->input('nasname', array('label' => __('IP address'))); 
echo $this->Form->input('shortname', array('label' => __('Name')));
echo $this->Form->input('secret', array('label' => __('Secret key')));
echo $this->Form->input('description');
echo $this->Form->input('login', array('label' => __('Login')));
echo $this->Form->input('password', array('label' => __('Password')));
echo $this->Form->input('confirm_password', array('type' => 'password', 'label' => __('Confirm Password')));
echo $this->Form->input('enablepassword', array('type' => 'password', 'label' => __('Enable Password')));
echo $this->Form->input('confirm_enablepassword', array('type' => 'password', 'label' => __('Confirm Enable Password')));


$options = array(
    'label' => __('Create'),
    'div' => array(
        'class' => 'form-group',
    ),
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
    );
echo $this->Form->end($options);
?>
