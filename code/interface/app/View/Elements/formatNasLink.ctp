<?php

if($nas['id'] != -1) {
    $nas = $this->Html->link(
	"<i class='icon-hdd' title='{$nas['ip']}'></i>&nbsp;{$nas['name']}",
	array(
	    'controller' => 'nas',
	    'action' => 'view',
	    $nas['id'],
	),
	array(
	    'escape' => false,
	)
    );
} else {
    $nas = "<span class='unknown'><i class='icon-hdd icon-red' title='{$nas['ip']}'></i>&nbsp;{$nas['name']}</span>";
}

echo $nas;

?>
