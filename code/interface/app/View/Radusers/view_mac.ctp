<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo $this->element(
	'viewInfo',
	array(
		'title' => __('MAC User'),
		'icon' => 'icon-user',
		'name' => $attributes['MAC address'],
		'id' => $raduser['Raduser']['id'],
		'editAction' => 'edit_' . $raduser['Raduser']['type'],
		'attributes' => $attributes,
		'showedAttr' => $showedAttr,
	)
);
?>
