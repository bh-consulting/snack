<?php
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('help_active', 'active');
?>

<h1><?php echo __('Help'); ?></h1>

<div class="tabbable">
    <ul class="nav nav-tabs">
            <li class="<?php echo $this->fetch('help_nasconfig_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('NAS Config'),
                    array('controller' => 'help', 'action' => 'index')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('help_windows_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Windows'),
                    array('controller' => 'help', 'action' => 'windows')
                );
                ?>
            </li>
	    <!-- <li class="<?php echo $this->fetch('help_android_active'); ?>">
                <?php
                /*echo $this->Html->link(
                    __('Android'),
                    array('controller' => 'help', 'action' => 'android')
                );*/
                ?>
            </li>-->
    </ul>
    <div class="tab-content">
        <?php echo $this->fetch('content'); ?>
    </div>
</div>