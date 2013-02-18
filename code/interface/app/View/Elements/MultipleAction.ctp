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
    echo $this->element('dropdownButton', array(
        'buttonCount' => 1,
        'title' => __('Action'),
        'icon' => '',
        'items' => array(
            $this->Html->link(
                '<i class="icon-remove"></i> ' . __('Delete selected'),
                '#',
                array(
                    'onClick' => "$('#selectionAction').attr('value', 'delete');"
                    . "if (confirm('" . __('Are you sure?') . "')) {"
                    . "$('#MultiSelectionIndexForm').submit();}",
                    'escape' => false,
                )
            ),
        )
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
