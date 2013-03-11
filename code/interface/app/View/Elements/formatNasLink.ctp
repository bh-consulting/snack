<?php

if($nas['id'] != -1) {
    $nas = $this->Html->link(
	"<i class='icon-hdd' title='{$nas['ip']}'></i> {$nas['name']}",
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
    $nas = $nas['name'];
}

echo $nas;

?>
