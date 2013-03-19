<? 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

if($attributes['Check server certificate'] == 'EAP-TTLS')
    $attributes['Check server certificate'] = 'Yes (TTLS)';
else
    $attributes['Check server certificate'] = 'No';

echo $this->element(
	'viewInfo',
	array(
		'title' => __('Login / Password User'),
		'icon' => 'icon-user',
		'name' => $raduser['Raduser']['username'],
		'id' => $raduser['Raduser']['id'],
		'editAction' => 'edit_' . $raduser['Raduser']['type'],
		'attributes' => $attributes,
		'showedAttr' => $showedAttr,
	)
);
?>
