<? 
$this->extend('/Common/radius_sidebar');
$this->assign('nas_active', 'active');

echo $this->element('viewInfo', array('title' => 'NAS', 'icon' => 'icon-hdd', 'name' => $nas['Nas']['nasname'], 'id' => $nas['Nas']['id'], 'editAction' => 'edit', 'attributes' => $attributes, 'showedAttr' => $showedAttr));
?>
