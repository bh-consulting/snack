<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

$actionButton = $this->element('dropdownButton', array(
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
		$this->Html->link(
			'<i class="icon-share"></i> ' . __('Export selected'),
			'#',
			array(
				'onClick' =>	"$('#selectionAction').attr('value', 'export');"
				. "$('#MultiSelectionIndexForm').submit();",
				'escape' => false,
			)
		),
	)
));
?>

<h1><? echo __('Users'); ?></h1>
<?php
echo $actionButton;
echo $this->element('dropdownButton', array(
	'buttonCount' => 1,
	'class' => 'btn-info',
	'title' => __('Add user'),
	'icon' => 'icon-user',
	'items' => array(
		$this->Html->link(
			'<i class="icon-plus-sign"></i> ' . __('Cisco'), 
			array('action' => 'add_cisco'),
			array('escape' => false)
		),
		$this->Html->link(
			'<i class="icon-plus-sign"></i> ' . __('Login / Password'), 
			array('action' => 'add_loginpass'),
			array('escape' => false)
		),
		$this->Html->link(
			'<i class="icon-plus-sign"></i> ' . __('Certificate'), 
			array('action' => 'add_cert'),
			array('escape' => false)
		),
		$this->Html->link(
			'<i class="icon-plus-sign"></i> ' . __('MAC address'), 
			array('action' => 'add_mac'),
			array('escape' => false)
		),
		$this->Html->link(
			'<i class="icon-plus"></i> ' . __('Upload CSV'), 
			array('action' => 'add_csv'),
			array('escape' => false)
		),
	)
));
?>

<?php
$columns = array(
	'id' => array('text' => __('ID'), 'width' => '40px'),
	'username' => array('text' => __('Username')),
	'comment' => array('text' => __('Comment')),
	'ntype' => array('text' => __('Type'))
);

echo $this->Form->create('Radusers', array('action' => 'delete'));
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
		$sort = '<i class="' .  $sortIcons[$this->Paginator->sortDir()] . '"></i>';
	}

	echo '<th' . ((isset($info['width'])) ? ' width="' . $info['width'] . '"' : null) . '>'
		. $this->Paginator->sort($field, $info['text'] . ' '. $sort, array('escape' => false))
		. '</th>';
}
?>
		<th width="90px"><? echo __('Edit'); ?></th>
		<th width="100px"><? echo __('Delete'); ?></th>
	</tr>
	</thead>

	<tbody>
<?php
if (!empty($radusers)) {
	foreach ($radusers as $rad) {
?>
		<tr>
			<td>
<?php
		echo $this->Form->select(
			'users',
			array($rad['Raduser']['id'] => ''),
			array('class' => 'checkbox range', 'multiple' => 'checkbox', 'hiddenField' => false)
		);
?>
			</td>
			<td>
<?php
		echo $this->Html->link(
			$rad['Raduser']['id'],
			array(
				'controller' => 'Radusers',
				'action' => 'view_' . $rad['Raduser']['type'],
				$rad['Raduser']['id']
			)
		);
?>
			</td>
			<td>
<?php
		echo $rad['Raduser']['username'];
?>
			</td>
			<td>
<?php
		echo $rad['Raduser']['comment'];
?>
			</td>
			<td>
<?php
		echo $rad['Raduser']['ntype'];
?>
			</td>
			<td>
				<i class="icon-edit"></i>
<?php
		echo $this->Html->link(
			__('Edit'),
			array('action' => 'edit_' . $rad['Raduser']['type'], $rad['Raduser']['id'])
		);
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
					. "$('#RadusersDeleteForm').attr('action',"
					. "$('#RadusersDeleteForm').attr('action') + '/" . $rad['Raduser']['id'] . "');"
					. "$('#RadusersDeleteForm').submit(); }"
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
		<td colspan="<?php echo count($columns) + 3; ?>">
<?php
	echo __('No users yet.');
?>
			</td>
		</tr>
<?
}
?>
	</tbody>
</table>
<?php
echo $actionButton;
echo $this->Form->end(array('id' => 'selectionAction', 'name' => 'action', 'type' => 'hidden'));
echo $this->element('paginator_footer');
unset($rad);
?>
