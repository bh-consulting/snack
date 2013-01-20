<? 
$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');

$deleteButton = '<div class="btn-group">'
	. $this->Html->link(
		'<i class="icon-remove icon-white"></i> ' . __('Delete selected'),
		'#',
		array(
			'class' => 'btn btn-primary',
			'onClick' =>	"$('#selectionAction').attr('value', 'delete');"
			. "if (confirm('" . __('Are you sure?') . "')) {"
			. "$('#MultiSelectionIndexForm').submit();}",
			'escape' => false
		)
	)
	. '</div>';

?>

<h1><? echo __('Groups'); ?></h1>
<?php
echo $deleteButton;
echo '<div class="btn-group">'
	. $this->Html->link(
		__('Add a group'),
		array('controller' => 'radgroups', 'action' => 'add'),
		array('class' => 'btn btn-info')
	)
	. '</div>';

$columns = array(
    'id' => array('text' => __('ID'), 'width' => '40px'),
    'groupname' => array('text' => __('Name')),
    'comment' => array('text' => __('Comment'))
);
?>

<?php
echo $this->Form->create('Radgroups', array('action' => 'delete'));
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
foreach ($columns as $field => $info) {
	$sort = '';

	if (preg_match("#$field$#", $this->Paginator->sortKey())) {
		$sort = '<i class="' . $sortIcons[$this->Paginator->sortDir()] . '"></i>';
	}

	echo '<th'
		. ((isset($info['width'])) ? ' width="' . $info['width'] . '"' : null) . '>'
		. $this->Paginator->sort($field, $info['text'] . ' ' . $sort, array('escape' => false))
		. '</th>';
}
?>
		<th width="90px"><? echo __('Edit'); ?></th>
		<th width="100px"><? echo __('Delete'); ?></th>
	</tr>
	</thead>

	<tbody>
<?php
if (!empty($radgroups)) {
	foreach ($radgroups as $g) {
?>
		<tr>
			<td>
<?php
		echo $this->Form->select(
			'users',
			array($g['Radgroup']['id'] => ''),
			array('class' => 'checkbox range', 'multiple' => 'checkbox', 'hiddenField' => false)
		);
?>
			</td>
			<td>
<?php
		echo $this->Html->link(
			$g['Radgroup']['id'],
			array('controller' => 'Radgroups', 'action' => 'view', $g['Radgroup']['id'])
		);
?>
			</td>
			<td>
<?php
		echo $g['Radgroup']['groupname'];
?>
			</td>
			<td>
<?php
		echo $g['Radgroup']['comment'];
?>
			</td>
			<td>
				<i class="icon-edit"></i>
<?php
		echo $this->Html->link(__('Edit'), array('action' => 'edit', $g['Radgroup']['id']));
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
					. "$('#RadgroupsDeleteForm').attr('action',"
					. "$('#RadgroupsDeleteForm').attr('action') + '/" . $g['Radgroup']['id'] . "');"
					. "$('#RadgroupsDeleteForm').submit(); }"
			)
		);
?>
			</td>
		</tr>
<?php
	}
} else {
?>
		<tr>
			<td colspan="<?php echo count($columns) + 3; ?>"><? echo __('No groups yet.'); ?></td>
		</tr>
<?php
}
?>
	</tbody>
</table>

<?php
echo $deleteButton;
echo $this->Form->end(array(
	'id' => 'selectionAction',
	'name' => 'action',
	'type' => 'hidden',
	'value' => 'delete'
));
echo $this->element('paginator_footer');
unset($g);
?>
