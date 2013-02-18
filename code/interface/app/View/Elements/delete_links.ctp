<?php
switch ($action) {
case 'form':
    echo $this->Form->create($model, array('action' => 'delete'))
        . $this->Form->hidden('id')
        . $this->Form->end();
    break;
case 'link':
    echo $this->Html->link(
        __('Delete'),
        '#',
        array(
            'onClick' => "if (confirm('" . __('Are you sure?') . "')) {"
            . "$('#" . $model . "DeleteForm input').val('"
            . $id . "');"
            . "$('#" . $model . "DeleteForm').submit(); }"
        )
    );
    break;
}
?>
