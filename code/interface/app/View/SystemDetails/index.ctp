<? 
$this->extend('/Common/radius_sidebar'); 
$this->assign('dashboard_active', 'active');
?>

<h1>Dashboard</h1>

<h4>General information:</h4>
<p style="padding: 0 0 0 20px;">
	<strong>Date:</strong> <?php echo $curdate; ?></br>
	<strong>Hostname:</strong> <?php echo $hostname; ?>
</p>

<h4>Services:</h4>
<p style="padding: 0 0 0 20px;">
	<strong>FreeRadius:</strong> <?php echo $radiusstate; ?></br>
	<strong>MySql:</strong> <?php echo $mysqlstate; ?>
</p>

<h4>Statistics:</h4>
<p style="padding: 0 0 0 20px;">
	<strong>Up time:</strong> <?php echo $uptime; ?></br>
	<strong>Idle time:</strong> <?php echo $idletime; ?></br>
	<strong>Load average:</strong> <?php echo $loadavg; ?></br>
	<strong>Tasks:</strong> <?php echo $tasks; ?>
</p>

<h4>Memory:</h4>
<p style="padding: 0 0 0 20px;">
	<strong>Used memory:</strong> <?php echo $usedmem; ?></br>
	<strong>Free memory:</strong> <?php echo $freemem; ?></br>
	<strong>Total memory:</strong> <?php echo $totalmem; ?></br></br>
	<strong>Used disk:</strong> <?php echo $useddisk; ?></br>
	<strong>Free disk:</strong> <?php echo $freedisk; ?></br>
	<strong>Total disk:</strong> <?php echo $totaldisk; ?>
</p>

<h4>Network:</h4>
<table class="table">
    <thead>
    <tr>
        <th>Interface</th>
        <th>MAC address</th>
        <th>IPv4 adresses</th>
        <th>IPv6 adresses</th>
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
						echo "No IPv4 address.";
				?>
				</td>
				<td>
				<?
					if( array_key_exists( "ipv6", $int) )
						for($i=0; $i<count($int['ipv6']); ++$i)
							echo $int['ipv6'][$i] . "</br>";
					else
						echo "No IPv6 address.";
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
				<td colspan="17">No interface</td>
			</tr>
		</tbody>
	<? } ?>
</table>

<table class="table">
    <thead>
    <tr>
        <th rowspan="2">Interface</th>
        <th colspan="8">Receive</th>
        <th colspan="8">Transmit</th>
    </tr>
    <tr>
        <th>bytes</th>
        <th>packets</th>
        <th>errors</th>
        <th>drop</th>
        <th>fifo</th>
        <th>frame</th>
        <th>compressed</th>
        <th>multicast</th>
        <th>bytes</th>
        <th>packets</th>
        <th>errors</th>
        <th>drop</th>
        <th>fifo</th>
        <th>collisions</th>
        <th>carrier</th>
        <th>compressed</th>
    </tr>
    </thead>
	<? if(!empty($intstats)){ ?>
		<tbody>
		<? foreach ($intstats as $int): ?>
			<tr>
				<?
					for($i=0; $i<17; ++$i)
						echo ($i) ? "<td>" . $int[$i] . "</td>" : "<th>" . $int[$i] . "</th>"; 
				?>
			</tr>
		<?
			endforeach;
			unset($intstats);
		?>
		</tbody>
	<? }else{ ?>
		<tbody>
    	<tr>
				<td colspan="17">No interface</td>
			</tr>
		</tbody>
	<? } ?>
</table>
