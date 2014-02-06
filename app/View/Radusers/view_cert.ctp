<?php

$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

$attributes['User certificate path'] = $this->Html->link(
    $attributes['User certificate path'], 
    array(
    	'action' => 'get_cert/' . $attributes['Username'],
    	'controller' => 'certs',
    )
);

$attributes['Server certificate path'] = $this->Html->link(
    $attributes['Server certificate path'],
    array(
    	'action' => 'get_cert/server',
    	'controller' => 'certs',
    )
);

echo $this->element(
	'viewInfo',
	array(
		'title' => __('Certificate user'),
		'glyphicon glyphicon' => 'glyphicon glyphicon-user',
		'name' => $raduser['Raduser']['username'],
		'id' => $raduser['Raduser']['id'],
		'editAction' => 'edit_' . $raduser['Raduser']['type'],
		'attributes' => $attributes,
		'showedAttr' => $showedAttr,
	)
);
?>
