<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_cluster_active', 'active');

$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Parameter', array(
    'url' => 'edit_cluster',
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

<h4><?php echo __('Cluster configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php
$myLabelOptions = array('text' => __('Role'));
echo  $this->Form->input('role', array(
    'id' => 'role',
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'options' => array(
        'master' => __('Master'),
        'slave' => __('Slave'),
    ),
    //'disabled' => true,
    'empty' => false,
));

$myLabelOptions = array('text' => __('Master IP Address'));
echo $this->Form->input('master_ip', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'readonly' => true
));
$myLabelOptions = array('text' => __('Slave IP to monitor'));
echo $this->Form->input('slave_ip_to_monitor', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
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
