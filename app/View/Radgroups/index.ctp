<?php

$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('groups_active', 'active');

$columns = array(
    'checkbox' => array(
        'id' => 'id',
        'fit' => true,
    ),
    'id' => array(
        'text' => __('ID'),
        'fit' => true,
        'bold' => true,
    ),
    'groupname' => array(
        'text' => __('Name'),
    ),
    'membercount' => array(
        'id' => 'id',
        'text' => __('Members'),
        'fit' => true,
        'bold' => true,
    ),
    'comment' => array(
        'text' => __('Comment'),
    ),
   'view' => array(
        'id' => 'id',
        'text' => __('View'),
        'fit' => true,
    ),
    'edit' => array(
        'id' => 'id',
        'text' => __('Edit'),
        'fit' => true,
    ),
    'delete' => array(
        'id' => 'id',
        'text' => __('Delete'),
        'fit' => true,
    ),
);

if(AuthComponent::user('role') != 'root'){
    unset($columns['delete']);
}
if(!in_array(AuthComponent::user('role'), array('root', 'admin'))){
    unset($columns['edit']);
}
?>

<h1><? echo __('Groups'); ?></h1>
<?php
echo $this->Html->link(
    '<i class="glyphicon glyphicon-list glyphicon glyphicon-white"></i> ' . __('Add a group'),
    array('controller' => 'radgroups', 'action' => 'add'),
    array('escape' => false, 'class' => 'btn btn-primary')
);

$dropdownCsvButtonItems = array(
    $this->Html->link(
        '<i class="glyphicon glyphicon-upload"></i> ' . __('Import groups'),
        '#confirmimport',
        array(
            'escape' => false,
            'data-toggle' => 'modal',
        )
    ),
    $this->Html->link(
        '<i class="glyphicon glyphicon-download"></i> ' . __('Export groups'),
        array('action' => 'exportAll'),
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
    'glyphicon glyphicon' => 'glyphicon glyphicon-file',
    'items' => $dropdownCsvButtonItems
));

echo '<div id="modalimport">';
echo $this->element('modalImport', array(
    'id'   => 'import',
    'url' => array(
        'controller' => 'Radgroups',
        'action' => 'import',
    ),
    'link' => $this->Html->link(
        '<i class="glyphicon glyphicon-upload glyphicon glyphicon-white"></i> ' . __('Upload'),
        array(
            'controller' => 'Radgroups',
            'action' => 'import',
        ),
        array(
            'escape' => false,
            'class'  => 'btn btn-primary'
        )
    )
));
echo '</div>';

echo $this->element('filters_panel', array(
    'controller' => 'radgroups/index',
    'inputs' => array(
        array(
            'name' => 'expired',
            'label' => __('Expiration'),
            'multiple' => 'checkbox',
            'type' => 'checkgroup',
            'escape' => false,
        ),
        array(
            'name' => 'text',
            'label' => __('Contains (accept regex)'),
            'autoComplete' => true,
        ))
    )
);

if(AuthComponent::user('role') == 'root'){
    echo $this->element(
        'delete_links',
        array('action' => 'form', 'model' => 'Radgroup')
    );
}

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
    case 'edit':
    case 'delete':
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
if (!empty($radgroups)) {
    foreach ($radgroups as $group) {
        echo '<tr>';

        foreach ($columns as $field=>$info) {
            if (isset($info['fit']) && $info['fit']) {
                echo '<td class="fit"';
            } else {
                echo '<td';
            }
            if (isset($info['bold']) && $info['bold']) {
                echo ' style="font-weight:bold;"';
            }
            echo '>';

            switch ($field) {
            case 'checkbox':
                echo $this->element(
                    'MultipleAction',
                    array(
                        'action' => 'line',
                        'name' => 'groups',
                        'id' => $group['Radgroup'][$info['id']]
                    )
                );
                break;
            case 'view':
		        echo '<i class="glyphicon glyphicon-eye-open"></i> ';
                echo $this->Html->link(
                    __('View'),
                    array(
                        'action' => 'view',
                        'controller' => 'radgroups',
                        $group['Radgroup'][$info['id']],
                    )
                );
                break;
            case 'edit':
                echo '<i class="glyphicon glyphicon-edit"></i> ';
                echo $this->Html->link(
                    __('Edit'),
                    array(
                        'action' => 'edit',
                        $group['Radgroup'][$info['id']]
                    )
                );
                break;
            case 'delete':
                echo '<i class="glyphicon glyphicon-remove"></i> ';
                echo $this->element(
                    'delete_links',
                    array(
                        'model' => 'Radgroup',
                        'action' => 'link',
                        'id' => $group['Radgroup'][$info['id']],
                    )
                );
                break;
            case 'groupname':
                if($group['Radgroup']['expiration'] != -1) {
                    echo '<span title="'
                        . __(
                            'Group expired since the %s.',
                            $group['Radgroup']['expiration']
                        )
                        . '"><i class="glyphicon glyphicon-warning-sign glyphicon glyphicon-red"></i> '
                        . h($group['Radgroup'][$field])
                        . '</span>';
                } else {
                    echo h($group['Radgroup'][$field]);
                }
                break;
            default:
                echo h($group['Radgroup'][$field]);
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
    echo __('No group found.');
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
            'options' => array('delete', 'export'),
            'action' => 'end',
        )
    );
}
echo $this->element('paginator_footer');
unset($radgroups);
?>
