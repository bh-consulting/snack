<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

if(isset($diff) && isset($config)):
?>

<h1><?php echo __('View and restore'); ?></h1>
<h2><?php echo __('Context'); ?></h2>

<ul>
    <li><?php echo $this->Html->link(
	    "<i class='icon-hdd'></i> $nasShortname",
		array(
		    'controller' => 'nas',
		    'action' => 'view',
		    $nasID,
		),
		array(
		    'escape' => false,
		)
	    ) ?> (<?php echo $nasIP ?>)</li>
    <li><?php echo __('<strong>%s:</strong> %s',
			__('When'),
			$dateA) ?></li>
    <li><?php echo __('<strong>%s:</strong> %s',
			__('Who'),
			$this->element('formatUsersList', array(
			    'users' => $usersA
			))) ?></li>
    <li><?php echo __('<strong>%s:</strong> <em>%s</em>',
			__('Why'),
			$actions[$actionA]) ?></li>

    <?php if(empty($diff)): ?>
    <li><strong><?php echo __('This is the current configuration.') ?></strong></li>
    <?php endif; ?>
</ul>

<?php if(!empty($diff)): ?>

<h2><?php echo __('Comparison with the current configuration'); ?></h2>

<div class="toggleBlock" onclick="toggleBlock(this)">
    <?php echo $this->Html->link(__('Show'), '#') ?>
    <i class="icon-chevron-down"></i>
</div>

<pre class="well" style="display: none">
<?php echo trim($diff) ?>
</pre>

<?php endif; ?>

<h2><?php echo __('Contents'); ?></h2>

<div class="toggleBlock" onclick="toggleBlock(this)">
    <?php echo $this->Html->link(__('Show'), '#') ?>
    <i class="icon-chevron-down"></i>
</div>

<div style="display: none">
<pre class="well">
<?php echo trim($config) ?>
</pre>

<?php
if(!empty($diff)) {
    echo $this->Html->link(
	    '<i class="icon-repeat icon-white"></i> ' . __('Restore'),
	    array(
		'controller' => 'backups',
		'action' => 'restore',
		$idA,
		$nasID,
	    ),
	    array(
		'onclick' => "return confirm('" . __('Are you sure?') . "')",
		'escape' => false,
		'class' => 'btn btn-primary',
	    )
	);
}
?>
</div>

<h2><?php echo __('Newer similar backups'); ?></h2>

<?php

$columns = array(
    'id' => __('ID'),
    'datetime' => __('Date'),
    'action' => __('Action'),
    'users' => __('Users'),
);

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
}
?>
	    </tr>
	</thead>

	<tbody>
	<?php if (count($backups) > 1): ?>
<?php
for($i = 0; $i < count($backups); $i++) {
    $backup = $backups[$i];

    echo '<tr>';

    foreach ($columns as $field => $text) {
	if($field == 'id')
	    echo '<td class="smallCol fit" style="font-weight: bold">';
	else if($backup['Backup']['id'] == $backupID)
	    echo '<td style="font-weight: bold">';
	else
	    echo '<td style="font-style: italic">';

	if($field == 'users') {
	    echo $this->element('formatUsersList', array(
		'users' => $users[$backup['Backup']['id']]
	    ));

	} else if($field == 'action') {
	    echo $actions[$backup['Backup'][$field]];

	} else
	    echo $backup['Backup'][$field];

	echo '</td>';
    }

    echo '</tr>';
}
?>
	<?php else: ?>
		<tr>
		<td colspan="5">
		<?php echo __('No similar backups found'); ?>
		</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>

<?php
    echo $this->element('paginator_footer');
?>

<br />
<br />
<br />

<?php
endif;

echo $this->Html->link(
	'<i class="icon-arrow-left icon-white"></i> <i class="icon-camera icon-white"></i> ' . __('Go back to backups'),
	'#',
	array(
	    'onclick' => 'history.go(-1)',
	    'escape' => false,
	    'class' => 'btn btn-primary',
	)
    );
?>
