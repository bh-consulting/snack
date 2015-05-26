<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_logs_active', 'active');
?>
<br>

<?php
echo $this->Html->link(
        '<i class="glyphicon glyphicon-wrench glyphicon glyphicon-white"></i> ' . __('Edit Logs parameters'), array('controller' => 'parameters', 'action' => 'edit_logs'), array('escape' => false, 'class' => 'btn btn-primary')
);
?>

<h4><?php echo __('Logs configuration:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('Archive older than'); ?></dt>
    <dd>
		<?php
		echo empty($logs_archive_date) ? __('Not set.') : $logs_archive_date . " days";
		?>
    </dd>
    <dt><?php echo __('Delete older than'); ?></dt>
    <dd>
        <?php
        echo empty($logs_delete_date) ? __('Not set.') : $logs_delete_date . " days";
        ?>
    </dd>
</dl>
