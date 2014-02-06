<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('dashboard_active', 'active');
?>

<div class="pull-right">
    <?php
    echo $this->Html->link(
            '<i class="glyphicon glyphicon-refresh glyphicon-white"></i> ' . __('Refresh'), array('controller' => 'systemDetails', 'action' => 'refresh'), array('class' => 'btn btn-success btn-large', 'escape' => false)
    );
    ?>
</div>

<h1><?php echo __('Dashboard'); ?></h1>

<!-- /.row -->
<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <?php echo __('General Information'); ?>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('Date'); ?></dt>
                    <dd><?php echo $curdate; ?></dd>
                    <dt><?php echo __('Hostname'); ?></dt>
                    <dd><?php echo $hostname; ?></dd>
                    <dt><?php echo __('CA link'); ?></dt>
                    <dd>
                        <?php
                        echo $this->Html->link(
                                Utils::getServerCertPath(), array(
                            'action' => 'get_cert/server',
                            'controller' => 'certs',
                                )
                        );
                        ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <?php echo __('Statistics'); ?>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('Uptime'); ?></dt>
                    <dd><?php echo $uptime; ?></dd>
                    <dt><?php echo __('Idle time'); ?></dt>
                    <dd><?php echo $idletime; ?></dd>
                    <dt><?php echo __('Load average') ?></dt>
                    <dd><?php echo $loadavg; ?></dd>
                    <dt><?php echo __('Tasks'); ?></dt>
                    <dd><?php echo $tasks; ?></dd>
                </dl>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <?php echo __('Memory'); ?>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('Cpu')." : ".$cpu." %"; ?></dt>
                    <dd><div class="progress">
                            <?php
                            if ($cpu<80) {
                                echo '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$cpu.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$cpu.'%;">';                          
                            }
                            else if ($cpu>=80 && $cpu<90) {
                                echo '<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="'.$cpu.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$cpu.'%;">';                           
                            }
                            else{
                                echo '<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="'.$cpu.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$cpu.'%;">';
                            }
                            ?>
                            
                                <span class="sr-only">0% Complete</span>
                            </div>
                        </div>
                    </dd>
                    <dt><?php echo __('Memory')." : ".round($usedmem/$totalmem*100,1) ." %"; ?></dt>
                    <dd><div class="progress">
                            <?php
                            $memusepercent=round($usedmem/$totalmem*100,1);
                            if ($memusepercent<80) {
                                echo '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$memusepercent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$memusepercent.'%;">';                          
                            }
                            else if ($memusepercent>=80 && $memusepercent<90) {
                                echo '<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="'.$memusepercent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$memusepercent.'%;">';                           
                            }
                            else{
                                echo '<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="'.$memusepercent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$memusepercent.'%;">';
                            }
                            ?>
                            
                                <span class="sr-only">0% Complete</span>
                            </div>
                        </div>
                    </dd>
                    <dt><?php echo __('Disk')." : ".round($useddisk/$totaldisk*100,1) ." %"; ?></dt>
                    <dd><div class="progress">
                            <?php
                            $diskusepercent=round($useddisk/$totaldisk*100,1);
                            if ($diskusepercent<80) {
                                echo '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$diskusepercent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$diskusepercent.'%;">';                          
                            }
                            else if ($diskusepercent>=80 && $diskusepercent<90) {
                                echo '<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="'.$diskusepercent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$diskusepercent.'%;">';                           
                            }
                            else{
                                echo '<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="'.$diskusepercent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$diskusepercent.'%;">';
                            }
                            ?>
                            
                                <span class="sr-only">0% Complete</span>
                            </div>
                        </div>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <!-- /.col-lg-4 -->
    <div class="col-lg-8">
        <div class="panel panel-primary">
            <div class="panel-heading">
                 <?php echo __('Services'); ?>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('Freeradius'); ?></dt>
                    <dd>
                        <?php
                        echo $radiusstate;
                        if (AuthComponent::user('role') == 'root') {
                            echo $this->Html->link(
                                    '<i class="glyphicon glyphicon-refresh glyphicon-white"></i> ' . __('Restart Freeradius'), array(
                                'action' => 'restart',
                                'freeradius'
                                    ), array(
                                'class' => 'btn btn-mini btn-danger',
                                'style' => 'margin-left:30px',
                                'escape' => false
                                    )
                            );
                        }
                        ?>
                    </dd>
                    <dt><?php echo __('MySQL'); ?></dt>
                    <dd>
                        <?php
                        echo $mysqlstate;
                        if (AuthComponent::user('role') == 'root') {
                            echo $this->Html->link(
                                    '<i class="glyphicon glyphicon-refresh glyphicon-white"></i> ' . __('Restart Mysql'), array(
                                'action' => 'restart',
                                'mysql',
                                    ), array(
                                'class' => 'btn btn-mini btn-danger',
                                'style' => 'margin-left:30px',
                                'escape' => false
                                    )
                            );
                        }
                        ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <!--<div class="col-lg-4">
        <div class="panel panel-primary">
            <div class="demo-container">
                <div id="placeholder" class="demo-placeholder"></div>
            </div>
        </div>
    </div>-->

    <!-- 	
