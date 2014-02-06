<? 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo $this->element(
	'viewInfo',
	array(
		'title' => __('MAC User'),
		'glyphicon glyphicon' => 'glyphicon glyphicon-user',
		'name' => $attributes['MAC address'],
		'id' => $raduser['Raduser']['id'],
		'editAction' => 'edit_' . $raduser['Raduser']['type'],
		'attributes' => $attributes,
		'showedAttr' => $showedAttr,
	)
);
?>
