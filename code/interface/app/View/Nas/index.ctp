<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');
?>

<h1><?php echo __('NAS'); ?></h1>
<?php
echo $this->Html->link(
    '<i class="icon-hdd icon-white"></i> ' . __('Add a NAS'),
    array('controller' => 'nas', 'action' => 'add'),
    array('escape' => false, 'class' => 'btn btn-primary')
);

$columns = array(
    'id' => array('text' => __('ID'), 'fit' => true),
    'nasname' => array('text' => __('Name')),
    'shortname' => array('text' => __('Short name')),
    'description' => array('text' => __('Description')),
    'type' => array('text' => __('Type'), 'fit' => true),
);

echo $this->Form->create('Nas', array('action' => 'delete'));
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
foreach ($columns as $field => $info) {
    $sort = '';

    if (preg_match("#$field$#", $this->Paginator->sortKey())) {
	$sort = '<i class="'
	    . $sortIcons[$this->Paginator->sortDir()]
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
	    <th class="fit smallCol"><? echo __('View'); ?></th>
	    <th class="fit smallCol"><? echo __('Edit'); ?></th>
	    <th class="fit smallCol"><? echo __('Delete'); ?></th>
	    <th class="fit smallCol"><? echo __('Backups'); ?></th>
	</tr>
    </thead>

    <tbody>
<?php
if (!empty($nas)) {
    foreach ($nas as $n) {
?>
	<tr>
	    <td class="fit smallCol">
<?php
	echo $this->Form->select(
	    'nas',
	    array($n['Nas']['id'] => ''),
	    array(
		'class' => 'checkbox range',
		'multiple' => 'checkbox',
		'hiddenField' => false,
	    )
	);
?>
	    </td>
	    <td class="fit smallCol"><strong> <?php echo $n['Nas']['id'] ?></strong> </td>
	    <td>
<?php
    echo $n['Nas']['nasname'];
?>
	    </td>
	    <td>
<?php
    echo $n['Nas']['shortname'];
?>
	    </td>
	    <td>
<?php
    echo $n['Nas']['description'];
?>
	    </td>
	    <td class="fit">
<?php
    echo $n['Nas']['type'];
?>
	    </td>
	    <td class="fit smallCol">
		<i class="icon-eye-open"></i>
<?php
    echo $this->Html->link(
	__('View'),
	array('action' => 'view', 'controller' => 'nas', $n['Nas']['id'])
    );
?>
	    </td>

	    <td class="fit smallCol">
		<i class="icon-edit"></i>
<?php
    echo $this->Html->link(
	__('Edit'),
	array('action' => 'edit', $n['Nas']['id'])
    );
?>
	    </td>
	    <td class="fit smallCol">
		<i class="icon-remove"></i>
<?php
    echo $this->Html->link(
	__('Delete'),
	'#',
	array(
	    'onClick' => "if (confirm('" . __('Are you sure?') . "')) {"
	    . "$('#NasDeleteForm').attr('action',"
	    . "$('#NasDeleteForm').attr('action') + '/"
	    . $n['Nas']['id'] . "');"
	    . "$('#NasDeleteForm').submit(); }"
	)
    );
?>
	    </td>
	    <td class="fit">
		<i class="icon-camera"></i>
<?php
    echo $this->Html->link(
	__('Backups'),
	array('action' => 'index', 'controller' => 'backups', $n['Nas']['id'])
    );
?>
	    </td>

	</tr>
<?php
    }
} else {
?>
	<tr>
	    <td colspan="<?php echo count($columns)+3; ?>">
<?php
    echo __('No NAS yet.');
?>
	    </td>
	</tr>
<?
}
?>
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
echo $this->Form->end();
echo $this->element('paginator_footer');
unset($n);
?>
