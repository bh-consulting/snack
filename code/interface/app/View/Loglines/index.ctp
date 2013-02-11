<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('logs_active', 'active');

$columns = array(
    'id' => __('Line'),
    'datetime' => __('Date'),
    'level' => __('Severity'),
    'msg' => __('Message')
);
?>

<h1><? echo __('Logs'); ?></h1>

<?php
echo $this->element('filters_panel', array(
    'controller' => 'loglines/index',
    'inputs' => array(
	array(
	    'name' => 'level',
	    'label' => __('Severity from'),
	    'type' => 'slidermax',
	    'options' => array('id' => 'severity')
	),
	array(
	    'name' => 'datefrom',
	    'label' => __('From'),
	    'type' => 'datetimepicker',
	    'options' => array('id' => 'datefrom')
	),
	array(
	    'name' => 'dateto',
	    'label' => __('To'),
	    'type' => 'datetimepicker',
	    'options' => array('id' => 'dateto')
	),
	array(
	    'name' => 'msg',
	    'label' => __('Message contains (accept regex)'),
	    'options' => array('id' => 'logmessage')
	))
    )
);

echo $this->Form->create('Logs', array('action' => 'delete'));
echo $this->Form->end();

echo $this->Form->create('MultiSelection', array('class' => 'form-inline'));
?>

<table class="table loglinks">
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
foreach($columns as $field => $text) {
    $sort = '';

    if (preg_match("#$field$#", $this->Paginator->sortKey())) {
	$sort = '<i class="' . $sortIcons[$this->Paginator->sortDir()] . '"></i>';
    }

    echo '<th>'
	. $this->Paginator->sort($field, "$text $sort", array('escape' => false))
	. '</th>';
}
?>
	    </tr>
	</thead>

	<tbody>
	<?php if(!empty($loglines)): ?>
<?php
foreach($loglines AS $logline) {
    echo "<tr class='loglevel{$logline['Logline']['level']}'>";
?>
		<td>
<?php
    echo $this->Form->select(
	'logs',
	array($logline['Logline']['id'] => ''),
	array('class' => 'checkbox range', 'multiple' => 'checkbox', 'hiddenField' => false)
    );
?>
		</td>
<?php
    foreach($columns as $field => $text) {
	echo "<td class='logcell$field'>";

	if($field == 'datetime')
	    echo $this->Html->link(__($logline['Logline'][$field]), '#', array('onclick' => 'logsSearchFromDate($(this))', 'title' => __('Search from this date')));
	else if($field == 'level')
	    echo $this->Html->link(__($logline['Logline'][$field]), '#', array('onclick' => 'logsSearchFromSeverity($(this))', 'title' => __('Search from this severity')));
	else
	    echo $logline['Logline'][$field];

	echo '</td>';
    }

    echo '</tr>';
}
?>
	<?php else: ?>
		<tr>
		<td colspan="5">
<?php
echo __('No logs found').' (';

if(count($this->params['url']) > 0)
    echo $this->Html->link(__('retry with no filters'), '.');
else
    echo __('no filters');

echo ').';
?>
		</td>
		</tr>
	<?php endif; ?>
	</tbody>
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
echo $this->element('paginator_footer');
?>
