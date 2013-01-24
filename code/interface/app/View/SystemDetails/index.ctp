<?php
$this->extend('/Common/radius_sidebar'); 
$this->assign('dashboard_active', 'active');
?>

<div class="pull-right">
<?php
echo $this->Html->link(
    '<i class="icon-refresh icon-white"></i> ' . __('Refresh'),
    array('controller' => 'systemDetails', 'action' => 'refresh'),
    array('class' => 'btn btn-success btn-large', 'escape' => false)
);
?>
</div>

<h1><?php echo __('Dashboard'); ?></h1>

<h4><?php echo __('General information:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('Date'); ?></dt>
    <dd><?php echo $curdate; ?></dd>
    <dt><?php echo __('Hostname'); ?></dt>
    <dd><?php echo $hostname; ?></dd>
</dl>

<h4><?php echo __('Services:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('Freeradius'); ?></dt>
    <dd><?php echo $radiusstate; ?></dd>
    <dt><?php echo __('MySQL'); ?></dt>
    <dd><?php echo $mysqlstate; ?></dd>
</dl>

<h4><?php echo __('Statistics:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('Uptime'); ?></dt>
    <dd><?php echo $uptime; ?></dd>
    <dt><?php echo __('Idle time'); ?></dt>
    <dd><?php echo $idletime; ?></dd>
    <dt><?php echo __('Load average') ?></dt>
    <dd><?php echo $loadavg; ?></dd>
    <dt><?php echo __('Tasks'); ?></dt>
    <dd><?php echo $tasks; ?></dd>
</dl>

<h4><?php echo __('Memory:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('Used memory'); ?></dt>
    <dd><?php echo $usedmem; ?></dd>
    <dt><?php echo __('Free memory'); ?></dt>
    <dd><?php echo $freemem; ?></dd>
    <dt><?php echo __('Total memory'); ?></dt>
    <dd><?php echo $totalmem; ?></dd>
    <dt><?php echo __('Used disk'); ?></dt>
    <dd><?php echo $useddisk; ?></dd>
    <dt><?php echo __('Free disk'); ?></dt>
    <dd><?php echo $freedisk; ?></dd>
    <dt><?php echo __('Total disk'); ?></dt>
    <dd><?php echo $totaldisk; ?></dd>
</dl>

<h4><?php echo __('Network:'); ?></h4>
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
	if( array_key_exists( "ipv4", $int) ) {
	    for($i=0; $i<count($int['ipv4']); ++$i) {
		echo $int['ipv4'][$i] . "</br>";
	    }
	} else {
	    echo __("No IPv4 address.");
	}
?>
	    </td>
	    <td>
<?php
	if( array_key_exists( "ipv6", $int) ) {
	    for($i=0; $i<count($int['ipv6']); ++$i) {
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

	for($i=1; $i<9; ++$i)
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

	for($i=9; $i<17; ++$i)
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
