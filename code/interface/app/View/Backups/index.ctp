<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

$columns = array(
    'id' => __('ID'),
    'datetime' => __('Date'),
    'commit' => __('Commit'),
    'nas' => __('NAS'),
    'action' => __('Action'),
    'users' => __('Users'),
);
?>

<h1><?php echo __('Backups of %s (%s)', $nasShortname, $nasIP); ?></h1>

<?php
echo $this->element('filters_panel', array(
    'controller' => 'nas/backups',
    'inputs' => array(
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
	    'name' => 'users',
	    'label' => __('User(s) contains (accept regex)'),
	    'options' => array('id' => 'users')
	))
    )
);

echo $this->Form->create('SelectDiff', array(
	'url' => array(
		'controller' => 'backups',
		'action' => 'diff',
	),
	'type' => 'get',
	'class' => 'form-inline',
));
?>

<table class="table tableBackups">
	<thead>
	    <tr>
<?php
foreach ($columns as $field => $text) {
    $sort = '';

    if (preg_match("#$field$#", $this->Paginator->sortKey())) {
	$sort = '<i class="' . $sortIcons[$this->Paginator->sortDir()] . '"></i>';
    }

    echo '<th '.($field == 'id' ? 'class="smallCol"' : '').'>'
	. $this->Paginator->sort($field, "$text $sort", array('escape' => false))
	. '</th>';

    if ($field == 'id') {
	echo '<th class="smallCol" colspan="2"><i class="icon-zoom-in"></i></th>';
    }
}

echo '<th class="smallCol">' . __('View') . '</th>';
?>
	    </tr>
	</thead>

	<tbody>
	<?php if (!empty($backups)): ?>
<?php
for($i = 0; $i < count($backups); $i++) {
    $backup = $backups[$i];

    echo '<tr>';

    foreach ($columns as $field => $text) {
	echo '<td '.($field == 'id' ? 'class="smallCol" style="font-weight: bold"' : '').'>';
	echo $backup['Backup'][$field];
	echo '</td>';

	if ($field == 'id') {
	    echo '<td class="smallCol">';

	    if ($i != 0)
		echo $this->Form->radio(
		    'b',
		    array($backup['Backup']['id'] => ''),
		    array(
			'hiddenField' => false,
			'checked' => $i == 1,
		    )
		);
	    
	    echo '</td><td class="smallCol">';

	    if ($i != count($backups)-1)
		echo $this->Form->radio(
		    'a',
		    array($backup['Backup']['id'] => ''),
		    array(
			'hiddenField' => false,
			'checked' => $i == 0,
		    )
		);

	    echo '</td>';
	}
    }

    echo '<td class="smallCol"><i class="icon-eye-open"></i> ';
    echo $this->Html->link(
	__('View'),
	array(
	    'controller' => 'backups',
	    'action' => 'view',
	    $backup['Backup']['id'],
	    $nasID,
	)
    );
    echo '</td>';

    echo '</tr>';
}
?>
	<?php else: ?>
		<tr>
		<td colspan="5">
<?php
echo __('No backups found').' (';

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

echo $this->Form->button('<i class="icon-zoom-in icon-white"></i> ' . __('Compare'),
    array(
	'type' => 'submit',
	'escape' => false,
	'class' => 'btn btn-primary',
    )
);

echo $this->Form->hidden('nas',
    array('value' => $nasID)
);

echo $this->Form->end();

?>

<?php
    echo $this->element('paginator_footer');
?>
