<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

$columns = array(
    'id' => __('ID'),
    'datetime' => __('When'),
    'action' => __('Why'),
    'users' => __('Who'),
);
?>

<h1><?php echo __('Backups of %s (%s)', $nasShortname, $nasIP); ?></h1>

<?php
echo $this->element('filters_panel', array(
    'controller' => 'backups/index/' . $nasID,
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
            'name' => 'author',
            'label' => __('Author contains (accept regex)'),
            'options' => array('id' => 'author')
        ),
        array(
            'name' => 'action',
            'label' => __('Action'),
            'multiple' => 'checkbox',
            'type' => 'checkgroup check-horizontal',
        ),
        array(
            'name' => 'writemem',
            'label' => __('Synchronization'),
            'multiple' => 'checkbox',
            'type' => 'checkgroup check-horizontal',
            'escape' => false,
        ),
    )
));

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

    echo '<th '.($field == 'id' ? 'class="smallCol fit"' : '').'>'
	. $this->Paginator->sort($field, "$text $sort", array('escape' => false))
	. '</th>';

    if ($field == 'id') {
	echo '<th class="smallCol"><i class="icon-ok-circle" title="' . __('Write memory') . '"></i></th>';
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
$n = 0;

for($i = 0; $i < count($backups); $i++) {
    $backup = $backups[$i];

    echo '<tr>';

    foreach ($columns as $field => $text) {
	echo '<td '.($field == 'id' ? 'class="smallCol fit" style="font-weight: bold"' : '').'>';

	if($field == 'users') {
	    echo $this->element('formatUsersList', array(
		'users' => $users[$backup['Backup']['id']]
	    ));
	} else if($field == 'datetime') {
	    echo $this->element('formatDates', array(
		'date' => $backup['Backup'][$field]
	    ));
	} else if($field == 'action') {
	    echo '<strong>';
	    switch($backup['Backup'][$field]) {
		case 'logoff':
		    echo __('Log off');
		    break;
		case 'login':
		    echo __('Log in');
		    break;
		case 'wrmem':
		    echo __('Write memory');
		    break;
		default:
		    echo $backup['Backup'][$field];
	    }
	    echo '</strong>';
	} else
	    echo $backup['Backup'][$field];

	echo '</td>';

	if ($field == 'id') {
	    if(in_array($backup['Backup']['id'], $unwrittenids)) {
		echo '<td class="smallCol fit">';
		echo '<i class="icon-exclamation-sign icon-red" title="' . __('Configuration NOT loaded if the device restart.') .'"></i>';
	    } else {
		echo '<td class="smallCol fit">';
		echo '<i class="icon-ok-sign icon-green" title="' . __('Seems saved as starting configuration.') .'"></i>';
	    }

	    echo '</td>';

	    echo '<td class="smallCol fit">';

	    if ($i != 0)
		echo $this->Form->radio(
		    'b',
		    array($backup['Backup']['id'] => ''),
		    array(
			'hiddenField' => false,
			'checked' => $i == 1,
			'set' => $n
		    )
		);
	    
	    echo '</td><td class="smallCol fit">';

	    if ($i != count($backups)-1)
		echo $this->Form->radio(
		    'a',
		    array($backup['Backup']['id'] => ''),
		    array(
			'hiddenField' => false,
			'checked' => $i == 0,
			'set' => $n
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

    $n++;
}
?>
	<?php else: ?>
		<tr>
		<td colspan="<?php echo count($columns) + 4; ?>" style="text-align: center">
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
