<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

/*
 * Define fields to show.
 */
$columns = array(
    'checkbox' => array(
        'id' => 'radacctid',
        'fit' => true,
    ),
    'id' => array(
        'text' => __('ID'),
        'fit' => true,
    ),
    'shortname' => array(
        'text' => __('Name'),
    ),
    'nasname' => array(
        'text' => __('IP address'),
    ),
    'description' => array(
        'text' => __('Description'),
    ),
    'version' => array(
        'text' => __('Version'),
    ),
    'image' => array(
        'text' => __('Image'),
    ),
    'serialnumber' => array(
        'text' => __('Serial number'),
    ),
    'model' => array(
        'text' => __('Model'),
    ),
    'action' => array(
        'text' => __('Action'),
        'fit' => true,
    ),
    'backups' => array(
        'text' => __('Backups'),
        'fit' => true,
    ),
    'backuptype' => array(
        'text' => __('Type'),
        'fit' => true,
    ),
);

if(AuthComponent::user('role') != 'root'){
    unset($columns['edit']);
    unset($columns['delete']);
    unset($columns['backups']);
}
?>

<h1><?php echo __('NAS'); ?></h1>

<?php

if ($isnaserrors) {
    echo '<div class="alert alert-danger">';
    echo '<center><b>';
    echo __('Error on configuration of NAS ( ');
    foreach ($listnaserr as $naserr) {
        echo $naserr['Nas']['shortname']." ";
    }
    echo ")<br>";
    echo '</b>';
    echo "Login used for backup is present in radusers and so will not used for backup (loop detected)<br>";
    echo "Please use a specific user for backup, create a snack user if not exists";
    echo '</center></div>';
}

if(AuthComponent::user('role') == 'root'){
    echo $this->Html->link(
        '<i class="glyphicon glyphicon-hdd glyphicon glyphicon-white"></i> ' . __('Add a NAS'),
        array('controller' => 'nas', 'action' => 'add'),
        array('escape' => false, 'class' => 'btn btn-primary')
    );
}

$dropdownCsvButtonItems = array(
    $this->Html->link(
        '<i class="glyphicon glyphicon-download"></i> ' . __('Export'),
        array('action' => 'exporttocsv'),
        array('escape' => false)
    ),
    $this->Html->link(
        '<i class="glyphicon glyphicon-upload"></i> ' . __('Import'),
            '#confirmimport',
            array(
                'escape' => false,
                'data-toggle' => 'modal',
            )
    ),
    $this->Html->link(
        '<i class="fa fa-file-excel-o"></i> ' . __('Download template'),
        array('action' => 'downloadcsvtemplate'),
        array('escape' => false)
    ),
);

if(AuthComponent::user('role') != 'admin' && AuthComponent::user('role') != 'root'){
    unset($dropdownCsvButtonItems[0]);
}

echo $this->element('dropdownButton', array(
    'buttonCount' => 1,
    'class' => 'btn-primary',
    'title' => __('CSV'),
    'icon' => 'glyphicon glyphicon-file',
    'items' => $dropdownCsvButtonItems
));

$dropdownConfigButtonItems = array(
    $this->Html->link(
        '<i class="fa fa-search"></i> ' . __('Search in config'),
        array('action' => 'searchinconfig'),
        array('escape' => false)
    ),
    $this->Html->link(
        __(' Backup config'),
        '#',
        array(
            'class' => 'fa fa-archive',
            'onclick' => 'getbackupall()'
        )
    ),
    $this->Html->link(
        '<i class="glyphicon glyphicon-download"></i> ' . __('Download Config'),
        array('action' => 'downloadconfig'),
        array('escape' => false)
    ),
);

if(AuthComponent::user('role') != 'admin' && AuthComponent::user('role') != 'root'){
    unset($dropdownConfigButtonItems[0]);
}

echo $this->element('dropdownButton', array(
    'buttonCount' => 1,
    'class' => 'btn-primary',
    'title' => __('Config'),
    'icon' => 'glyphicon glyphicon-file',
    'items' => $dropdownConfigButtonItems
));

