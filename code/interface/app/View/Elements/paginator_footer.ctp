<?
    $paginate = $this->Paginator->prev('< Previous', array(), null, array('class' => 'disabled')).$this->Paginator->numbers(array(
        'modulus' => 2,
        'first' => 2,
        'last' => 2,
        'ellipsis' => "<span class='disabled'>...</span>",
        'separator' => '',  
        'currentClass' => 'disabled'

    )).$this->Paginator->next('Next >', array(), null, array('class' => 'disabled'));

    echo $this->Html->tag('div', $paginate, array('class' => 'pagination pagination-small', 'style' => 'float:left;'));
?>
<br />
<div style="float:right;">
<?php
    echo $this->Paginator->counter(array('format' => 'range'));
    $this->Paginator->options(array('url' => $this->Paginator->params['pass']));
?>