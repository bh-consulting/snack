<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('groups_active', 'active');

echo $this->element(
    'viewInfo',
    array(
        'title' => 'Group',
        'icon' => 'icon-list',
        'name' => $radgroup['Radgroup']['groupname'],
        'id' => $radgroup['Radgroup']['id'],
        'editAction' => 'edit',
        'attributes' => $attributes,
        'showedAttr' => $showedAttr,
    )
);
?>
