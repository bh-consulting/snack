<?php 
/* TODO: change role to a nice output
remove link from id, set blank
shorter name for role in fr
*/

$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

$columns = array(
    'checkbox' => array(
        'id' => 'id',
        'fit' => true,
    ),
    'id' => array(
        'id' => 'id',
        'text' => __('ID'),
        'fit' => true,
    ),
    'username' => array(
        'text' => __('Username'),
    ),
    'role' => array(
        'text' => __('Role'),
        'fit' => true,
    ),
    'comment' => array(
        'text' => __('Comment'),
    ),
    'is_cert' => array(
        'text' => __('Certificate'),
        'fit' => true,
    ),
    'is_loginpass' => array(
        'text' => __('Login / Password'),
        'fit' => true,
    ),
    'is_mac' => array(
        'text' => __('MAC'),
        'fit' => true,
    ),
    'is_cisco' => array(
        'text' => __('Cisco'),
        'fit' => true,
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
?>

<h1><? echo __('Users'); ?></h1>

<?php
echo $this->element('filters_panel', array(
    'controller' => 'radusers/index',
    'inputs' => array(
	array(
	    'name' => 'authtype',
	    'label' => __('Authentication type'),
	),
	array(
	    'name' => 'role',
	    'label' => __('Role'),
	),
	array(
	    'name' => 'text',
	    'label' => __('Contains (accept regex)'),
        'autoComplete' => true,
	))
    )
);

$dropdownUsersButtonItems = array(
    __('Active') => array(
        $this->Html->link(
            __('Certificate'), 
            array('action' => 'add_cert'),
            array('escape' => false, 'class' => 'secure_auth')
        ),
        $this->Html->link(
            __('Login / Password'), 
            array('action' => 'add_loginpass'),
            array('escape' => false, 'class' => 'warning_auth')
        ),
    ),
    $this->Html->link(
        __('Passive (MAC)'), 
        array('action' => 'add_mac'),
        array('escape' => false)
    ),
    $this->Html->link(
        __('Snack'), 
        array('action' => 'add_snack'),
        array('escape' => false)
    ),
);

if(AuthComponent::user('role') != 'superadmin'){
    unset($dropdownUsersButtonItems[__('Snack')]);
}

if(AuthComponent::user('role') == 'admin' || AuthComponent::user('role') == 'superadmin'){
    echo $this->element('dropdownButton', array(
        'buttonCount' => 1,
        'class' => 'btn-primary',
        'title' => __('Add user'),
        'icon' => 'icon-user',
        'items' => $dropdownUsersButtonItems,
    ));
}

$dropdownCsvButtonItems = array(
    $this->Html->link(
        '<i class="icon-upload"></i> ' . __('Import users'),
        array('action' => 'import_csv'),
        array('escape' => false)
    ),
    $this->Html->link(
        '<i class="icon-download"></i> ' . __('Export users'),
        array('action' => 'export_csv'),
        array('escape' => false)
    ),
);

if(AuthComponent::user('role') != 'admin' && AuthComponent::user('role') != 'superadmin'){
    unset($dropdownCsvButtonItems[0]);
}

echo $this->element('dropdownButton', array(
    'buttonCount' => 1,
    'class' => 'btn-primary',
    'title' => __('CSV'),
    'icon' => 'icon-file',
    'items' => $dropdownCsvButtonItems
));

?>

<?php
if(AuthComponent::user('role') == 'superadmin'){
    echo $this->element(
        'delete_links',
        array('action' => 'form', 'model' => 'Raduser')
    );
}

echo $this->element('MultipleAction', array('action' => 'start'));
?>

<table class="table">
    <thead>
    <tr>
<?php
foreach ($columns as $field => $info) {
    if (($field == 'edit'
        && !in_array(AuthComponent::user('role'), array('superadmin', 'admin')))
        ||
        ($field == 'delete'
        && AuthComponent::user('role') != 'superadmin')
    ) {
        break;
    }

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
if (!empty($radusers)) {
    foreach ($radusers as $user) {
        echo '<tr>';

        foreach ($columns as $field=>$info) {
            if (($field == 'edit'
                && (!in_array(AuthComponent::user('role'), array('superadmin', 'admin'))
                    || (AuthComponent::user('role') === 'admin'
                    && $rad['Raduser']['type'] === 'snack')))
                ||
                ($field == 'delete'
                && AuthComponent::user('role') != 'superadmin')
            ) {
                break;
            }

            if (isset($info['fit']) && $info['fit']) {
		echo '<td class="fit"'.($field == 'role' ? ' style="font-weight: bold"' : '').'>';
            } else {
                echo '<td>';
            }

            switch ($field) {
            case 'checkbox':
                echo $this->element(
                    'MultipleAction',
                    array(
                        'action' => 'line',
                        'name' => 'users',
                        'id' => $user['Raduser'][$info['id']]
                    )
                );
                break;
            case 'view':
		        echo '<i class="icon-eye-open"></i> ';
                echo $this->Html->link(
                    __('View'),
                    array(
                        'action' => 'view_' . $user['Raduser']['type'],
                        'controller' => 'radusers',
                        $user['Raduser'][$info['id']],
                    )
                );
                break;
            case 'edit':
                echo '<i class="icon-edit"></i> ';
                echo $this->Html->link(
                    __('Edit'),
                    array(
                        'action' => 'edit_' . $user['Raduser']['type'],
                        $user['Raduser'][$info['id']]
                    )
                );
                break;
            case 'delete':
                echo '<i class="icon-remove"></i> ';
                echo $this->element(
                    'delete_links',
                    array(
                        'model' => 'Raduser',
                        'action' => 'link',
                        'id' => $user['Raduser'][$info['id']],
                    )
                );
                break;
            case 'id':
                echo $this->Html->link(
                    h($user['Raduser'][$field]),
                    array(
                        'controller' => 'Radusers',
                        'action' => 'view_' . $user['Raduser']['type'],
                        $user['Raduser'][$info['id']]
                    )
                );
                break;
            case (preg_match("#is_(cert|loginpass|mac|cisco)#i", $field)
                ? $field : !$field):
                echo $user['Raduser'][$field] ? '<i class="icon-ok"></i>' : '';
                break;
            case 'role':
                echo __($roles[$user['Raduser'][$field]]);
                break;
            default:
                echo h($user['Raduser'][$field]);
                break;
            }

            echo '</td>';
        }
        echo '</tr>';
    }
} else {
?>
    <tr>
        <td colspan="<?php echo count($columns) + 3; ?>" style="text-align: center">
<?php
    echo __('No user found.');
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
unset($rad);
?>
