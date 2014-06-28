<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_cluster_active', 'active');

echo $this->Form->create('Parameter', array('action' => 'edit_cluster'));
?>

<br>

<h4><?php echo __('Cluster configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php 
echo $this->Form->input('role', array(
    'class' => 'role',
    'options' => array(
        'master' => __('Master'),
        'slave' => __('Slave'),
    ),
    //'disabled' => true,
    'empty' => false,
    'label' => __('Role'),
));
echo $this->Form->input('master_ip', array('label' => __('Master IP Address'), 'readonly' => true));
echo $this->Form->input('slave_ip_to_monitor', array('label' => __('Slave IP to monitor')));
?>
</dl>

<?php
$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
);
echo $this->Form->end($options);
?>