echo $this->Html->link(
    '<i class="glyphicon glyphicon-retweet glyphicon-white"></i> ' . __('Get Infos'),
    array('controller' => 'nas', 'action' => 'getInfos'),
    array('escape' => false, 'class' => 'btn btn-primary')
);
echo " ";
echo $this->Html->link(
    '<i class="fa fa-sitemap"></i> ' . __('Discover'),
    array('controller' => 'nas', 'action' => 'discover'),
    array('escape' => false, 'class' => 'btn btn-primary')
);
echo " ";
echo $this->Html->link(
    '<i class="fa fa-search"></i> ' . __('Find MAC'),
    array('controller' => 'nas', 'action' => 'findmacaddress'),
    array('escape' => false, 'class' => 'btn btn-primary')
);
echo " ";
if(AuthComponent::user('role') == 'root'){
    echo $this->Html->link(
        '<i class="glyphicon glyphicon-remove glyphicon-white"></i> ' . __('Reinitialize conf'),
            '#confirmreinitconf',
            array(
                'escape' => false,
                'data-toggle' => 'modal',
                'class' => 'btn btn-primary btn-danger',
            )
    );
}

echo '<div id="modalimport">';
echo $this->element('modalImport', array(
    'id'   => 'import',
    'url' => array(
        'controller' => 'Nas',
        'action' => 'import',
    ),
    'link' => $this->Html->link(
        '<i class="glyphicon glyphicon-upload glyphicon-white"></i> ' . __('Upload'),
        array(
            'controller' => 'Nas',
            'action' => 'import',
        ),
        array(
            'escape' => false,
            'class'  => 'btn btn-primary'
        )
    )
));
echo '</div>';

echo $this->element('modalReinitgit', array(
        'id'   => 'reinitconf',
        'url' => array(
            'controller' => 'Nas',
            'action' => 'reinitconf',
        ),
        'link' => $this->Html->link(
            '<i class="glyphicon glyphicon-remove glyphicon-white"></i> ' . __('Reinitialize'),
            array(
                'controller' => 'Nas',
                'action' => 'reinitconf',
            ),
            array(
            //'onclick' => "reinitconf();",
            'escape' => false,
            'class' => 'btn btn-primary btn-danger'
            )
        )
    ));

/*
 * Show a filter panel.
 */
echo $this->element('filters_panel', array(
    'controller' => 'nas/index',
    'inputs' => array(
        array(
            'name' => 'text',
            'label' => __('Contains (accept regex)'),
            'autoComplete' => 'true',
        ),
    )
));

echo $this->element(
    'delete_links',
    array('action' => 'form', 'model' => 'Nas')
);

echo $this->element('MultipleAction', array('action' => 'start'));

echo '<strong>';

if(count($unBackupedNas)>0) {
    echo '<i class="glyphicon glyphicon-warning-sign text-danger"></i> ';
    echo __('There is at least one NAS not backuped since 7 days or more.');

} else {
    echo '<i class="glyphicon glyphicon-ok"></i> ';
    echo __('All NAS seem backuped.');
}

echo '</strong>';
?>

<br />
<br />

<table class="table table-hover table-bordered" id="tablenas">
    <thead>
	    <tr>
<?php
foreach ($columns as $field => $info) {
    if (isset($info['fit']) && $info['fit']) {
        echo '<th class="fit">';
    } else {
        echo '<th>';
    }

    switch ($field) {
    case 'checkbox':
        echo $this->element('MultipleAction', array('action' => 'head'));
        break;
    case 'view':
    case 'delete':
    case 'backups':
    case 'edit':
        echo h($info['text']);
        break;
    default:
        $sort = '';

        if (preg_match("#$field$#", $this->Paginator->sortKey())) {
            $sort = '<i class="'
                .  $sortIcons[$this->Paginator->sortDir()]
               . '"></i>';
        }

        echo $this->Paginator->sort(
            $field,
            $info['text'] . ' '. $sort,
            array(
                'escape' => false,
                'url' => array('page' => 1),
            )
        );
        break;
    }

    echo '</th>';
}
?>
	    </tr>
    </thead>

    <tbody>
