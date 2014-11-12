<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_nagios_active', 'active');
?>

<br>

<?php
echo $this->Html->link(
        '<i class="glyphicon glyphicon-wrench glyphicon glyphicon-white"></i> ' . __('Edit email parameters'), array('controller' => 'parameters', 'action' => 'edit_nagios'), array('escape' => false, 'class' => 'btn btn-primary')
);
?>

<h4><?php echo __('Nagios configuration:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('Nagios IP Address'); ?></dt>
    <dd>
<?php
echo empty($nagios_ip) ? __('Not set.') : $nagios_ip;
?>
    </dd>
    <dt><?php echo __('Password'); ?></dt>
    <dd>
        <?php
        echo empty($nagios_password) ? __('Not set.') : "******";
        ?>
    </dd>
</dl>

