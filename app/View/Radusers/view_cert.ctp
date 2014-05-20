<?php

$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

$usercertificatep12 = $this->Html->link(
    $attributes['Username'].".p12", 
    array(
    	'action' => 'get_cert_user/' . $attributes['Username'] . '/p12',
    	'controller' => 'certs',
    )
);

$usercertificatepem = $this->Html->link(
    $attributes['Username'].".pem", 
    array(
    	'action' => 'get_cert_user/' . $attributes['Username'] . '/pem',
    	'controller' => 'certs',
    )
);

$usercertificatekey = $this->Html->link(
    $attributes['Username'].".key", 
    array(
    	'action' => 'get_cert_user/' . $attributes['Username'] . '/key',
    	'controller' => 'certs',
    )
);

$servercertificatepem = $this->Html->link(
    "cacert.pem",
    array(
    	'action' => 'get_cert/server',
    	'controller' => 'certs',
    )
);

$servercerficatecer = $this->Html->link(
    "cacert.cer",
    array(
    	'action' => 'get_cert/servercer',
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
<dl class="well dl-horizontal">
    <dt>For Windows : </dt>
    <dd><?php echo $servercertificatepem; ?></dd>
    <dt></dt>
    <dd><?php echo $usercertificatep12; ?></dd>
</dl>

<dl class="well dl-horizontal">
    <dt>For Android / Linux: </dt>
    <dd><?php echo $servercerficatecer; ?></dd>
    <dt></dt>
    <dd><?php echo $usercertificatepem; ?></dd>
    <dt></dt>
    <dd><?php echo $usercertificatekey; ?></dd>
</dl> 
