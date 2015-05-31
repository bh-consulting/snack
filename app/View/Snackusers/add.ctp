<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('snackusers_active', 'active');
?>

<h1><?php echo __('Add a SNACK User'); ?></h1>
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
//echo $this->Form->create('Radgroup', array('novalidate' => true));

$myLabelOptions = array('text' => __('Username'));
echo $this->Form->input('username', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$myLabelOptions = array('text' => __('Password'));
echo $this->Form->input('password', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$myLabelOptions = array('text' => __('Confirm Password'));
echo $this->Form->input('confirm_password', array(
	'label' => array_merge($mainLabelOptions, $myLabelOptions),
	'type' => 'password'
));
$mainLabelOptions = array('class' => 'col-sm-4 control-label');
$myLabelOptions = array('text' => __('Role'));
echo $this->Form->input('role', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    //'class' => 'slidermin',
));

echo '<div class="col-sm-2"></div><dl class="col-sm-6 well dl-horizontal">';
echo '<dt>' . __('Tech') . '</dt><dd>' . __('view users, download certificates') . '</dd>';
echo '<dt>' . __('Admin') . '</dt><dd>' . __('view, create, update users') . '</dd>';
echo '<dt>' . __('Root') . '</dt><dd>' . __('view, create, update, delete all objects') . '</dd>';
echo '</dl>';

$options = array(
    'label' => __('Create'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => 'btn btn-primary',
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
);
echo $this->Form->end($options);

?>
