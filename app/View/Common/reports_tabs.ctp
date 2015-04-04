<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('reports_active', 'active');
?>

<h1><? echo __('Reports'); ?></h1>
<div class="tabbable">
    <ul class="nav nav-tabs">
            <li class="<?php echo $this->fetch('reportsgeneral_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('General'),
                    array('controller' => 'reports', 'action' => 'index')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('errorsreports_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Errors'),
                    array('controller' => 'reports', 'action' => 'errors_reports')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('voicereports_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Voice'),
                    array('controller' => 'reports', 'action' => 'voice_reports')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('sessionsreports_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Sessions'),
                    array('controller' => 'reports', 'action' => 'sessions_reports')
                );
                ?>
            </li>
    </ul>
    <div class="tab-content">
        <?php echo $this->fetch('content'); ?>
    </div>
</div>