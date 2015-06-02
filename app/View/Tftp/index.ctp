
<?php
$this->assign('tftp_active', 'active');
?>
<div class="col-md-12">
<h1>TFTP</h1>
<div class="col-md-offset-2 col-md-6">
<?php
echo $this->Html->link('<i class="glyphicon glyphicon-open glyphicon-white"></i> ' . __('Upload a file'), '#confirmupload',
    array(
        'class' => 'btn btn-success btn-large',
        'escape' => false,
        'data-toggle' => 'modal',
    )
);

echo '<div id="modalimport">';
echo $this->element('modalUpload', array(
    'id'   => 'upload',
    'url' => array(
        'controller' => 'Tftp',
        'action' => 'upload_file',
    ),
    'link' => $this->Html->link(
        '<i class="glyphicon glyphicon-upload glyphicon-white"></i> ' . __('Upload'),
        array(
            'controller' => 'Tftp',
            'action' => 'upload_file',
        ),
        array(
            'escape' => false,
            'class'  => 'btn btn-primary'
        )
    )
));
echo '</div>';
?>
<p></p>
<table class="table table-hover table-stripped table-condensed">
<?php
	foreach ($files as $file)  {
		echo '<tr>';

		echo '<td class="col-md-1"><span class="glyphicon glyphicon-file" aria-hidden="true"></span></td>';
		echo '<td>'.$this->Html->link(
		    $file,
		    array('controller' => 'tftp', 'action' => 'get_file/'.$file),
		    array('escape' => false)
		).'</td>';
		echo '<td class="col-md-1">'.$this->Html->link(
		    '<span class="glyphicon glyphicon-trash" aria-hidden="true">',
		    array('controller' => 'tftp', 'action' => 'delete_file/'.$file),
		    array('escape' => false)
		).'</td>';
		echo '</tr>';
	}
?>
</table>
</div>
</div>