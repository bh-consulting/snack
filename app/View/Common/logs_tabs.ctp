<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('logs_active', 'active');
?>

<h1><? echo __('Logs'); ?></h1>
<?php

echo $this->Form->create();
echo '<div class="col-sm-12"><b>Disable refresh </b>';
echo '<input type="checkbox" name="data[Radacct][cisco]" class="" value="1" id="LoglineAjax" />';
echo '</div><br>';
echo $this->Form->end();
?>
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
                    __('SNACK'),
                    array('controller' => 'loglines', 'action' => 'snack_logs')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('naslogs_active'); ?>">
                <?php
                echo $this->Html->link(
                        __('NAS'), array('controller' => 'loglines', 'action' => 'nas_logs')
                );
                ?>
            </li>
            <?php
            if($this->Session->read('Auth.User')['role'] == "root"){
                echo "<li class=".$this->fetch('voicelogs_active').">";
                echo $this->Html->link(
                        __('Voice'), array('controller' => 'loglines', 'action' => 'voice_logs')
                );
                echo "</li>";
            }
            ?>
    </ul>
    <div class="tab-content">
        <?php echo $this->fetch('content'); ?>
    </div>
</div>
