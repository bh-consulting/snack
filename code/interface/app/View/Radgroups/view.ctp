<? 
$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');

echo $this->element('viewInfo', array('title' => 'Group', 'icon' => 'icon-list', 'name' => $radgroup['Radgroup']['groupname'], 'id' => $radgroup['Radgroup']['id'], 'editAction' => 'edit', 'attributes' => $attributes, 'showedAttr' => $showedAttr));
?>
