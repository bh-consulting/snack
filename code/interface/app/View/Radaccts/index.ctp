<? 
$this->extend('/Common/radius_sidebar');
$this->assign('monitoring_active', 'active');
?>
<h1>Sessions</h1>


<table class="table">
    <thead>
    <tr>
        <th>Session ID</th>
        <th>Username</th>
        <th>IP</th>
        <th>Start</th>
        <th>Stop</th>
				<th>NAS</th>
    </tr>
    </thead>

<? if(!empty($radaccts)){ ?>
    <tbody>
    <? foreach ($radaccts as $acct): ?>
    <tr>
        <td>
            <? echo $this->Html->link(	h($acct['Radacct']['acctuniqueid']),
            														array(	'controller' => 'Radaccts', 
																								'action' => 'view', 
																								$acct['Radacct']['radacctid']	)); ?>
        </td>
        <td>
            <? echo h($acct['Radacct']['username']); ?>
        </td>
        <td>
            <? echo h($acct['Radacct']['callingstationid']); ?>
        </td>
        <td>
            <? echo ( !empty( $acct['Radacct']['acctstarttime'] ) ) ? h($acct['Radacct']['acctstarttime']) : "Unknown"; ?>
        </td>
        <td>
            <? echo ( !empty( $acct['Radacct']['acctstoptime'] ) ) ? h($acct['Radacct']['acctstoptime']) : "Connected"; ?>
        </td>
        <td>
            <? echo h($acct['Radacct']['nasipaddress']) . ( ( !empty( $acct['Radacct']['nasportid'] ) ) ? ":" . h($acct['Radacct']['nasportid']) : "" ); ?>
        </td>
    </tr>
    <? endforeach; ?>
    <? unset($acct); ?>
    </tbody>
<? }else{ ?>
    <tbody>
    <tr>
        <td colspan="6">
            No session found.
        </td>
    </tr>
    </tbody>
<? } ?>
</table>

