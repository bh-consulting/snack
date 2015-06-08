<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_email_active', 'active');

$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Parameter', array(
    'action' => 'edit_email',
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
?>

<h4><?php echo __('Email configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php
$myLabelOptions = array('text' => __('SMTP IP Address'));
echo $this->Form->input('smtp_ip', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$myLabelOptions = array('text' => __('SMTP Port'));
echo $this->Form->input('smtp_port', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$myLabelOptions = array('text' => __('SMTP Login'));
echo $this->Form->input('smtp_login', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$myLabelOptions = array('text' => __('SMTP Password'));
echo $this->Form->input('smtp_password', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'type' => 'password',
));
$myLabelOptions = array('text' => __('SMTP Email From'));
echo $this->Form->input('smtp_email_from', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'after'  => '<div class="input-group-addon">@</div></div>',
));
$myLabelOptions = array('text' => __('SMTP Email Dest'));
echo $this->Form->input('configurationEmail', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'after'  => '<div class="input-group-addon">@</div></div>',
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
