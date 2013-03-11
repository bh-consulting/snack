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
    'nasname' => array(
        'text' => __('Name'),
    ),
    'shortname' => array(
        'text' => __('Short name'),
    ),
    'description' => array(
        'text' => __('Description'),
    ),
    'type' => array(
        'text' => __('Type'),
        'fit' => true,
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

if(AuthComponent::user('role') != 'superadmin'){
    unset($columns['edit']);
    unset($columns['delete']);
    unset($columns['backups']);
}
?>

<h1><?php echo __('NAS'); ?></h1>

<?php
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
        ))
    )
);

if(AuthComponent::user('role') == 'superadmin'){
    echo $this->Html->link(
        '<i class="icon-hdd icon-white"></i> ' . __('Add a NAS'),
        array('controller' => 'nas', 'action' => 'add'),
        array('escape' => false, 'class' => 'btn btn-primary')
    );
}

echo $this->element(
    'delete_links',
    array('action' => 'form', 'model' => 'Nas')
);

echo $this->element('MultipleAction', array('action' => 'start'));
?>

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
            array('escape' => false)
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
		        echo '<i class="icon-eye-open"></i> ';
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
                if(AuthComponent::user('role') == 'superadmin'){
                    echo '<i class="icon-edit"></i> ';
                    echo $this->Html->link(
                        __('Edit'),
                        array('action' => 'edit', $n['Nas']['id'])
                    );
                }
                break;
            case 'backups':
                if(AuthComponent::user('role') == 'superadmin'){
                    echo '<i class="icon-camera icon-' . (
			    in_array($n['Nas']['id'], $nowriteids) ?
				'red' :
				'green'
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
                if(AuthComponent::user('role') == 'superadmin'){
                    echo '<i class="icon-remove"></i> ';
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
        <td colspan="<?php echo count($columns); ?>">
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
if(AuthComponent::user('role') == 'superadmin'){
    echo $this->element('MultipleAction', array('action' => 'end'));
}
echo $this->element('paginator_footer');
unset($n);
?>
