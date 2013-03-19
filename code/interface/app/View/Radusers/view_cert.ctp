<?php

$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

$attributes['Certificate path'] = $this->Html->link(
    __($attributes['Certificate path']), 
    array(
    	'action' => 'get_public/' . $attributes['Username'],
    	'controller' => 'certs',
    )
);

$attributes['Key path'] = $this->Html->link(
    __($attributes['Key path']), 
    array(
    	'action' => 'get_key/' . $attributes['Username'],
    	'controller' => 'certs',
    )
);

$attributes['Server certificate path'] = $this->Html->link(
    __($attributes['Server certificate path']),
    array(
    	'action' => 'get_public/server',
    	'controller' => 'certs',
    )
);

echo $this->element(
	'viewInfo',
	array(
		'title' => __('Certificate user'),
		'icon' => 'icon-user',
		'name' => $raduser['Raduser']['username'],
		'id' => $raduser['Raduser']['id'],
		'editAction' => 'edit_' . $raduser['Raduser']['type'],
		'attributes' => $attributes,
		'showedAttr' => $showedAttr,
	)
);
?>
