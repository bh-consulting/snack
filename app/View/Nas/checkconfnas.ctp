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
	if ($type == "Show CDP") {
		echo $this->Html->image('tmp/network.dot.png', array('alt' => 'Network'));//, 'width'=>'2048px'
	}
}

?>