<?php

$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');
?>

<h1><? echo __('Add a NAS'); ?></h1>

<?php
echo $this->Form->create('Nas');
echo $this->Form->input('nasname', array('label' => __('IP address'))); 
echo $this->Form->input('shortname', array('label' => __('Name')));
echo $this->Form->input('secret', array('label' => __('Secret key')));
echo $this->Form->input('description');

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
