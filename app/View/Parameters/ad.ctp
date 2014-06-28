<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_ad_active', 'active');
?>

<br>

<?php
echo $this->Html->link(
        '<i class="glyphicon glyphicon-wrench glyphicon glyphicon-white"></i> ' . __('Edit AD parameters'), array('controller' => 'parameters', 'action' => 'edit_ad'), array('escape' => false, 'class' => 'btn btn-primary')
);
?>

<h4><?php echo __('ActiveDirectory configuration:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('Status'); ?></dt>
    <dd>
        <?php
        if (empty($adstatus)) {
            echo __('Not set.');
        }
        elseif(preg_match("/^Joined .*/", $adstatus, $matches)) {
            echo "<b><font color='green'>".$adstatus."</font></b>";
        }
        else {
            echo "<b><font color='red'>".$adstatus."</font></b>";
        }
        ?>
    </dd>
    <dt><?php echo __('Domain'); ?></dt>
    <dd>
        <?php
        echo empty($addomain) ? __('Not set.') : $addomain;
        ?>
    </dd>
    <dt><?php echo __('Active Directory IP'); ?></dt>
    <dd>
        <?php
        echo empty($adip) ? __('Not set.') : $adip;
        ?>
    </dd>
</dl>

<?php
echo $this->Html->link(
        '<i class="glyphicon glyphicon-wrench glyphicon glyphicon-white"></i> ' . __('Edit group parameters'), array('controller' => 'parameters', 'action' => 'edit_ad_group'), array('escape' => false, 'class' => 'btn btn-primary')
);
?>
<br><br>
<dl class="well dl-horizontal">
    <dt><?php echo __('Group Synchronization'); ?></dt>
    <dd>
        <?php
        echo empty($adgroupsync) ? __('Not set.') : $adgroupsync;
        ?>
    </dd>
</dl>