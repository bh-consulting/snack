<? 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

echo $this->element('viewInfo', array(
    'title' => 'NAS',
    'icon' => 'icon-hdd',
    'name' => $nas['Nas']['shortname'],
    'id' => $nas['Nas']['id'],
    'editAction' => 'edit',
    'attributes' => $attributes,
    'showedAttr' => $showedAttr)
);

echo '<strong>';

if($isunwritten) {
    echo '<i class="icon-camera icon-red"></i> ';
    echo __('There are %s not saved on the memory.',
	$this->Html->link(
            __('some changes'),
            array(
                'action' => 'index',
                'controller' => 'backups',
                $nas['Nas']['id'],
            )
        )
    );
} else {
    echo '<i class="icon-camera icon-green"></i> ';
    echo __('All %s on the memory.',
	$this->Html->link(
            __('changes saved'),
            array(
                'action' => 'index',
                'controller' => 'backups',
                $nas['Nas']['id'],
            )
        )
    );
}

echo '</strong>';

?>