<?php
if (!empty($nas)) {
    $i=1;
    foreach ($nas as $n) {
        echo '<tr id="nas_'.$n['Nas']['id'].'">';

        foreach ($columns as $field=>$info) {
            if (isset($info['fit']) && $info['fit']) {
                echo '<td class="fit" id="'.$field."_".$n['Nas']['id'].'">';
            } else {
                echo '<td id="'.$field."_".$n['Nas']['id'].'">';
            }

            switch ($field) {
            case 'checkbox':
                echo $this->element(
                    'MultipleAction',
                    array(
                        'action' => 'line',
                        'name' => 'nas',
                        'id' => $n['Nas']['id']
                    )
                );
                break;
            case 'action':
                echo $this->Html->link(
                    '<i class="glyphicon glyphicon-eye-open" data-toggle="tooltip" data-placement="top" title='.__('View').'></i> ',
                    array(
                        'action' => 'view',
                        'controller' => 'nas',
                        $n['Nas']['id'],
                    ),
                    array('escape' => false)
                );
                if(AuthComponent::user('role') == 'root'){
                    echo $this->Html->link(
                        '<i class="glyphicon glyphicon-edit" data-toggle="tooltip" data-placement="top" title='.__('Edit').'></i> ',
                        array('action' => 'edit', $n['Nas']['id']),
                        array('escape' => false)
                    );
                }
                if(AuthComponent::user('role') == 'root'){
                    if ($n['Nas']['nasname'] != "127.0.0.1") {
                        echo $this->element(
                            'delete_links',
                            array(
                                'model' => 'Nas',
                                'action' => 'link',
                                'id' => $n['Nas']['id'],
                            )
                        );
                        echo $this->Html->link(
                            '<i class="glyphicon glyphicon-download-alt" data-toggle="tooltip" data-placement="top" title='.__('Backup').'></i> ',
                            array(  
                            ),
                            array(
                                'onclick' => 'getbackupid('.$n['Nas']['id'].')',
                                'escape' => false)
                        );
                    }
                }
                break;
            case 'backups':
                if(AuthComponent::user('role') == 'root'){
                    if ($n['Nas']['nasname'] != "127.0.0.1") {
                        if (in_array($n['Nas']['nasname'], $unBackupedNas)) {
                            echo '<i class="glyphicon glyphicon-warning-sign glyphicon text-danger" title="' . $lastBackupNas[$n['Nas']['nasname']] . '"> ';
                        } else {
                            if ($n['Nas']['backup']) {
                                echo '<i class="glyphicon glyphicon-ok glyphicon text-success" title="' . $lastBackupNas[$n['Nas']['nasname']] . '"> ';
                            } else {
                                echo '<i class="glyphicon glyphicon-eye-close" title="' . $lastBackupNas[$n['Nas']['nasname']] . '"> ';
                            }
                        }
                        echo $this->Html->link(
                            __('Backups'),
                            array(
                                'action' => 'index',
                                'controller' => 'backups',
                                $n['Nas']['id'],
                            )
                        );
                        echo "</i>";
                    }
                }
                break;
            case 'backuptype':
                if (isset($n['Nas'][$field])) {
                    if ($n['Nas'][$field] == "ssh") {
                        echo '<strong><p class="text-success">'.$n['Nas'][$field].'</p></strong>';
                    }
                    if ($n['Nas'][$field] == "telnet") {
                        echo '<strong><p class="text-warning">'.$n['Nas'][$field].'</p></strong>';
                    }
                }
                break;    
            case 'id':
                echo '<strong>' . h($n['Nas'][$field]) . '</strong>';
                break;
            default:
                echo h($n['Nas'][$field]);
                break;
            }

            echo '</td>';
        }
        echo '</tr>';
        $i++;
    }
} else {
?>
    <tr>
        <td colspan="<?php echo count($columns); ?>" style="text-align: center">
<?php
    echo __('No NAS found.');
?>
        </td>
    </tr>
<?php
}
?>
    </tbody>
</table>
<?php
if(AuthComponent::user('role') == 'root'){
    echo $this->element(
        'MultipleAction',
        array(
            'options' => array('delete'),
            'action' => 'end',
        )
    );
}
echo $this->element('paginator_footer');
unset($n);
?>
