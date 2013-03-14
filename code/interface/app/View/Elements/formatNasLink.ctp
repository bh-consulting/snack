<?php

if($nas['id'] != -1) {
    $nas = $this->Html->link(
	"<span title='{$nas['ip']}'><i class='icon-hdd'></i>&nbsp;{$nas['name']}</span>",
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
    $nas = "<span class='unknown' title='" . __('NAS unknown') . "'><i class='icon-hdd icon-red'></i>&nbsp;{$nas['ip']}</span>";
}

echo $nas;

?>
