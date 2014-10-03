<?php
$nextpage=$page+1;
$prevpage=$page-1;
if ($page == 1 && $totalPages == 1) {
    $link=$this->Html->tag(
            "li", $this->Html->link('&larr; Prev', array('file' => $file, 'page' => $prevpage, '?' => $this->params['url']), array('escape' => false)),  array('class' => 'previous disabled')
    );
    $link.=$this->Html->tag(
            "li", $this->Html->link($page." / ".$totalPages, '#'), array('class' => 'previous'));
    $link.=$this->Html->tag(
            "li", $this->Html->link('Next &rarr;', array('file' => $file, 'page' => $nextpage, '?' => $this->params['url']), array('escape' => false)), array('class' => 'next disabled')
    );
}
elseif ($page == 1) {
    $link=$this->Html->tag(
            "li", $this->Html->link('&larr; Prev', array('file' => $file, 'page' => $prevpage, '?' => $this->params['url']), array('escape' => false)),  array('class' => 'previous disabled')
    );
    $link.=$this->Html->tag(
            "li", $this->Html->link($page." / ".$totalPages, '#'), array('class' => 'previous'));
    $link.=$this->Html->tag(
            "li", $this->Html->link('Next &rarr;', array('file' => $file, 'page' => $nextpage, '?' => $this->params['url']), array('escape' => false)), array('class' => 'next')
    );
}
elseif ($page==$totalPages) {
    $link=$this->Html->tag(
            "li", $this->Html->link('&larr; Prev', array('file' => $file, 'page' => $prevpage, '?' => $this->params['url']), array('escape' => false)),  array('class' => 'previous')
    );
    $link.=$this->Html->tag(
            "li", $this->Html->link($page." / ".$totalPages, '#'), array('class' => 'previous'));
    $link.=$this->Html->tag(
            "li", $this->Html->link('Next &rarr;', array('file' => $file, 'page' => $nextpage, '?' => $this->params['url']), array('escape' => false)), array('class' => 'next disabled')
    );
}
else {
    $link=$this->Html->tag(
            "li", $this->Html->link('&larr; Prev', array('file' => $file, 'page' => $prevpage, '?' => $this->params['url']), array('escape' => false)),  array('class' => 'previous')
    );
    $link.=$this->Html->tag(
            "li", $this->Html->link($page." / ".$totalPages, '#'), array('class' => 'previous'));
    $link.=$this->Html->tag(
            "li", $this->Html->link('Next &rarr;', array('file' => $file, 'page' => $nextpage, '?' => $this->params['url']), array('escape' => false)), array('class' => 'next')
    );
}
echo $this->Html->tag(
    'ul',
    $link,
    array('class' => 'pager', 'style' => 'float:left;')
);
?>
