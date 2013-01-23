<?php
$this->extend('/Common/radius_sidebar');
$this->assign('monitoring_active', 'active');
?>

<h1><?php echo __('Sessions'); ?></h1>
<?php
$columns = array(
    'acctuniqueid' => __('Session ID'),
    'username' => __('Username'),
    'callingstationid' => __('IP'),
    'acctstarttime' => __('Start'),
    'acctstoptime' => __('Stop'),
    'nasipaddress' => __('NAS IP'),
    'nasportid' => __('NAS Port'),
);

echo $this->Form->create('Session', array('action' => 'delete'));
echo $this->Form->end();

echo $this->Form->create('MultiSelection', array('class' => 'form-inline'));
?>

<table class="table">
    <thead>
	<tr>
	    <th width="10px">
<?php
echo $this->Form->select(
	'All',
	array('all' => ''),
	array('class' => 'checkbox rangeAll', 'multiple' => 'checkbox', 'hiddenField' => false)
);
?>
	    </th>
<?php
foreach( $columns as $field => $text ) {
    $sort = '';

    if (preg_match("#$field$#", $this->Paginator->sortKey())) {
	$sort = '<i class="' . $sortIcons[$this->Paginator->sortDir()] . '"></i>';
    }

    echo '<th>'
	. $this->Paginator->sort($field, $text . ' ' . $sort, array('escape' => false))
	. '</th>';
}
?>
	    <th><? echo __('Delete'); ?></th>
	</tr>
    </thead>

<?php
if (!empty($radaccts)) {
?>
    <tbody>
<?php
    foreach ($radaccts as $acct) {
?>
	<tr>
	    <td>
<?php
	echo $this->Form->select(
	    'sessions',
	    array($acct['Radacct']['radacctid'] => ''),
	    array('class' => 'checkbox range', 'multiple' => 'checkbox', 'hiddenField' => false)
	);
?>
	    </td>
	    <td>
<?php
	echo $this->Html->link(
	    h($acct['Radacct']['acctuniqueid']),
	    array(
		'controller' => 'Radaccts',
		'action' => 'view',
		$acct['Radacct']['radacctid']
	    )
	);
?>
	    </td>
	    <td>
<?php
	echo h($acct['Radacct']['username']);
?>
	    </td>
	    <td>
<?php
	echo h($acct['Radacct']['framedipaddress']);
?>
	    </td>
	    <td>
<?php
	echo ( !empty( $acct['Radacct']['acctstarttime'] ) ) ? h($acct['Radacct']['acctstarttime']) : __("Unknown");
?>
	    </td>
	    <td>
<?php
	echo ( !empty( $acct['Radacct']['acctstoptime'] ) ) ? h($acct['Radacct']['acctstoptime']) : __("Connected");
?>
	    </td>
	    <td>
<?php
	echo h($acct['Radacct']['nasipaddress']);
?>
	    </td>
	    <td>
<?php
	echo ( !empty( $acct['Radacct']['nasportid'] ) ) ? h($acct['Radacct']['nasportid']) : __("Unknown");
?>
	    </td>
	    <td>
		<i class="icon-remove"></i>
<?php
		echo $this->Html->link(
			__('Delete'),
			'#',
			array(
				'onClick' => "if (confirm('" . __('Are you sure?') . "')) {"
					. "$('#SessionDeleteForm').attr('action',"
					. "$('#SessionDeleteForm').attr('action') + '/" . $acct['Radacct']['radacctid'] . "');"
					. "$('#SessionDeleteForm').submit(); }"
			)
		);
?>
	    </td>
	</tr>
<?php
    }
?>
    </tbody>
<?php
} else {
?>
    <tbody>
	<tr>
	    <td colspan="8"><?php echo __('No session found.'); ?></td>
	</tr>
    </tbody>
<?php
}
?>
</table>

<?php
echo $this->element('dropdownButton', array(
    'buttonCount' => 1,
    'title' => 'Action',
    'icon' => '',
    'items' => array(
	$this->Html->link(
	    '<i class="icon-remove"></i> ' . __('Delete selected'),
	    '#',
	    array(
		'onClick' =>	"$('#selectionAction').attr('value', 'delete');"
		. "if (confirm('" . __('Are you sure?') . "')) {"
		. "$('#MultiSelectionIndexForm').submit();}",
		'escape' => false,
	    )
	),
    )
));
echo $this->Form->end(array(
    'id' => 'selectionAction',
    'name' => 'action',
    'type' => 'hidden',
    'value' => 'delete'
));
unset($acct);
echo $this->element('paginator_footer');
?>

