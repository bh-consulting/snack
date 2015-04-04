<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_nagios_active', 'active');

$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Parameter', array(
    'action' => 'edit_nagios',
    'novalidate' => true, 
    'autocomplete' => 'off',
    'class' => 'form-horizontal',
    'inputDefaults' => array(
        'div' => 'form-group',
        'label' => array(
            'class' => $mainLabelOptions
        ),
        'between' => '<div class="col-sm-4 input-group">',
        'after'   => '</div>',
        'class' => 'form-control'
    ),
));
//echo $this->Form->create('Parameter', array('action' => 'edit_nagios', 'autocomplete' => 'off'));
?>

<h4><?php echo __('Nagios configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php
$myLabelOptions = array('text' => __('Nagios IP Address'));
echo $this->Form->input('nagios_ip', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
));
$myLabelOptions = array('text' => __('Nagios Password'));
echo $this->Form->input('nagios_password', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'type' => 'password',
));
?>
</dl>

<?php
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
