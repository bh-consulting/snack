<div id="topology">
<?php 
echo '<div class="toggleBlock" onclick="toggleBlock(this)">';
echo $this->Html->link(__("Topology"), '#');
echo '<i class="glyphicon glyphicon-chevron-down"></i>';
echo '</div>';

echo $this->Html->image('tmp/network.dot.png', array('alt' => 'Network'));//, 'width'=>'2048px'
//debug($results);
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
?>
</div>