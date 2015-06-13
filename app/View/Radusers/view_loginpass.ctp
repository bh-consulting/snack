<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

$servercertificatepem = $this->Html->link(
        "cacert.pem", array(
    'action' => 'get_cert/server',
    'controller' => 'certs',
        )
);

$servercerficatecer = $this->Html->link(
        "cacert.cer", array(
    'action' => 'get_cert/servercer',
    'controller' => 'certs',
        )
);

echo $this->element(
	'viewInfo',
	array(
		'title' => __('Login / Password User'),
		'glyphicon glyphicon' => 'glyphicon glyphicon-user',
		'name' => $raduser['Raduser']['username'],
		'id' => $raduser['Raduser']['id'],
		'editAction' => 'edit_' . $raduser['Raduser']['type'],
		'attributes' => $attributes,
		'showedAttr' => $showedAttr,
	)
);
echo '<dl class="well dl-horizontal">
<dt>For Windows : </dt>
<dd>';
echo $servercertificatepem;
echo '</dd>
</dl>
<dl class="well dl-horizontal">
<dt>For Android / Linux: </dt>
<dd>';
echo $servercerficatecer;
echo '</dd></dl>';

?>