<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_proxy_active', 'active');

$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Parameter', array(
    'action' => 'edit_proxy',
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

<br>

<h4><?php echo __('Proxy configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php
$myLabelOptions = array('text' => __('Proxy IP Address'));
echo $this->Form->input('proxy_ip', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$myLabelOptions = array('text' => __('Proxy Port'));
echo $this->Form->input('proxy_port', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$myLabelOptions = array('text' => __('Proxy Login'));
echo $this->Form->input('proxy_login', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$myLabelOptions = array('text' => __('Proxy Password'));
echo $this->Form->input('proxy_password', array(
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
