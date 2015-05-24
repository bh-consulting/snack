<?php
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('param_active', 'active');
?>

<h1><?php echo __('Server parameters'); ?></h1>

<div class="tabbable">
    <ul class="nav nav-tabs">
            <li class="<?php echo $this->fetch('param_general_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('General'),
                    array('controller' => 'parameters', 'action' => 'index')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('param_email_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Email'),
                    array('controller' => 'parameters', 'action' => 'email')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('param_proxy_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Proxy'),
                    array('controller' => 'parameters', 'action' => 'proxy')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('param_cluster_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Cluster'),
                    array('controller' => 'parameters', 'action' => 'cluster')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('param_ad_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('ActiveDirectory'),
                    array('controller' => 'parameters', 'action' => 'ad')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('param_cron_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Cron'),
                    array('controller' => 'parameters', 'action' => 'cron')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('param_nagios_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Nagios'),
                    array('controller' => 'parameters', 'action' => 'nagios')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('param_logs_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Logs'),
                    array('controller' => 'parameters', 'action' => 'logs')
                );
                ?>
            </li>
    </ul>
    <div class="tab-content">
        <?php echo $this->fetch('content'); ?>
    </div>
</div>