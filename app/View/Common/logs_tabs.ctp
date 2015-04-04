<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('logs_active', 'active');
?>

<h1><? echo __('Logs'); ?></h1>
<?php
$dir = new Folder('/home/snack/logs');
$files = $dir->find('snacklog.*');
sort($files);
$mainLabelOptions = array('class' => 'label-inline  control-label');
echo $this->Form->create('Loglines', array(
    'action' => 'chooselogfile/'.$this->request['action'],
    'novalidate' => true, 
    'autocomplete' => 'off',
    'class' => 'form-inline',
    'inputDefaults' => array(
        'div' => 'form-group',
        'class' => 'form-control'
    ),
));
//echo $this->Form->create('Loglines', array('action' => 'chooselogfile'));
$myLabelOptions = array('text' => __('Log file'));
echo  $this->Form->input('chooselogfile', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'options' => $files,
    'selected' => array_search($file, $files),
    'empty' => false,
));
$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => 'btn btn-primary',
    'before' => '<div class="col-sm-offset-1 col-sm-2">',
    'after' => '</div>'
);
echo $this->Form->end($options);

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
                    array('controller' => 'loglines', 'action' => 'index', 'file' => $file)
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('snacklogs_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('SNACK'),
                    array('controller' => 'loglines', 'action' => 'snack_logs', 'file' => $file)
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('naslogs_active'); ?>">
                <?php
                echo $this->Html->link(
                        __('NAS'), array('controller' => 'loglines', 'action' => 'nas_logs', 'file' => $file)
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('voicelogs_active'); ?>">
                <?php
                echo $this->Html->link(
                        __('Voice'), array('controller' => 'loglines', 'action' => 'voice_logs', 'file' => $file)
                );
                ?>
            </li>
    </ul>
    <div class="tab-content">
        <?php echo $this->fetch('content'); ?>
    </div>
</div>
