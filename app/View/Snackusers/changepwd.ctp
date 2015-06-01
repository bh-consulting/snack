<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('snackusers_active', 'active');
?>
<h1><?php echo __('Change password'); ?></h1>
<?
$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Snackuser', array(
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

$myLabelOptions = array('text' => __('Password'));
echo $this->Form->input('password', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$myLabelOptions = array('text' => __('Confirm Password'));
echo $this->Form->input('confirm_password', array(
	'label' => array_merge($mainLabelOptions, $myLabelOptions),
	'type' => 'password'
));
$mainLabelOptions = array('class' => 'col-sm-4 control-label');

$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => 'btn btn-primary',
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
);
echo $this->Form->end($options);

?>