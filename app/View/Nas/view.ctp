<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

echo $this->element('viewInfo', array(
    'title' => 'NAS',
    'icon' => 'glyphicon glyphicon-hdd',
    'name' => $nas['Nas']['shortname'],
    'id' => $nas['Nas']['id'],
    'editAction' => 'edit',
    'attributes' => $attributes,
    'showedAttr' => $showedAttr)
);

echo '<strong>';

if(in_array($nas['Nas']['nasname'], $unBackupedNas)) {
    echo '<i class="glyphicon glyphicon-warning-sign text-danger"></i> ';

            echo __('NOT Backuped from 7 days or more',
        	$this->Html->link(
                    __('changes saved'),
                    array(
                        'action' => 'index',
                        'controller' => 'backups',
                        $nas['Nas']['id'],
                    )
                )
            );
} else {
    echo '<i class="glyphicon glyphicon-ok text-success"></i> ';
    echo __('Backuped');
}

echo '</strong>';

?>
