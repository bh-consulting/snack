<? 
$this->extend('/Common/radius_sidebar');
$this->assign('session_active', 'active');
?>
<h2><? echo __('Session:') . ' ' . h($radacct['Radacct']['acctuniqueid']); ?></h2>

<div class="well">
	<h4><? echo __('User:'); ?></h4>
	<p style="padding: 0 0 0 20px">
		<strong><? echo __('Username:'); ?></strong> <? echo $radacct['Radacct']['username']; ?></br>
		<strong><? echo __('Groupname:'); ?></strong> <? echo $radacct['Radacct']['groupname']; ?></br>
		<strong><? echo __('IP:'); ?></strong> <? echo $radacct['Radacct']['callingstationid']; ?>
	</p>
	
	<h4><? echo __('Statistics:'); ?></h4>
	<p style="padding: 0 0 0 20px">
		<strong><? echo __('Start:'); ?> </strong><? echo $radacct['Radacct']['acctstarttime']; ?></br>
		<strong><? echo __('Stop:'); ?> </strong><? echo $radacct['Radacct']['acctstoptime']; ?></br>
		<strong><? echo __('Period:'); ?> </strong><? echo $radacct['Radacct']['acctsessiontime']; ?>
	</p>
	
	<h4><? echo __('Network Access Server:'); ?></h4>
	<p style="padding: 0 0 0 20px">
		<strong><? echo __('IP:'); ?> </strong><? echo $radacct['Radacct']['calledstationid']; ?></br>
		<strong><? echo __('Port:'); ?> </strong><? echo $radacct['Radacct']['nasportid']; ?></br>
		<strong><? echo __('Connection type:'); ?> </strong><? echo $radacct['Radacct']['nasporttype']; ?>
	</p>
</div>
