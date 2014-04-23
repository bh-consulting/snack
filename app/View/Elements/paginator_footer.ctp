<?php
$paginate = $this->Paginator->prev(
    '&larr; Previous',
    array('tag' => 'li', 'class' => 'previous', 'escape' => false),
    '<a href="#">&larr; Previous</a>',
    array('tag' => 'li', 'class' => 'previous disabled', 'escape' => false)
) . 
$this->Paginator->next(
    'Next &rarr;',
    array('tag' => 'li', 'class' => 'previous', 'escape' => false),
    '<a href="#">Next &rarr;</a>',
    array('tag' => 'li', 'class' => 'next disabled', 'escape' => false)
);

echo $this->Html->tag(
    'ul',
    $paginate,
    array('class' => 'pager', 'style' => 'float:left;')
);
?>
<br />

