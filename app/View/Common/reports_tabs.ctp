<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('reports_active', 'active');
?>

<h1><?php echo __('Reports'); ?></h1>
<div class="tabbable">
    <ul class="nav nav-tabs">
            <li class="<?php echo $this->fetch('errorsfromradiusreports_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Errors from Radius'),
                    array('controller' => 'reports', 'action' => 'errorsfromradius_reports')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('errorsfromnasreports_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Errors from NAS'),
                    array('controller' => 'reports', 'action' => 'errorsfromnas_reports')
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