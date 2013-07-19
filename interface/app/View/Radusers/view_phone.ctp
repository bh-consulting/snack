<? 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

if (isset($attributes['EAP-Type'])
    && $attributes['EAP-Type'] == 'EAP-TTLS'
) {
    $attributes['Server certificate path'] = $this->Html->link(
        __($attributes['Server certificate path']),
        array(
            'action' => 'get_cert/server',
            'controller' => 'certs',
        )
    );
} else if (array_search('Server certificate path', $showedAttr)) {
    unset($showedAttr[array_search('Server certificate path', $showedAttr)]);
}

echo $this->element(
	'viewInfo',
	array(
		'title' => __('Cisco Phone'),
		'icon' => 'icon-user',
		'name' => $raduser['Raduser']['username'],
		'id' => $raduser['Raduser']['id'],
		'editAction' => 'edit_' . $raduser['Raduser']['type'],
		'attributes' => $attributes,
		'showedAttr' => $showedAttr,
	)
);
?>
