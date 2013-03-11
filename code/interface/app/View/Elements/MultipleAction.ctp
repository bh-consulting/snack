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
                    '#',
                    array(
                        'onClick' => "$('#selectionAction').attr('value', 'delete');"
                        . "if (confirm('" . __('Are you sure?') . "')) {"
                        . "$('#MultiSelectionIndexForm').submit();}",
                        'escape' => false,
                    )
                );
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
