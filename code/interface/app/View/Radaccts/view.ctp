<? 
$this->extend('/Common/radius_sidebar');
$this->assign('monitoring_active', 'active');
?>
<h1>Session : <? echo h($radacct['Radacct']['acctuniqueid']); ?></h1>

<h4>User:</h4>
<p style="padding: 0 0 0 20px">
	<strong>Username:</strong> <? echo $radacct['Radacct']['username']; ?></br>
	<strong>Groupname:</strong> <? echo $radacct['Radacct']['groupname']; ?></br>
	<strong>IP:</strong> <? echo $radacct['Radacct']['callingstationid']; ?>
</p>

<h4>Statistics:</h4>
<p style="padding: 0 0 0 20px">
	<strong>Start: </strong><? echo $radacct['Radacct']['acctstarttime']; ?></br>
	<strong>Stop: </strong><? echo $radacct['Radacct']['acctstoptime']; ?></br>
	<strong>Period: </strong><? echo $radacct['Radacct']['acctsessiontime']; ?>
</p>

<h4>Network Access Server:</h4>
<p style="padding: 0 0 0 20px">
	<strong>IP: </strong><? echo $radacct['Radacct']['calledstationid']; ?></br>
	<strong>Port: </strong><? echo $radacct['Radacct']['nasportid']; ?></br>
	<strong>Connection type: </strong><? echo $radacct['Radacct']['nasporttype']; ?>
</p>
