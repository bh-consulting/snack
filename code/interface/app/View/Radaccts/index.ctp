<? 
$this->extend('/Common/radius_sidebar');
$this->assign('monitoring_active', 'active');
?>
<h1>Sessions</h1>

<table class="table">
    <thead>
    <tr>
        <th><? echo $this->Paginator->sort('acctuniqueid', 'Session ID ' . ( ($this->Paginator->sortKey() == "acctuniqueid") ? $this->Html->image( $this->Paginator->sortDir().'.png') : '' ), array( 'escape' => false )); ?></th>
        <th><? echo $this->Paginator->sort('username', 'Username ' . ( ($this->Paginator->sortKey() == "username") ? $this->Html->image( $this->Paginator->sortDir().'.png') : '' ), array( 'escape' => false )); ?></th>
        <th><? echo $this->Paginator->sort('callingstationid', 'IP ' . ( ($this->Paginator->sortKey() == "callingstationid") ? $this->Html->image( $this->Paginator->sortDir().'.png') : '' ), array( 'escape' => false )); ?></th>
        <th><? echo $this->Paginator->sort('acctstarttime', 'Start ' . ( ($this->Paginator->sortKey() == "acctstarttime") ? $this->Html->image( $this->Paginator->sortDir().'.png') : '' ), array( 'escape' => false )); ?></th>
        <th><? echo $this->Paginator->sort('acctstoptime', 'Stop ' . ( ($this->Paginator->sortKey() == "acctstoptime") ? $this->Html->image( $this->Paginator->sortDir().'.png') : '' ), array( 'escape' => false )); ?></th>
				<th><? echo $this->Paginator->sort('nasipaddress', 'NAS IP ' . ( ($this->Paginator->sortKey() == "nasipaddress") ? $this->Html->image( $this->Paginator->sortDir().'.png') : '' ), array( 'escape' => false )); ?></th>
				<th><? echo $this->Paginator->sort('nasportid', 'Nas Port ' . ( ($this->Paginator->sortKey() == "nasportid") ? $this->Html->image( $this->Paginator->sortDir().'.png') : '' ), array( 'escape' => false )); ?></th>
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
            <? echo h($acct['Radacct']['nasipaddress']); ?>
        </td>
        <td>
            <? echo ( !empty( $acct['Radacct']['nasportid'] ) ) ? h($acct['Radacct']['nasportid']) : "Unknown"; ?>
        </td>
    </tr>
    <? endforeach; ?>
    <? unset($acct); ?>
    </tbody>
<? }else{ ?>
    <tbody>
    <tr>
        <td colspan="7">
            No session found.
        </td>
    </tr>
    </tbody>
<? 
	}
?>
</table>

<p style="float:left;">
<?
	echo 	$this->Paginator->prev('Prev. ', null, null, array('class' => 'disabled')) .
				$this->Paginator->numbers( array(	'modulus'		=> 4,
																					'first'			=> 2,
																					'last'			=> 2,
																					'ellipsis'	=> " ... "
																					)) .
				$this->Paginator->next(' Next', null, null, array('class' => 'disabled'));
?>
</p>

<p style="float:right;">
<?
	echo $this->Paginator->counter( array('format' => 'range') );
?>
</p>

