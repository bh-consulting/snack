<? 
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
    ),
    'groupname' => array(
        'text' => __('Name'),
    ),
    'membercount' => array(
        'id' => 'id',
        'text' => __('Members'),
        'fit' => true,
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

if(AuthComponent::user('role') != 'superadmin'){
    unset($columns['delete']);
}
if(!in_array(AuthComponent::user('role'), array('superadmin', 'admin'))){
    unset($columns['edit']);
}
?>

<h1><? echo __('Groups'); ?></h1>
<?php
echo $this->Html->link(
    '<i class="icon-list icon-white"></i> ' . __('Add a group'),
    array('controller' => 'radgroups', 'action' => 'add'),
    array('escape' => false, 'class' => 'btn btn-primary')
);

echo $this->element('filters_panel', array(
    'controller' => 'radgroups/index',
    'inputs' => array(
        array(
            'name' => 'text',
            'label' => __('Contains (accept regex)'),
            'autoComplete' => true,
        ))
    )
);

if(AuthComponent::user('role') == 'superadmin'){
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
if (!empty($radgroups)) {
    foreach ($radgroups as $group) {
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
                        'name' => 'groups',
                        'id' => $group['Radgroup'][$info['id']]
                    )
                );
                break;
            case 'view':
		        echo '<i class="icon-eye-open"></i> ';
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
                echo '<i class="icon-edit"></i> ';
                echo $this->Html->link(
                    __('Edit'),
                    array(
                        'action' => 'edit',
                        $group['Radgroup'][$info['id']]
                    )
                );
                break;
            case 'delete':
                echo '<i class="icon-remove"></i> ';
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
		    $expiration = new DateTime($group['Radgroup']['expiration']);
		    $now = new DateTime();
		    $interval = $now->diff($expiration);

		    if($interval->format('%R') == '-') {
			echo '<i class="icon-warning-sign icon-red" title="';
			echo __('Group expired since the %s.', $this->element('formatDates', array('date' => $group['Radgroup']['expiration'])));
			echo '"></i> ';
		    }
		}

                echo h($group['Radgroup'][$field]);

                break;

            case 'id':
            case 'membercount':
                echo '<strong>' . h($group['Radgroup'][$field]) . '</strong>';
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
if(AuthComponent::user('role') == 'superadmin'){
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
