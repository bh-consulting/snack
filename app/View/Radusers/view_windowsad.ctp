<? 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo $this->element(
	'viewInfo',
	array(
		'title' => __('Windows AD User'),
		'glyphicon glyphicon' => 'glyphicon glyphicon-user',
		'name' => $raduser['Raduser']['username'],
		'id' => $raduser['Raduser']['id'],
		'editAction' => 'edit_' . $raduser['Raduser']['type'],
		'attributes' => $attributes,
		'showedAttr' => $showedAttr,
	)
);
?>
