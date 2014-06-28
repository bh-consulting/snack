<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_cluster_active', 'active');
?>

<br>

<?php
echo $this->Html->link(
        '<i class="glyphicon glyphicon-wrench glyphicon glyphicon-white"></i> ' . __('Edit cluster parameters'), array('controller' => 'parameters', 'action' => 'edit_cluster'), array('escape' => false, 'class' => 'btn btn-primary')
);
?>

<h4><?php echo __('Cluster configuration:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('Role'); ?></dt>
    <dd>
<?php
echo empty($role) ? __('Not set.') : $role;
?>
    </dd>
    <dt><?php echo __('Master IP Address'); ?></dt>
    <dd>
        <?php
        echo empty($master_ip) ? __('Not set.') : $master_ip;
        ?>
    </dd>
    <dt><?php echo __('Slave IP to monitor'); ?></dt>
    <dd>
        <?php
        echo empty($slave_ip_to_monitor) ? __('Not set.') : $slave_ip_to_monitor;
        ?>
    </dd>
</dl>