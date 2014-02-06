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
    'view' => array(
        'text' => __('View'),
        'fit' => true,
    ),
    'edit' => array(
        'text' => __('Edit'),
        'fit' => true,
    ),
    'delete' => array(
        'text' => __('Delete'),
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
            case 'view':
		        echo '<i class="glyphicon glyphicon-eye-open"></i> ';
                echo $this->Html->link(
                    __('View'),
                    array(
                        'action' => 'view',
                        'controller' => 'nas',
                        $n['Nas']['id'],
                    )
                );
                break;
            case 'edit':
                if(AuthComponent::user('role') == 'root'){
                    echo '<i class="glyphicon glyphicon-edit"></i> ';
                    echo $this->Html->link(
                        __('Edit'),
                        array('action' => 'edit', $n['Nas']['id'])
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
            case 'delete':
                if(AuthComponent::user('role') == 'root'){
                    echo '<i class="glyphicon glyphicon-remove"></i> ';
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
