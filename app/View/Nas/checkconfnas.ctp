<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

?>

<h1><?php echo __('Check NAS Configurations'); ?></h1>
<?php
echo $this->Form->create('Nas', array(
    //'action' => $action,
    'novalidate' => true, 
    'autocomplete' => 'off',
    'class' => 'form-inline col-sm-offset-4',
    'inputDefaults' => array(
        'div' => 'form-group',
        'class' => 'form-control'
    ),
));

$mainLabelOptions = array('class' => 'label-inline control-label');
$myLabelOptions = array('text' => __('Verify'));
echo  $this->Form->input('checktype', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),//__('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NAS &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'),
    'options' => $list,
    //'selected' => $id,
    'empty' => false,
));
$options = array(
    'label' => __('Check'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => 'btn btn-primary',
    'before' => '<div class="col-sm-offset-1 col-sm-2">',
    'after' => '</div>'
);
echo $this->Form->end($options);

if (isset($results)) {
	$radius1=Configure::read('Parameters.ipAddress');
	$radius2=Configure::read('Parameters.slave_ip_to_monitor');
	echo "<br>";
	if ($type == "Radius servers") {
		echo "<div class='col-sm-offset-3 col-sm-4'>";
		echo "<table class='table table-bordered table-striped table-condensed '>";
		echo "<th>NAS</th><th>Radius</th><th>Status</th><th>Msg</th>";
		foreach($results as $nas=>$result) {
			echo "<tr>";
			echo "<td>".$nas."</td>";
			echo "<td></td><td></td><td></td>";
			echo "</tr>";
			echo "<tr>";
			//debug($result);
			echo "<td></td><td>".$result[0][0]."</td>";
			if ($result[0][0] == $radius1) {
				echo "<td><i class='glyphicon glyphicon-ok text-success'></i></td>";
				echo "<td></td>";
			}
			echo "</tr>";
			echo "<tr>";
			if (count($result[0]) > 1) {
				echo "<td></td><td>".$result[0][1]."</td>";
				if ($result[0][1] == $radius2) {
					echo "<td><i class='glyphicon glyphicon-ok text-success'></i></td>";
				}
				else {
					echo "<td><i class='glyphicon glyphicon-remove text-danger'></i></td>";
					echo "<td>Should be ".$radius2."</td>";
				}
			} else {
				echo "<td></td><td> - </td>";
				echo "<td><i class='glyphicon glyphicon-remove text-danger'></i></td>";
				echo "<td>Should be ".$radius2."</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
		echo "</div>";
	}
	if ($type == "Test servers on NAS") {
		/*echo "<div class='col-sm-offset-3 col-sm-4'>";
		echo "<table class='table table-bordered table-striped table-condensed '>";
		echo "<th>NAS</th><th>Radius</th><th>Status</th><th>Msg</th>";
		foreach($results as $nas=>$result) {
			echo "<tr>";
			echo "<td>".$nas."</td>";
			echo "<td></td><td></td><td></td>";
			echo "</tr>";
			echo "<tr>";
			//debug($result);
			echo "<td></td><td>".$result[0][0]."</td>";
			if ($result[0][0] == $radius1) {
				echo "<td><i class='glyphicon glyphicon-ok text-success'></i></td>";
				echo "<td></td>";
			}
			echo "</tr>";
			echo "<tr>";
			if (count($result[0]) > 1) {
				echo "<td></td><td>".$result[0][1]."</td>";
				if ($result[0][1] == $radius2) {
					echo "<td><i class='glyphicon glyphicon-ok text-success'></i></td>";
				}
				else {
					echo "<td><i class='glyphicon glyphicon-remove text-danger'></i></td>";
					echo "<td>Should be ".$radius2."</td>";
				}
			} else {
				echo "<td></td><td> - </td>";
				echo "<td><i class='glyphicon glyphicon-remove text-danger'></i></td>";
				echo "<td>Should be ".$radius2."</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
		echo "</div>";*/
	}
	if ($type == "Show clock") {
		echo "<div class='col-sm-offset-3 col-sm-4'>";
		echo "<table class='table table-bordered table-striped table-condensed '>";
		echo "<th>NAS</th><th>Clock</th>";
		foreach($results as $nas=>$result) {
			echo "<tr>";
			echo "<td>".$nas."</td>";
			echo "<td>".$result."</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</div>";
	}
	if ($type == "Show errors") {
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
			'Carri-Sen',
			'Runts',
			'Giants',
		);
		echo "<div class='col-sm-4'>";
		foreach($results as $nas=>$result) {
			if (count($result)>0) {
				echo "<h3>$nas</h3>";
				echo "<table class='table table-bordered table-striped table-condensed '>";
				echo "<th>Interface</th>";
				foreach ($cols as $col) {
					echo "<th>$col</th>";
				}
				foreach ($result as $intf=>$arr) {
					echo "<tr>";
					echo "<td>".$intf."</td>";
					foreach ($cols as $col2) {
						$found = false;
						foreach ($arr as $col=>$nb) {	
							if ($col == $col2) {
								echo "<td>$nb</td>";
								$found = true;
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
	}
	if ($type == "Show STP") {

		echo "<div class='col-sm-offset-3 col-sm-4'>";
		echo "<table class='table table-bordered table-striped table-condensed '>";
		echo "<th>NAS</th><th>STP Mode</th><th>is Root ?</th>";
		foreach($results as $nas=>$result) {
			echo "<tr>";
			echo "<td>".$nas."</td>";
			echo "<td>".$result['mode']."</td>";
			if (count($result['rootvlans']) > 0) {
				echo "<td>Yes</td>";
			} else {
				echo "<td>No</td>";
			}
			echo "</tr>";
		}
		echo "</table>";

		echo "<table class='table table-bordered table-striped table-condensed '>";
		echo "<th>NAS</th><th>VLAN</th><th>BLK</th><th>LIS</th><th>LRN</th><th>FWD</th><th>ACT</th>";
		foreach($results as $nas=>$result) {
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
			
			echo "</tr>";
		}
		echo "</table>";
		echo "</div>";
	}
}

?>