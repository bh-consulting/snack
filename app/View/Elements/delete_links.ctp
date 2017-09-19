<?php
switch ($action) {
    case 'form':
    echo $model;
	echo $this->Form->create($model."Delete", array('url' => 'delete'))
	    . $this->Form->hidden('id')
	    . $this->Form->end();

	break;

    case 'link':
    if (AuthComponent::user('role') != 'root') {
    	echo '<span class="unknown" title="'
                        . __('Not allowed!')
                        . '">'
                        . '<i class="glyphicon glyphicon-remove glyphicon-red" data-toggle="tooltip" data-placement="top" title='.__('Delete').'></i> </span>';
    } else {
		echo "<a href='#confirm$id' data-toggle='modal'><i class='glyphicon glyphicon-remove' data-toggle='tooltip' data-placement='top' title=".__('Delete')."></i></a>";

		echo $this->element('modalDelete', array(
		    'id'   => $id,
		    'link' => $this->Html->link(
			    '<i class="glyphicon glyphicon-remove glyphicon-white"></i> ' . __('Delete'),
			    '#',
			    array(
				'onclick' => "$('#{$model}DeleteId').val('$id');"
				    . "$('#{$model}DeleteIndexForm').submit()",
				'escape' => false,
				'class' => 'btn btn-primary btn-danger'
			    )
			)
		));
	}
	break;
}
?>
