<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('logs_active', 'active');
?>

<h1><? echo __('Logs'); ?></h1>

<div class="tabbable">
    <ul class="nav nav-tabs">
            <li class="<?php echo $this->fetch('radiuslogs_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Radius'),
                    array('controller' => 'loglines', 'action' => 'index')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('snacklogs_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Snack'),
                    array('controller' => 'loglines', 'action' => 'snack_logs')
                );
                ?>
            </li>
    </ul>
    <div class="tab-content">
        <?php echo $this->fetch('content'); ?>
    </div>
</div>
