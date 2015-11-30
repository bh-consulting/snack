<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');
?>
<h1><?php echo __('Discover Network'); ?></h1>

<?php
$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Nas', array(
    'novalidate' => true, 
    'autocomplete' => 'off',
    'class' => 'form-horizontal',
    'inputDefaults' => array(
        'div' => 'form-group',
        'label' => array(
            'class' => $mainLabelOptions
        ),
        'between' => '<div class="col-sm-4">',
        'after'   => '</div>',
        'class' => 'form-control'
    ),
));

$myLabelOptions = array('text' => __('IP of one switch'));
echo $this->Form->input('ipaddress', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$myLabelOptions = array('text' => __('Login'));
echo $this->Form->input('login', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$myLabelOptions = array('text' => __('Password'));
echo $this->Form->input('password', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$myLabelOptions = array('text' => __('Depth'));
echo $this->Form->input('depth', array('value' => 1000, 'label' => array_merge($mainLabelOptions, $myLabelOptions)));

$list=array();
$myLabelOptions = array('text' => __('Hosts to display on graph'));
echo  $this->Form->input('hoststodisplay', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'options' => $hostsToDisplay,
    //'selected' => $id,
    'empty' => false,
));

$options = array(
    'label' => __('Discover'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => "btn btn-primary",
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>',
    'onclick' => 'load();'
    );
echo $this->Form->end($options);

echo "<div class='load'></div>";
//echo "<center>Veuillez patienter ".$this->Html->image('ajax-loader.gif')."</center>";

echo "<div class='results'>";
if ($post && !$error) {
	echo "<h1>".__('Results')."</h1>";
	echo $this->Html->image('tmp/network.dot.png', array('alt' => 'Network'));

	echo "<h1>".__('List of NAS')."</h1>";
	echo "<table class='table table-bordered table-striped table-condensed'>";
	echo "<th>Hostname</th><th>IP</th><th>Platform</th><th>Version</th>";
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


	echo "<h1>".__('List of Connections')."</h1>";
	echo "<table class='table table-bordered table-striped table-condensed'>";
	echo "<th>Hostname</th><th>Local Interface</th><th>Neighbors</th><th>Remote Interface</th>";
	foreach($results as $hostname=>$list) {
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


	


	echo "<h1>".__('List of NAS Unconfigured')."</h1>";
	echo "<div class='col-sm-4 col-sm-offset-4'>";
	echo "<table class='table table-bordered table-striped table-condensed'>";
	echo "<th>Hostname</th><th>IP Address</th><th class='fit'></th>";
	$id=0;
	foreach($allnasnotconfigured as $hostname=>$ipaddress) {
		echo "<tr id='nas_".$id."'>";
		echo "<td id='nashostname_".$id."'>".$hostname."</td>";
		echo "<td id='nasip_".$id."'>".$ipaddress."</td>";
		echo "<td id='nasadd_".$id."'>";
		echo '<i class="fa fa-plus text-info" onclick=\'addnas("'.$id.'","'.$ipaddress.'","'.$hostname.'","'.$login.'","'.$secret64Enc.'");\'></i>';
		echo "</td>";
		echo "</tr>";
		$id++;
	}
	echo "</table>";
	echo $this->Html->link(
	    '<i class="glyphicon glyphicon-plus"></i> ' . __('Add All'),
	    '#',
	    array('escape' => false, 'class' => 'btn btn-primary', 'onclick' => 'addallnas(\''.$login.'\',\''.$secret64Enc.'\');')
	);
	echo "</div>";
	echo "</div>";
}
?>