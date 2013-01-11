<? 
$this->extend('/Common/radius_sidebar'); 
$this->assign('dashboard_active', 'active');
?>

<div class="pull-right">
<?php
	echo $this->Html->link('<i class="icon-refresh icon-white"></i> ' . __('Refresh'),
		array('controller' => 'systemDetails', 'action' => 'refresh'),
		array('class' => 'btn btn-success btn-large', 'escape' => false)
		);
?>
</div>
<h1><? echo __('Dashboard'); ?></h1>

<h4><? echo __('General information:'); ?></h4>
<p style="padding: 0 0 0 20px;">
	<strong><? echo __('Date:'); ?></strong> <?php echo $curdate; ?></br>
	<strong><? echo __('Hostname:'); ?></strong> <?php echo $hostname; ?>
</p>

<h4><? echo __('Services:'); ?></h4>
<p style="padding: 0 0 0 20px;">
	<strong><? echo __('FreeRadius:'); ?></strong> <?php echo $radiusstate; ?></br>
	<strong><? echo __('MySQL:'); ?></strong> <?php echo $mysqlstate; ?>
</p>

<h4><? echo __('Statistics:'); ?></h4>
<p style="padding: 0 0 0 20px;">
	<strong><? echo __('Uptime:'); ?></strong> <?php echo $uptime; ?></br>
	<strong><? echo __('Idle time:'); ?></strong> <?php echo $idletime; ?></br>
	<strong><? echo __('Load average:') ?></strong> <?php echo $loadavg; ?></br>
	<strong><? echo __('Tasks:'); ?></strong> <?php echo $tasks; ?>
</p>

<h4><? echo __('Memory:'); ?></h4>
<p style="padding: 0 0 0 20px;">
	<strong><? echo __('Used memory:'); ?></strong> <?php echo $usedmem; ?></br>
	<strong><? echo __('Free memory:'); ?></strong> <?php echo $freemem; ?></br>
	<strong><? echo __('Total memory:'); ?></strong> <?php echo $totalmem; ?></br>
	<strong><? echo __('Used disk:'); ?></strong> <?php echo $useddisk; ?></br>
	<strong><? echo __('Free disk:'); ?></strong> <?php echo $freedisk; ?></br>
	<strong><? echo __('Total disk:'); ?></strong> <?php echo $totaldisk; ?>
</p>

<h4><? echo __('Network:'); ?></h4>
<table class="table">
    <thead>
    <tr>
        <th><? echo __('Interface'); ?></th>
        <th><? echo __('MAC address'); ?></th>
        <th><? echo __('IPv4 adresses'); ?></th>
        <th><? echo __('IPv6 adresses'); ?></th>
    </tr>
    </thead>
	<? if(!empty($ints)){ ?>
		<tbody>
		<? foreach ($ints as $int): ?>
			<tr>
				<th><? echo $int['name']; ?></th>
				<td><? echo $int['mac']; ?></td>
				<td>
				<?
					if( array_key_exists( "ipv4", $int) )
						for($i=0; $i<count($int['ipv4']); ++$i)
							echo $int['ipv4'][$i] . "</br>";
					else
						echo __("No IPv4 address.");
				?>
				</td>
				<td>
				<?
					if( array_key_exists( "ipv6", $int) )
						for($i=0; $i<count($int['ipv6']); ++$i)
							echo $int['ipv6'][$i] . "</br>";
					else
						echo __("No IPv6 address.");
				?>
				</td>
			</tr>
		<?
			endforeach;
			unset($ints);
		?>
		</tbody>
	<? }else{ ?>
		<tbody>
    	<tr>
				<td colspan="17"><? echo __('No interface'); ?></td>
			</tr>
		</tbody>
	<? } ?>
</table>

<table class="table">
	<thead>
		<tr>
			<th rowspan="2"><? echo __('Interface'); ?></th>
			<th colspan="8" style="text-align:center;"><? echo __('Receive'); ?></th>
		</tr>
		<tr>
			<th><? echo __('bytes'); ?></th>
			<th><? echo __('packets'); ?></th>
			<th><? echo __('errors'); ?></th>
			<th><? echo __('drop'); ?></th>
			<th><? echo __('fifo'); ?></th>
			<th><? echo __('frame'); ?></th>
			<th><? echo __('compressed'); ?></th>
			<th><? echo __('multicast'); ?></th>
		</tr>
	</thead>
	<? if(!empty($intstats)){ ?>
	<tbody>
		<? foreach ($intstats as $int): ?>
		<tr>
			<?
			echo "<th>" . $int[0] . "</th>"; 
			
			for($i=1; $i<9; ++$i)
				echo "<td>" . $int[$i] . "</td>"; 
			?>
		</tr>
		<?
		endforeach;
		?>
	</tbody>
	<? }else{ ?>
	<tbody>
		<tr>
			<td colspan="9"><? echo __('No interface'); ?></td>
		</tr>
	</tbody>
	<? } ?>
</table>
<table class="table">
	<thead>
		<tr>
			<th rowspan="2"><? echo __('Interface'); ?></th>
			<th colspan="8" style="text-align:center;"><? echo __('Transmit'); ?></th>
		</tr>
		<tr>
			<th><? echo __('bytes'); ?></th>
			<th><? echo __('packets'); ?></th>
			<th><? echo __('errors'); ?></th>
			<th><? echo __('drop'); ?></th>
			<th><? echo __('fifo'); ?></th>
			<th><? echo __('collisions'); ?></th>
			<th><? echo __('carrier'); ?></th>
			<th><? echo __('compressed'); ?></th>
		</tr>
	</thead>
	<? if(!empty($intstats)){ ?>
	<tbody>
		<? foreach ($intstats as $int): ?>
		<tr>
			<?
			echo "<th>" . $int[0] . "</th>"; 
			
			for($i=9; $i<17; ++$i)
				echo "<td>" . $int[$i] . "</td>"; 
			?>
		</tr>
		<?
		endforeach;
		?>
	</tbody>
	<? }else{ ?>
	<tbody>
		<tr>
			<td colspan="9"><? echo __('No interface'); ?></td>
		</tr>
	</tbody>
	<? } ?>
</table>
