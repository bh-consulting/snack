<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

echo '<h1>' . __('Edit NAS') . ' ' . $this->data['Nas']['nasname'] . '</h1>';

$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Nas', array(
    'action' => 'edit',
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

$myLabelOptions = array('text' => __('IP address'));
echo $this->Form->input('nasname', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$myLabelOptions = array('text' => __('Name'));
echo $this->Form->input('shortname', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$myLabelOptions = array('text' => __('Secret key'));
echo $this->Form->input('secret', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$myLabelOptions = array('text' => __('Description'));
echo $this->Form->input('description', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

echo $this->Form->input('backup', array(
    'type' => 'checkbox',
    'between' => '',
    'after'   => '',
    'class' => '', 
    //'before' => '<label class="col-sm-4 control-label">'.__('Check server certificate').'</label><div class="col-sm-1">',
    'before' => '<label class="col-sm-4 control-label">'.__('Must be Backuped ?').'</label><div class="col-sm-1">',
    'between' => '',
    'after'   => '</div>',
    'label' => false,
    //'readonly' => true,
));

if ($backup) {
    $backupattr = false;
} else {
    $backupattr = true;
}
$myLabelOptions = array('text' => __('Login'));
echo $this->Form->input('login', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'readonly' => $backupattr,
));

$myLabelOptions = array('text' => __('Password'));
echo $this->Form->input('password', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'readonly' => $backupattr,
));

$myLabelOptions = array('text' => __('Confirm Password'));
echo $this->Form->input('confirm_password', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'type' => 'password',
    'readonly' => $backupattr,
));

$myLabelOptions = array('text' => __('Enable Password'));
echo $this->Form->input('enablepassword', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'type' => 'password',
    'readonly' => $backupattr,
));

$myLabelOptions = array('text' => __('Confirm Enable Password'));
echo $this->Form->input('confirm_enablepassword', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'type' => 'password',
    'readonly' => $backupattr,
));
echo $this->Form->input('id', array('type' => 'hidden'));

$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => "btn btn-primary",
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
    );
echo $this->Form->end($options);
?>
