<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo $this->element('viewInfo', array('title' => 'Login / Password User', 'icon' => 'icon-user', 'name' => $raduser['Raduser']['username'], 'id' => $raduser['Raduser']['id'], 'editAction' => 'edit_' . $raduser['Raduser']['type'], 'attributes' => $attributes, 'showedAttr' => $showedAttr));
?>
