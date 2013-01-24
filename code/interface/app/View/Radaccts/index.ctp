<?php
$this->extend('/Common/radius_sidebar');
$this->assign('monitoring_active', 'active');
?>

<h1><?php echo __('Sessions'); ?></h1>
<?php
$columns = array(
    'acctuniqueid' => array('text' => __('ID'), 'fit' => true),
    'username' => array('text' => __('Username')),
    'callingstationid' => array('text' => __('IP'), 'fit' => true),
    'acctstarttime' => array('text' => __('Start'), 'fit' => true),
    'acctstoptime' => array('text' => __('Stop'), 'fit' => true),
    'nasipaddress' => array('text' => __('NAS IP'), 'fit' => true),
    'nasportid' => array('text' => __('NAS Port'), 'fit' => true),
);

echo $this->Form->create('Session', array('action' => 'delete'));
echo $this->Form->end();

echo $this->Form->create('MultiSelection', array('class' => 'form-inline'));
?>

<table class="table">
    <thead>
	<tr>
	    <th class="fit">
<?php
echo $this->Form->select(
    'All',
    array('all' => ''),
    array(
	'class' => 'checkbox rangeAll',
	'multiple' => 'checkbox',
	'hiddenField' => false,
    )
);
?>
	    </th>
<?php
foreach( $columns as $field => $info ) {
    $sort = '';

    if (preg_match("#$field$#", $this->Paginator->sortKey())) {
	$sort = '<i class="'
	    .  $sortIcons[$this->Paginator->sortDir()]
	    . '"></i>';
    }

    if (isset($info['fit']) && $info['fit']) {
	echo '<th class="fit">';
    } else {
	echo '<th>';
    }

    echo $this->Paginator->sort(
	$field,
	$info['text'] . ' '. $sort,
	array('escape' => false)
    )
    . '</th>';
}
?>
	    <th class="fit"><? echo __('Delete'); ?></th>
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
	    <td class="fit">
<?php
	echo $this->Form->select(
	    'sessions',
	    array($acct['Radacct']['radacctid'] => ''),
	    array(
		'class' => 'checkbox range',
		'multiple' => 'checkbox',
		'hiddenField' => false,
	    )
	);
?>
	    </td>
	    <td class="fit">
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
	    <td class="fit">
<?php
	echo h($acct['Radacct']['framedipaddress']);
?>
	    </td>
	    <td class="fit">
<?php
	echo ( !empty( $acct['Radacct']['acctstarttime'] ) ) ? 
	    h($acct['Radacct']['acctstarttime']) : __("Unknown");
?>
	    </td>
	    <td class="fit">
<?php
	echo ( !empty( $acct['Radacct']['acctstoptime'] ) ) ? 
	    h($acct['Radacct']['acctstoptime']) : __("Connected");
?>
	    </td>
	    <td class="fit">
<?php
	echo h($acct['Radacct']['nasipaddress']);
?>
	    </td>
	    <td class="fit">
<?php
	echo ( !empty( $acct['Radacct']['nasportid'] ) ) ? 
	    h($acct['Radacct']['nasportid']) : __("Unknown");
?>
	    </td>
	    <td class="fit">
		<i class="icon-remove"></i>
<?php
	echo $this->Html->link(
	    __('Delete'),
	    '#',
	    array(
		'onClick' => "if (confirm('" . __('Are you sure?') . "')) {"
		. "$('#SessionDeleteForm').attr('action',"
		. "$('#SessionDeleteForm').attr('action') + '/"
		. $acct['Radacct']['radacctid'] . "');"
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
	    <td colspan="<?php echo count($columns)+2; ?>">
<?php
    echo __('No session found.');
?>
	    </td>
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

