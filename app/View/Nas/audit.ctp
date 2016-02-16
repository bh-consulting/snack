<html><body>
<h1 class="book-title">Audit</h1>
<p class="title-page">CONFIDENTIAL</p>
<p class="title-page">SNACK - Guillaume Roche</p>
<p class="title-page"><?php echo $now->format("D, d M Y H:i:s O");?></p>
<pagebreak />

<tocpagebreak toc-prehtml="&lt;h1&gt;Example Contents&lt;/h1&gt;" toc-bookmarkText="Example Contents" toc-pagenumstyle="i" pagenumstyle="1" toc-resetpagenum="1" />

<tocpagebreak toc-prehtml="&lt;h1&gt;Contents&lt;/h1&gt;" toc-bookmarkText="Contents" toc-pagenumstyle="i" pagenumstyle="1" toc-resetpagenum="1" />

<htmlpagefooter name="footer-left" style="display:none">
  <div style="text-align: left;">{PAGENO} Example footer</div>
</htmlpagefooter>

<htmlpagefooter name="footer-right" style="display:none">
  <div style="text-align: right;">SNACK - {PAGENO} / {nb}</div>      
</htmlpagefooter>

<sethtmlpagefooter name="footer-right" value="on"  />

<tocentry level="0" content="Topology"></tocentry><bookmark content="Topology" /></tocentry><h1>Topology</h1>
<?php
echo $this->Html->image('tmp/network.dot.png', array('alt' => 'Network'));
?>
<tocentry level="1" content="Infos retrived from NAS"><bookmark content="Infos retrived from NAS" /></tocentry><h2>Infos retrived from NAS</h2>
<p>
<?php
echo "<table class='table table-bordered table-condensed'>";
echo "<tr>";
echo "<td><b>Nasname</b></td><td><b>Result</b></td><td><b>Connection type</b></td>";
echo "</tr>";
foreach($results as $hostname=>$infos) {
	echo "<tr>";
	echo "<td>".$hostname."</td>";
	if ($infos['result'] == "success") {
		echo "<td class='text-success'>";
	} else {
		echo "<td class='text-danger'>";
	}
	echo isset($infos['result']) ? $infos['result'] : "";
	echo "</td>";
	if ($infos['type'] == "ssh") {
		echo "<td class='text-success'>";
	} else {
		echo "<td class='text-danger'>";
	}
	echo isset($infos['type']) ? $infos['type'] : "";
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
?>
</p>
<tocentry level="1" content="List of NAS"><bookmark content="List of NAS" /></tocentry><h2>List of NAS</h2>
<p>
<?php
echo "<table class='table table-bordered table-condensed'>";
echo "<tr>";
echo "<td><b>Hostname</b></td><td><b>IP</b></td><td><b>Platform</b></td><td><b>Version</b></td>";
echo "</tr>";
foreach($listNasDone as $hostname=>$nas) {
	echo "<tr>";
	echo "<td>".$hostname."</td>";
	echo "<td>";
	echo isset($nas['ipaddress']) ? $nas['ipaddress'] : "";
	echo "</td>";
	echo "<td>";
	echo isset($nas['platform']) ? $nas['platform'] : "";
	echo "</td>";
	echo "<td>";
	echo isset($nas['version']) ? $nas['version'] : "";
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
?>
</p>
<tocentry level="1" content="List of connections"><bookmark content="List of connections" /></tocentry><h2>List of connections</h2>
<?php
echo "<table class='table table-bordered table-striped table-condensed'>";
echo "<tr>";
echo "<td><b>Hostname</b></td><td><b>Local Interface</b></td><td><b>Neighbors</b></td><td><b>Remote Interface</b></td>";
echo "</tr>";
foreach($connections as $hostname=>$list) {
	echo "<tr>";
	echo "<td>".$hostname."</td>";
	echo "</tr>";
	foreach($list as $nas) {
		echo "<tr>";
		echo "<td></td><td>".$nas['localinterface']."</td><td>".$nas['hostname']."</td><td>".$nas['remoteinterface']."</td>";
		echo "</tr>";
	}
}
echo "</table>";
?>
<pagebreak />
<tocentry level="0" content="Configuration analysis"><bookmark content="Configuration analysis" /></tocentry><h1>Configuration analysis</h1>
<tocentry level="1" content="List of vlans"><bookmark content="List of vlans" /></tocentry><h2>List of vlans</h2>
<?php
$errvlan=false;
foreach($vlans as $vlan) {
	if (isset($nb)) {
		if ($nb != count($vlan)) {
			$errvlan = true;
			break;
		}
	}
	$nb=count($vlan);
}
if ($errvlan) {
	echo '<div class="alert alert-danger" role="alert">There is at least one nas with number of vlans mismatches</div>';
}
echo "<table class='table table-bordered table-striped table-condensed'>";
echo "<tr>";
echo "<td><b>Hostname</b></td><td><b>VLAN Id</b></td><td><b>VLAN Name</b></td>";
echo "</tr>";
foreach($vlans as $hostname=>$vlan) {
	echo "<tr>";
	echo "<td>".$hostname."</td>";
	echo "</tr>";
	foreach($vlan as $id=>$name) {
		echo "<tr>";
		echo "<td></td><td>".$id."</td><td>".$name."</td>";
		echo "</tr>";
	}
}
echo "</table>";
?>
<tocentry level="1" content="VTP config"><bookmark content="VTP config" /></tocentry><h2>VTP config</h2>
<?php
$errvtp=false;
foreach($vtp as $vtpinfos) {
	if (isset($vtpinfos['domain'])) {
		if ($vtpinfos['domain'] == "VTP") {
			$errvtp = true;
			break;
		}
	}
	$nb=count($vlan);
}
if ($errvtp) {
	echo '<div class="alert alert-danger" role="alert">There is at least one nas with VTP not configured</div>';
}
echo "<table class='table table-bordered table-striped table-condensed'>";
echo "<tr>";
echo "<td><b>Hostname</b></td><td><b>Domain</b></td><td><b>Password</b></td><td><b>Mode</b></td><td><b>Version</b></td><td><b>Pruning</b></td>";
echo "</tr>";
foreach($vtp as $hostname=>$vtpinfos) {
	echo "<tr>";
	if ($vtpinfos['domain'] == "VTP") {
		echo "<td class='text-danger'>".$hostname."</td>";
		echo "<td class='text-danger'>".$vtpinfos['domain']."</td>";
		echo "<td></td>";
		echo "<td>".$vtpinfos['vtpmode']."</td>";
		if (isset($vtpinfos['version'])) {
			echo "<td>".$vtpinfos['version']."</td>";
		} else {
			echo "<td></td>";
		}
		echo "<td>".$vtpinfos['pruning']."</td>";
	} else {
		echo "<td>".$hostname."</td>";
		echo "<td>".$vtpinfos['domain']."</td>";
		echo "<td>".$vtpinfos['password']."</td>";
		echo "<td>".$vtpinfos['vtpmode']."</td>";
		if (isset($vtpinfos['version'])) {
			echo "<td>".$vtpinfos['version']."</td>";
		} else {
			echo "<td></td>";
		}
		echo "<td>".$vtpinfos['pruning']."</td>";
	}
	echo "</tr>";
}
echo "</table>";
?>

<tocentry level="1" content="Time NAS"><bookmark content="Time NAS" /></tocentry><h2>Time NAS</h2>
<?php

if ($errclock) {
	echo '<div class="alert alert-danger" role="alert">There is at least one nas with clock not configured</div>';
}
echo "<div class='col-sm-4'>";
echo "<table class='table table-bordered table-striped table-condensed '>";
echo "<tr>";
echo "<td>NAS</td><td>Clock</td>";
echo "</tr>";
foreach($clock as $nas=>$result) {
	echo "<tr>";
	echo "<td>".$nas."</td>";
	echo "<td>".$result."</td>";
	echo "</tr>";
}
echo "</table>";
echo "</div>";
?>

<tocentry level="1" content="NTP Status"><bookmark content="NTP Status" /></tocentry><h2>NTP Status</h2>
<?php
echo "<div class='col-sm-4'>";
echo "<table class='table table-bordered table-striped table-condensed '>";
echo "<tr>";
echo "<td>NAS</td><td>Reference</td><td>Status</td>";
echo "</tr>";
foreach($ntp as $nas=>$result) {
	echo "<tr>";
	echo "<td>".$nas."</td>";
	echo "<td>".$result['reference']."</td>";
	echo "<td>".$result['status']."</td>";
	echo "</tr>";
}
echo "</table>";
echo "</div>";
?>

<tocentry level="1" content="ENV Status"><bookmark content="ENV Status" /></tocentry><h2>ENV Status</h2>
<?php
echo "<div class='col-sm-4'>";
echo "<table class='table table-bordered table-striped table-condensed '>";
echo "<tr>";
echo "<td>NAS</td><td>System Temp</td><td>FAN</td><td>Status FAN</td>";
echo "</tr>";
foreach($env as $nas=>$result) {
	echo "<tr>";
	echo "<td>".$nas."</td>";
	echo "<td>".$result['systemtemp']."</td>";
	$i = 0;
	if (count($result['fan']) == 0) {
		echo "<td></td>";
		echo "<td></td>";
	}
	foreach ($result['fan'] as $id=>$fan) {
		if ($i > 0) {
			echo "<tr><td></td><td></td>";
		}
		echo "<td>".$id."</td>";
		echo "<td>".$fan."</td>";
		if (count($result['fan']) != ($i+1)) {
			echo "</tr>";
		}
		$i++;
	}
	echo "</tr>";
}
echo "</table>";
echo "</div>";
?>



<pagebreak />
<tocentry level="0" content="Topology analysis"></tocentry><bookmark content="Topology analysis" /></tocentry><h1>Topology analysis</h1>
<tocentry level="1" content="HSRP config"><bookmark content="HSRP config" /></tocentry><h2>HSRP config</h2>
<?php
echo "<div class='col-sm-offset-3 col-sm-4'>";
echo "<table class='table table-bordered table-striped table-condensed '>";
echo "<td><b>NAS</b></td><td><b>Interface</b></td><td><b>Group</b></td><td><b>Priority</b></td><td><b>Interface</b></td><td><b>Preempt</b></td><td><b>State</b></td><td><b>Active</b></td><td><b>Standby</b></td><td><b>Virtual IP</b></td>";
foreach ($hsrp as $nas=>$result) {
	if (count($result)>0) {
		echo "<tr>";
		echo "<td>$nas</td>";
		echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
		echo "</tr>";
		foreach($result as $line) {
			echo "<tr>";
			echo "<td></td>";
			foreach ($line as $key=>$value) {
				echo "<td>$value</td>";
			}			
			echo "</tr>";
		}
		echo "</table>";
	}
}



?>
<tocentry level="1" content="Spanning-Tree"><bookmark content="Spanning-Tree" /></tocentry><h2>Spanning-Tree</h2>
<?php
echo "<div class='col-sm-offset-3 col-sm-4'>";
echo "<table class='table table-bordered table-striped table-condensed '>";
echo "<tr>";
echo "<td><b>NAS</b></td><td><b>STP Mode</b></td><td><b>is Root ?</b></td>";
echo "</tr>";
foreach($stp as $nas=>$result) {
	echo "<tr>";
	if ($result['mode'] == "") {
		echo "<td class='text-danger'>".$nas."</td>";
	} else {
		echo "<td>".$nas."</td>";
	}
	echo "<td>".$result['mode']."</td>";
	if (count($result['rootvlans']) > 0) {
		echo "<td><b>Yes</b></td>";
	} else {
		echo "<td>No</td>";
	}
	echo "</tr>";
}
echo "</table>";

echo "<table class='table table-bordered table-striped table-condensed '>";
echo "<tr>";
echo "<td><b>NAS</b></td><td><b>VLAN</b></td><td><b>BLK</b></td><td><b>LIS</b></td><td><b>LRN</b></td><td><b>FWD</b></td><td><b>ACT</b></td>";
echo "</tr>";
foreach($stp as $nas=>$result) {
	echo "<tr>";
	echo "<td>".$nas."</td>";
	echo "<td></td><td></td><td></td><td></td><td></td><td></td>";
	$i=0;
	echo "</tr>";
	
	foreach ($result['intfstates']['vlan'] as $vlan) {
		echo "<tr>";
		echo "<td></td>";
		echo "<td>$vlan</td>";
		echo "<td>".$result['intfstates']['BLK'][$i]."</td>";
		echo "<td>".$result['intfstates']['LIS'][$i]."</td>";
		echo "<td>".$result['intfstates']['LRN'][$i]."</td>";
		echo "<td>".$result['intfstates']['FWD'][$i]."</td>";
		echo "<td>".$result['intfstates']['ACT'][$i]."</td>";
		echo "</tr>";
		$i++;
	}
}
echo "</table>";
echo "</div>";

?>
<tocentry level="1" content="Interface errors"><bookmark content="Interface errors" /></tocentry><h2>Interface errors</h2>
<?php

$cols = array(
	'Align-Err',
	'FCS-Err',
	'Xmit-Err',
	'Rcv-Err',
	'UnderSize',
	'OutDiscards',
	'Single-Col',
	'Multi-Col',
	'Late-Col',
	'Excess-Col',
	//'Carri-Sen',
	'Runts',
	'Giants',
);
$colsrecv = array(
	'Align-Err',
	'FCS-Err',
	'Rcv-Err',
	'UnderSize',
	'Runts',
	'Giants',
);
$colstranmit = array(
	'OutDiscards',
	'Single-Col',
	'Multi-Col',
	'Late-Col',
	'Excess-Col',
	//'Carri-Sen',
);
echo "<div class='col-sm-8'>";
$packcols =  array(
	'OutUcastPkts',
	'InUcastPkts'
);
foreach($err as $nas=>$result) {
	if (count($result)>0) {
		echo "<h3>$nas</h3>";
		echo "<table class='table table-bordered table-striped table-condensed '>";
		echo "<tr>";
		echo "<td><b>Interface</b></td>";
		$realcols = array();
		foreach($result as $arr) {
			foreach($arr as $col=>$nb) {
				if (!in_array($col, $realcols)) {
					if ($col != "Carri-Sen") {
						$realcols[] = $col;
					}
				}
			}
		}
		echo "<td><b>Last Clear</b></td>";
		foreach ($packcols as $col) {
			echo "<td><b>$col</b></td>";
		} 
		foreach ($realcols as $col) {
			echo "<td><b>$col</b></td>";
		}
		echo "</tr>";
		foreach ($result as $intf=>$arr) {
			echo "<tr>";
			if (preg_match('/Fa(.*)/', $intf, $matches)) {
				$tmp = "FastEthernet".$matches[1];
			}
			if (preg_match('/Gi(.*)/', $intf, $matches)) {
				$tmp = "GigabitEthernet".$matches[1];
			}
			if (preg_match('/Te(.*)/', $intf, $matches)) {
				$tmp = "TenGigabitEthernet".$matches[1];
			}
			if (preg_match('/Po(.*)/', $intf, $matches)) {
				$tmp = "Port-channel".$matches[1];
			}
			echo "<td><b>".$intf."</b></td>";
			echo "<td>".$intfclr[$nas][$tmp]."</td>";
			if ($pack[$nas][$intf][$packcols[0]] > 1000000000) {
				$nb = bcdiv($pack[$nas][$intf][$packcols[0]], 1000000000, 3);
				echo "<td>".$nb." T</td>";
			}
			else if ($pack[$nas][$intf][$packcols[0]] > 1000000) {
				$nb = bcdiv($pack[$nas][$intf][$packcols[0]], 1000000, 3);
				echo "<td>".$nb." M</td>";
			}
			else if ($pack[$nas][$intf][$packcols[0]] > 1000) {
				$nb = bcdiv($pack[$nas][$intf][$packcols[0]], 1000, 3);
				echo "<td>".$nb." K</td>";
			} else {
				echo "<td>".$pack[$nas][$intf][$packcols[0]]."</td>";
			}
			if ($pack[$nas][$intf][$packcols[1]] > 1000000000) {
				$nb = bcdiv($pack[$nas][$intf][$packcols[1]], 1000000000, 3);
				echo "<td>".$nb." T</td>";
			}
			else if ($pack[$nas][$intf][$packcols[1]] > 1000000) {
				$nb = bcdiv($pack[$nas][$intf][$packcols[1]], 1000000, 3);
				echo "<td>".$nb." M</td>";
			}
			else if ($pack[$nas][$intf][$packcols[1]] > 1000) {
				$nb = bcdiv($pack[$nas][$intf][$packcols[1]], 1000, 3);
				echo "<td>".$nb." K</td>";
			} else {
				echo "<td>".$pack[$nas][$intf][$packcols[1]]."</td>";
			}
			foreach ($realcols as $col2) {
				$found = false;
				foreach ($arr as $col=>$nb) {	
					if ($col == $col2) {
						if (in_array($col2, $colstranmit)) {
							$percent = bcdiv($nb, $pack[$nas][$intf][$packcols[0]], 3);
							if ($percent >= 2) {
								echo "<td class='text-danger'>$nb ($percent %)</td>";
							} else {
								echo "<td>$nb ($percent %)</td>";
							}
							$found = true;
						}
						if (in_array($col2, $colsrecv)) {
							$percent = bcdiv($nb, $pack[$nas][$intf][$packcols[1]], 3);
							if ($percent >= 2) {
								echo "<td class='text-danger'>$nb ($percent %)</td>";
							} else {
								echo "<td>$nb ($percent %)</td>";
							}
							$found = true;
						}
					}	
				}
				if (!$found) {
					echo "<td></td>";
				}
			}
			echo "</tr>";
		}
		echo "</table>";
	}
}

echo "</div>";
?>

<div class="panel panel-default">
  <div class="panel-heading">Align-Err</div>
  <div class="panel-body">
    CatOS sh port and Cisco IOS sh interfaces counters errors. Alignment errors are a count of the number of frames received that don’t end with an even number of octets and have a bad Cyclic Redundancy Check (CRC). Common Causes: These are usually the result of a duplex mismatch or a physical problem (such as cabling, a bad port, or a bad NIC). When the cable is first connected to the port, some of these errors can occur. Also, if there is a hub connected to the port, collisions between other devices on the hub can cause these errors. Platform Exceptions: Alignment errors are not counted on the Catalyst 4000 Series Supervisor I (WS-X4012) or Supervisor II (WS-X4013).
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">FCS-Err</div>
  <div class="panel-body">
    CatOS sh port and Cisco IOS sh interfaces counters errors. The number of valid size frames with Frame Check Sequence (FCS) errors but no framing errors. Common Causes: This is typically a physical issue (such as cabling, a bad port, or a bad Network Interface Card (NIC)) but can also indicate a duplex mismatch.
frame 	Description: Cisco IOS sh interfaces counter. The number of packets received incorrectly that has a CRC error and a non-integer number of octets (alignment error). Common Causes: This is usually the result of collisions or a physical problem (such as cabling, bad port or NIC) but can also indicate a duplex mismatch.
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Giants</div>
  <div class="panel-body">
    CatOS sh port and Cisco IOS sh interfaces and sh interfaces counters errors. Frames received that exceed the maximum IEEE 802.3 frame size (1518 bytes for non-jumbo Ethernet) and have a bad Frame Check Sequence (FCS). Common Causes: In many cases, this is the result of a bad NIC. Try to find the offending device and remove it from the network. Platform Exceptions: Catalyst Cat4000 Series that run Cisco IOS Previous to software Version 12.1(19)EW, the giants counter incremented for a frame > 1518bytes. After 12.1(19)EW, a giant in show interfaces increments only when a frame is received >1518bytes with a bad FCS.
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Excess-Col</div>
  <div class="panel-body">
    CatOS sh port and Cisco IOS sh interfaces counters errors. A count of frames for which transmission on a particular interface fails due to excessive collisions. An excessive collision happens when a packet has a collision 16 times in a row. The packet is then dropped. Common Causes: Excessive collisions are typically an indication that the load on the segment needs to be split across multiple segments but can also point to a duplex mismatch with the attached device. Collisions must not be seen on interfaces configured as full duplex.
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Late-Col</div>
  <div class="panel-body">
    CatOS sh port and Cisco IOS sh interfaces and sh interfaces counters errors. The number of times a collision is detected on a particular interface late in the transmission process. For a 10 Mbit/s port this is later than 512 bit-times into the transmission of a packet. Five hundred and twelve bit-times corresponds to 51.2 microseconds on a 10 Mbit/s system. Common Causes: This error can indicate a duplex mismatch among other things. For the duplex mismatch scenario, the late collision is seen on the half duplex side. As the half duplex side is transmitting, the full duplex side does not wait its turn and transmits simultaneously which causes a late collision. Late collisions can also indicate an Ethernet cable or segment that is too long. Collisions must not be seen on interfaces configured as full duplex.
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Multi-Col</div>
  <div class="panel-body">
    CatOS sh port and Cisco IOS sh interfaces counters errors. The number of times multiple collisions occurred before the interface transmitted a frame to the media successfully. Common Causes: Collisions are normal for interfaces configured as half duplex but must not be seen on full duplex interfaces. If collisions increase dramatically, this points to a highly utilized link or possibly a duplex mismatch with the attached device.
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Out-Discard</div>
  <div class="panel-body">
     The number of outbound packets chosen to be discarded even though no errors have been detected. Common Causes: One possible reason to discard such a packet can be to free up buffer space.
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Rcv-Err</div>
  <div class="panel-body">
    CatOS show port or show port counters and Cisco IOS (for the Catalyst 6000 Series only) sh interfaces counters error. Common Causes: See Platform Exceptions. Platform Exceptions: Catalyst 5000 Series rcv-err = receive buffer failures. For example, a runt, giant, or an FCS-Err does not increment the rcv-err counter. The rcv-err counter on a 5K only increments as a result of excessive traffic. On Catalyst 4000 Series rcv-err = the sum of all receive errors, which means, in contrast to the Catalyst 5000, that the rcv-err counter increments when the interface receives an error like a runt, giant or FCS-Err.
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Single-Col</div>
  <div class="panel-body">
    CatOS sh port and Cisco IOS sh interfaces counters errors. The number of times one collision occurred before the interface transmitted a frame to the media successfully. Common Causes: Collisions are normal for interfaces configured as half duplex but must not be seen on full duplex interfaces. If collisions increase dramatically, this points to a highly utilized link or possibly a duplex mismatch with the attached device.
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Xmit-Err</div>
  <div class="panel-body">
    CatOS sh port and Cisco IOS sh interfaces counters errors. This is an indication that the internal send (Tx) buffer is full. Common Causes: A common cause of Xmit-Err can be traffic from a high bandwidth link that is switched to a lower bandwidth link, or traffic from multiple inbound links that are switched to a single outbound link. For example, if a large amount of bursty traffic comes in on a gigabit interface and is switched out to a 100Mbps interface, this can cause Xmit-Err to increment on the 100Mbps interface. This is because the output buffer of the interface is overwhelmed by the excess traffic due to the speed mismatch between the inbound and outbound bandwidths.
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Runts</div>
  <div class="panel-body">
    CatOS sh port and Cisco IOS sh interfaces and sh interfaces counters errors. The frames received that are smaller than the minimum IEEE 802.3 frame size (64 bytes for Ethernet), and with a bad CRC. Common Causes: This can be caused by a duplex mismatch and physical problems, such as a bad cable, port, or NIC on the attached device. Platform Exceptions: Catalyst 4000 Series that run Cisco IOS Previous to software Version 12.1(19)EW, a runt = undersize. Undersize = frame < 64bytes. The runt counter only incremented when a frame less than 64 bytes was received. After 12.1(19EW, a runt = a fragment. A fragment is a frame < 64 bytes but with a bad CRC. The result is the runt counter now increments in show interfaces, along with the fragments counter in show interfaces counters errors when a frame <64 bytes with a bad CRC is received. Cisco Catalyst 3750 Series Switches In releases prior to Cisco IOS 12.1(19)EA1, when dot1q is used on the trunk interface on the Catalyst 3750, runts can be seen on show interfaces output because valid dot1q encapsulated packets, which are 61 to 64 bytes and include the q-tag, are counted by the Catalyst 3750 as undersized frames, even though these packets are forwarded correctly. In addition, these packets are not reported in the appropriate category (unicast, multicast, or broadcast) in receive statistics. This issue is resolved in Cisco IOS release 12.1(19)EA1 or 12.2(18)SE or later.
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Undersize</div>
  <div class="panel-body">
    CatOS sh port and Cisco IOS sh interfaces counters errors . The frames received that are smaller than the minimum IEEE 802.3 frame size of 64 bytes (which excludes framing bits, but includes FCS octets) that are otherwise well formed. Common Causes: Check the device that sends out these frames.
  </div>
</div>

<pagebreak />
<tocentry level="0" content="Annex"></tocentry><bookmark content="Annex" /></tocentry><h1>Annex</h1>
<?php
foreach($conf as $nas=>$result) {
	echo '<tocentry level="1" content="'.$nas.'"><bookmark content="'.$nas.'" /></tocentry><h2>'.$nas.'</h2>';
	foreach ($result as $line) {
		echo "$line<br>";
	}
}
?>


</body></html>