<div class="col-lg-4">
 <div class="demo-container">
     <div id="placeholder" class="demo-placeholder"></div>
 </div>
</div>

    <div class="col-lg-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-bell fa-fw"></i> Notifications Panel
            </div>

            <div class="panel-body">
                <div class="list-group">
                    <a href="#" class="list-group-item">
                        <i class="fa fa-comment fa-fw"></i> New Comment
                        <span class="pull-right text-muted small"><em>4 minutes ago</em>
                        </span>
                    </a>
                    <a href="#" class="list-group-item">
                        <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                        <span class="pull-right text-muted small"><em>12 minutes ago</em>
                        </span>
                    </a>
                    <a href="#" class="list-group-item">
                        <i class="fa fa-envelope fa-fw"></i> Message Sent
                        <span class="pull-right text-muted small"><em>27 minutes ago</em>
                        </span>
                    </a>
                    <a href="#" class="list-group-item">
                        <i class="fa fa-tasks fa-fw"></i> New Task
                        <span class="pull-right text-muted small"><em>43 minutes ago</em>
                        </span>
                    </a>
                    <a href="#" class="list-group-item">
                        <i class="fa fa-upload fa-fw"></i> Server Rebooted
                        <span class="pull-right text-muted small"><em>11:32 AM</em>
                        </span>
                    </a>
                    <a href="#" class="list-group-item">
                        <i class="fa fa-bolt fa-fw"></i> Server Crashed!
                        <span class="pull-right text-muted small"><em>11:13 AM</em>
                        </span>
                    </a>
                    <a href="#" class="list-group-item">
                        <i class="fa fa-warning fa-fw"></i> Server Not Responding
                        <span class="pull-right text-muted small"><em>10:57 AM</em>
                        </span>
                    </a>
                    <a href="#" class="list-group-item">
                        <i class="fa fa-shopping-cart fa-fw"></i> New Order Placed
                        <span class="pull-right text-muted small"><em>9:49 AM</em>
                        </span>
                    </a>
                    <a href="#" class="list-group-item">
                        <i class="fa fa-money fa-fw"></i> Payment Received
                        <span class="pull-right text-muted small"><em>Yesterday</em>
                        </span>
                    </a>
                </div>
                <a href="#" class="btn btn-default btn-block">View All Alerts</a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <?php echo __('Network'); ?>
            </div>
            <div class="panel-body">
                <div id="placeholder" style="width:600px;height:300px;"></div>
            </div>
        </div>
    </div>-->
    
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <?php echo __('Network'); ?>
            </div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo __('Interface'); ?></th>
                            <th><?php echo __('MAC address'); ?></th>
                            <th><?php echo __('IPv4 adresses'); ?></th>
                            <th><?php echo __('IPv6 adresses'); ?></th>
                        </tr>
                    </thead>
                    <?php
                    if (!empty($ints)) {
                        ?>
                        <tbody>
                            <?php
                            foreach ($ints as $int) {
                                ?>
                                <tr>
                                    <th><?php echo $int['name']; ?></th>
                                    <td><?php echo $int['mac']; ?></td>
                                    <td>
                                        <?php
                                        if (array_key_exists("ipv4", $int)) {
                                            for ($i = 0; $i < count($int['ipv4']); ++$i) {
                                                echo $int['ipv4'][$i] . "</br>";
                                            }
                                        } else {
                                            echo __("No IPv4 address.");
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (array_key_exists("ipv6", $int)) {
                                            for ($i = 0; $i < count($int['ipv6']); ++$i) {
                                                echo $int['ipv6'][$i] . "</br>";
                                            }
                                        } else {
                                            echo __("No IPv6 address.");
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            unset($ints);
                            ?>
                        </tbody>
                        <?php
                    } else {
                        ?>
                        <tbody>
                            <tr>
                                <td colspan="17"><?php echo __('No interface'); ?></td>
                            </tr>
                        </tbody>
                        <?php
                    }
                    ?>
                </table>

                <table class="table">
                    <thead>
                        <tr>
                            <th rowspan="2"><?php echo __('Interface'); ?></th>
                            <th colspan="8" style="text-align:center;">
                                <?php
                                echo __('Receive');
                                ?>
                            </th>
                        </tr>
                        <tr>
                            <th><?php echo __('bytes'); ?></th>
                            <th><?php echo __('packets'); ?></th>
                            <th><?php echo __('errors'); ?></th>
                            <th><?php echo __('drop'); ?></th>
                            <th><?php echo __('fifo'); ?></th>
                            <th><?php echo __('frame'); ?></th>
                            <th><?php echo __('compressed'); ?></th>
                            <th><?php echo __('multicast'); ?></th>
                        </tr>
                    </thead>
                    <?php
                    if (!empty($intstats)) {
                        ?>
                        <tbody>
                            <?php
                            foreach ($intstats as $int) {
                                ?>
                                <tr>
                                    <?php
                                    echo "<th>" . $int[0] . "</th>";

                                    for ($i = 1; $i < 9; ++$i)
                                        echo "<td>" . $int[$i] . "</td>";
                                    ?>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <?php
                    } else {
                        ?>
                        <tbody>
                            <tr>
                                <td colspan="9"><?php echo __('No interface'); ?></td>
                            </tr>
                        </tbody>
                        <?php
                    }
                    ?>
                </table>
                <table class="table">
                    <thead>
                        <tr>
                            <th rowspan="2"><?php echo __('Interface'); ?></th>
                            <th colspan="8" style="text-align:center;">
                                <?php
                                echo __('Transmit');
                                ?>
                            </th>
                        </tr>
                        <tr>
                            <th><?php echo __('bytes'); ?></th>
                            <th><?php echo __('packets'); ?></th>
                            <th><?php echo __('errors'); ?></th>
                            <th><?php echo __('drop'); ?></th>
                            <th><?php echo __('fifo'); ?></th>
                            <th><?php echo __('collisions'); ?></th>
                            <th><?php echo __('carrier'); ?></th>
                            <th><?php echo __('compressed'); ?></th>
                        </tr>
                    </thead>
                    <?php
                    if (!empty($intstats)) {
                        ?>
                        <tbody>
                            <?php
                            foreach ($intstats as $int) {
                                ?>
                                <tr>
                                    <?php
                                    echo "<th>" . $int[0] . "</th>";

                                    for ($i = 9; $i < 17; ++$i)
                                        echo "<td>" . $int[$i] . "</td>";
                                    ?>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <?php
                    } else {
                        ?>
                        <tbody>
                            <tr>
                                <td colspan="9"><?php echo __('No interface'); ?></td>
                            </tr>
                        </tbody>
                        <?php
                    }
                    ?>
                </table>
            </div>
            <div class="panel-footer">
            </div>
        </div>
    </div>
    
    
</div>


