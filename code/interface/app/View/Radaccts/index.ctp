<? 
$this->extend('/Common/radius_sidebar');
$this->assign('monitoring_active', 'active');

$columns = array(
	'acctuniqueid'		=> __('Session ID'),
	'username'		=> __('Username'),
	'callingstationid'	=> __('IP'),
	'acctstarttime'		=> __('Start'),
	'acctstoptime'		=> __('Stop'),
	'nasipaddress'		=> __('NAS IP'),
	'nasportid'		=> __('NAS Port'),
);
?>
<h1><? echo __('Sessions'); ?></h1>

<table class="table">
    <thead>
    <tr>
				<?
					foreach( $columns as $field => $text )
						echo "<th>" . $this->Paginator->sort($field, $text . " " . ( (preg_match( "#$field$#", $this->Paginator->sortKey()) ) ? $this->Html->tag('i', '', array('class' => $sortIcons[$this->Paginator->sortDir()])) : '' ), array( 'escape' => false )) . "</th>";
				?>
				<th><? echo __('Delete'); ?></th>
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
            <? echo h($acct['Radacct']['framedipaddress']); ?>
        </td>
        <td>
            <? echo ( !empty( $acct['Radacct']['acctstarttime'] ) ) ? h($acct['Radacct']['acctstarttime']) : __("Unknown"); ?>
        </td>
        <td>
            <? echo ( !empty( $acct['Radacct']['acctstoptime'] ) ) ? h($acct['Radacct']['acctstoptime']) : __("Connected"); ?>
        </td>
        <td>
            <? echo h($acct['Radacct']['nasipaddress']); ?>
        </td>
        <td>
            <? echo ( !empty( $acct['Radacct']['nasportid'] ) ) ? h($acct['Radacct']['nasportid']) : __("Unknown"); ?>
        </td>
        <td>
						<i class="icon-trash"></i>
            <?php echo $this->Form->postLink('Delete', array('action' => 'delete', $acct['Radacct']['radacctid']), array('confirm' => __('Are you sure?'))); ?>
        </td>
    </tr>
    <? endforeach; ?>
    <? unset($acct); ?>
    </tbody>
<? }else{ ?>
    <tbody>
    <tr>
        <td colspan="8">
            <? echo __('No session found'); ?>.
        </td>
    </tr>
    </tbody>
<? 
	}
?>
</table>

<? echo $this->element('paginator_footer'); ?>
</div>

