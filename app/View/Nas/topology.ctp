<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

?>

<h1><?php echo __('Toplogy'); ?></h1>
<?php
echo $this->Form->button('Check Topology', array(
    'type' => 'button',
    'class' => 'btn btn-primary',
    'onclick' => 'check_topology();',
));
?>
<div id="topology">
</div>
<?php
echo "<div class='col-md-2'></div>";
echo "<div class='col-md-8'>";

echo "<h2>".__('List of changes')."</h2>";
echo $this->Form->create('SelectDiff', array(
	'url' => array(
		'controller' => 'nas',
		'action' => 'topology_diff',
	),
	'type' => 'get',
	'class' => 'form-inline',
));

echo "<table class='table table-bordered table-striped table-condensed'>";
echo "<th colspan=\"2\"><i class=\"glyphicon glyphicon-zoom-in\"></i></th><th>Date</th><th>Commit</th><th>Changes</th><th><i class=\"glyphicon glyphicon-eye-open\"></i></th>";
$i=0;
$n=0;
foreach($results as $res) {
	echo "<tr>";
	echo "<td>";
	
	if ($i != 0) {
	    echo $this->Form->radio(
	        'a',
	        array($res['commit'] => ''),
	        array(
	            'hiddenField' => false,
	            'checked' => $i == 1,
	            'set' => $n
	        )
	    );
	}

	echo '</td>';

	if (isset($info['fit']) && $info['fit']) {
	    echo '<td class="fit">';
	} else {
	    echo '<td>';
	}

	if ($i != count($results)-1) {
	    echo $this->Form->radio(
	        'b',
	        array($res['commit'] => ''),
	        array(
	            'hiddenField' => false,
	            'checked' => $i == 0,
	            'set' => $n
	        )
	    );
	}
	echo "</td>";
	echo "<td>".$res['date']."</td>";
	echo "<td>".$res['commit']."</td>";
	if ($i == count($results)-1) {
		echo "<td> First commit </td>";	
	} else {
		echo "<td>".$res['changes']."</td>";
	}
	echo "<td onclick='view_topology(\"".$res['commit']."\");'>";
	echo '<i class="glyphicon glyphicon-eye-open"></i>';
    echo "</td>";
	echo "</tr>";
	$i++;
	++$n;
}
echo "</table>";
echo "</div>";
echo "<div class='col-md-offset-2 col-md-8'>";

echo $this->Form->button(
    '<i class="glyphicon glyphicon-zoom-in glyphicon glyphicon-white"></i> ' . __('Compare'),
    array(
	'type' => 'submit',
	'escape' => false,
	'class' => 'btn btn-primary',
    )
);

/*echo $this->Form->hidden('nas',
    array('value' => $nasID)
);*/

echo $this->Form->end();
echo "</div>";
?>