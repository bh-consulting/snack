<?php 

$this->extend('/Common/radusers_tabs');
$this->assign('radusers_revokcerts_active', 'active');

?>

<table class="table table-hover table-bordered">
<th>Name</th><th>Serial</th><th>Expiration date</th><th>Revokation date</th>
<?php
//debug($results);
for($i=0; $i<count($results[0]); $i++) {
	echo "<tr>";
	echo "<td>".$results[5][$i]."</td>";
	echo "<td>".$results[3][$i]."</td>";
	if (preg_match("/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})\w/", $results[1][$i], $matches)) {
		$d1=new DateTime("20".$matches[1]."-".$matches[2]."-".$matches[3]." ".$matches[4].":".$matches[5]);
		echo "<td>".$d1->format('Y-m-d H:i:s')." UTC </td>";
	} else {
		echo "<td>NA</td>";
	}
	if (preg_match("/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})\w/", $results[2][$i], $matches)) {
		$d1=new DateTime("20".$matches[1]."-".$matches[2]."-".$matches[3]." ".$matches[4].":".$matches[5]);
		echo "<td>".$d1->format('Y-m-d H:i:s')." UTC </td>";
	} else {
		echo "<td>NA</td>";
	}
	echo "</tr>";
}
?>
</table>

