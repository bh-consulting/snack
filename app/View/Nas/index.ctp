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
);

if(AuthComponent::user('role') != 'root'){
    unset($columns['edit']);
    unset($columns['delete']);
    unset($columns['backups']);
}
?>

<h1><?php echo __('NAS'); ?></h1>

<?php
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


/*
 * Show a filter panel.
 */
echo $this->element('filters_panel', array(
    'controller' => 'nas/index',
    'inputs' => array(
        array(
            'name' => 'writemem',
            'label' => __('Synchronization'),
            'multiple' => 'checkbox',
            'type' => 'checkgroup',
            'escape' => false,
        ),
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

if($nasunwritten) {
    echo '<i class="glyphicon glyphicon-camera glyphicon glyphicon-red"></i> ';
    echo __('There is at least one NAS not synchronized with the starting configuration.');
} else {
    echo '<i class="glyphicon glyphicon-camera glyphicon glyphicon-green"></i> ';
    echo __('All NAS seem synchronized with the starting configuration.');
}

echo '</strong>';
?>

<br />
<br />

<table class="table">
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
    foreach ($nas as $n) {
        echo '<tr>';

        foreach ($columns as $field=>$info) {
            if (isset($info['fit']) && $info['fit']) {
                echo '<td class="fit">';
            } else {
                echo '<td>';
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
                    echo $this->element(
                        'delete_links',
                        array(
                            'model' => 'Nas',
                            'action' => 'link',
                            'id' => $n['Nas']['id'],
                        )
                    );
                }
                break;
            case 'backups':
                if(AuthComponent::user('role') == 'root'){
                    echo '<i class="glyphicon glyphicon-camera glyphicon glyphicon-' . (
			    in_array($n['Nas']['id'], $unwrittenids) ?
				'red" title="' . __('Running configuration NOT synchronized with the starting one.') :
				'green" title="' . __('Running configuration seems synchronized with the starting configuration.')
			) . '"></i> ';
                    echo $this->Html->link(
                        __('Backups'),
                        array(
                            'action' => 'index',
                            'controller' => 'backups',
                            $n['Nas']['id'],
                        )
                    );
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
