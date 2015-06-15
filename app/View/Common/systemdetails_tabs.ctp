<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('dashboard_active', 'active');
?>

<div class="pull-right">
    <?php
    echo $this->Html->link(
        '<i class="glyphicon glyphicon-send glyphicon-white"></i>&nbsp;&nbsp;' . __('Send Configuration'), array('controller' => 'systemDetails', 'action' => 'send_backup'), array('class' => 'btn btn-success btn-large', 'escape' => false)
    );
    echo $this->Html->link('<i class="glyphicon glyphicon-open glyphicon-white"></i> ' . __('Import'), '#confirmimport',
        array(
            'class' => 'btn btn-success btn-large',
            'escape' => false,
            'data-toggle' => 'modal',
        )
    );
    echo $this->Html->link(
        '<i class="glyphicon glyphicon-save glyphicon-white"></i> ' . __('Export'), array('controller' => 'systemDetails', 'action' => 'export'), array('class' => 'btn btn-danger btn-large', 'escape' => false)
    );
    echo $this->Html->link(
        '<i class="glyphicon glyphicon-refresh glyphicon-white"></i> ' . __('Check Updates'), array('controller' => 'systemDetails', 'action' => 'checkupdates'), array('class' => 'btn btn-warning btn-large', 'escape' => false)
    );
    echo $this->Html->link(
        '<i class="glyphicon glyphicon-refresh glyphicon-white"></i> ' . __('Refresh'), array('controller' => 'systemDetails', 'action' => 'refresh'), array('class' => 'btn btn-success btn-large', 'escape' => false)
    );
    ?>
</div>

<?php
echo '<div id="modalimportConf">';
echo $this->element('modalImportConf', array(
    'id'   => 'import',
    'url' => array(
        'controller' => 'SystemDetails',
        'action' => 'import',
    ),
    'link' => $this->Html->link(
        '<i class="glyphicon glyphicon-upload glyphicon-white"></i> ' . __('Upload'),
        array(
            'controller' => 'SystemDetails',
            'action' => 'import',
        ),
        array(
            'escape' => false,
            'class'  => 'btn btn-primary'
        )
    )
));
echo '</div>';
?>

<h1><?php echo __('Dashboard'); ?></h1>

<div class="tabbable">
    <ul class="nav nav-tabs">
            <li class="<?php echo $this->fetch('systemdetails_general_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('General'),
                    array('controller' => 'SystemDetails', 'action' => 'index')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('systemdetails_backup_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Backup'),
                    array('controller' => 'SystemDetails', 'action' => 'backup')
                );
                ?>
            </li>
            <?php
            if (Configure::read('Parameters.role')=="master") {
            echo "<li class=".$this->fetch('systemdetails_ha_active').">";
                echo $this->Html->link(
                    __('HA'),
                    array('controller' => 'SystemDetails', 'action' => 'ha')
                );            
            echo "</li>";
            }
            ?>
            <?php
            echo "<li class=".$this->fetch('systemdetails_tests_active').">";
                echo $this->Html->link(
                    __('Tests'),
                    array('controller' => 'SystemDetails', 'action' => 'tests'),
                    array('onclick'=>'loading()')
                );            
            echo "</li>";
            ?>
            <?php
            echo "<li class=".$this->fetch('systemdetails_notifications_active').">";
                echo $this->Html->link(
                    __('Notifications'),
                    array('controller' => 'SystemDetails', 'action' => 'notifications')
                );            
            echo "</li>";
            ?>
            <?php
            echo "<li class=".$this->fetch('systemdetails_upgrade_active').">";
                echo $this->Html->link(
                    __('Upgrade'),
                    array('controller' => 'SystemDetails', 'action' => 'upgrade')
                );            
            echo "</li>";
            ?>
    </ul>
    <div class="tab-content">
        <?php echo $this->fetch('content'); ?>
    </div>
</div>