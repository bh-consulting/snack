<?php
switch ($action) {
case 'start':
    echo $this->Form->create('MultiSelection', array('class' => 'form-inline'));
    break;
case 'head':
    echo $this->Form->select(
        'All',
        array('all' => ''),
        array(
            'class' => 'checkbox rangeAll',
            'multiple' => 'checkbox',
            'hiddenField' => false,
        )
    );
    break;
case 'line':
    echo $this->Form->select(
        strtolower($name),
        array($id => ''),
        array(
            'class' => 'checkbox range',
            'multiple' => 'checkbox',
            'hiddenField' => false,
        )
    );
break;
case 'end':
    $items = array();

    if (isset($options) && is_array($options)) {
        foreach ($options as $option) {
            switch ($option) {
            case 'delete':
                $items[] = $this->Html->link(
			'<i class="icon-remove"></i> ' . __('Delete selected'),
			"#confirmmultiple",
			array(
			    'escape' => false,
			    'data-toggle' => 'modal',
			    'onclick' => 'countItems()'
			)
		    );

		echo '<div id="modaldel">';
		echo $this->element('modalDelete', array(
		    'id'   => 'multiple',
		    'link' => $this->Html->link(
			    '<i class="icon-remove icon-white"></i> ' . __('Delete 0 items'),
			    '#',
			    array(
				'onclick' => "$('#selectionAction').attr('value', 'delete');"
				. "$('#MultiSelectionIndexForm').submit()",
				'escape' => false,
				'class'  => 'btn btn-primary btn-danger'
			    )
			)
		));
		echo '</div>';

                break;
            case 'export':
                $items[] = $this->Html->link(
                    '<i class="icon-download"></i> ' . __('Export selected'),
                    '#',
                    array(
                        'onClick' => "$('#selectionAction').attr('value', 'export');"
                        . "$('#MultiSelectionIndexForm').submit();",
                        'escape' => false,
                    )
                );
                break;
            }
        }
    }

    echo $this->element('dropdownButton', array(
        'buttonCount' => 1,
        'title' => __('Action'),
        'icon' => '',
        'items' => $items,
    ));

    echo $this->Form->end(array(
        'id' => 'selectionAction',
        'name' => 'action',
        'type' => 'hidden',
        'value' => 'delete'
    ));
    break;
}
?>
