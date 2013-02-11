<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

$columns = array(
    'datetime' => __('Date'),
    'author' => __('Author'),
    'commit' => __('Commit'),
);
?>

<h1><?php echo __('Backups (%s)', $this->data['Nas']['nasname']); ?></h1>

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
	    'name' => 'author',
	    'label' => __('Author contains (accept regex)'),
	    'options' => array('id' => 'author')
	))
    )
);

echo $this->Form->create('Nas', array('action' => 'delete'));
echo $this->Form->end();

?>

<table class="table">
	<thead>
	    <tr>
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
	<?php if(!empty($backups)): ?>
<?php
foreach($backups AS $backup) {
    echo '<tr>';

    foreach($columns as $field => $text) {
	echo '<td>';
	echo $backup[$field];
	echo '</td>';
    }

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
    echo $this->element('paginator_footer');
?>
