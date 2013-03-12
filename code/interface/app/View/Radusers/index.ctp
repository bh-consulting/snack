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

if(AuthComponent::user('role') != 'superadmin'){
    unset($columns['delete']);
}
if(!in_array(AuthComponent::user('role'), array('superadmin', 'admin'))){
    unset($columns['edit']);
}
?>

<h1><? echo __('Users'); ?></h1>

<?php
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

echo $this->element('filters_panel', array(
    'controller' => 'radusers/index',
    'inputs' => array(
        array(
            'name' => 'authtype',
            'label' => __('Authentication type'),
            'multiple' => 'checkbox',
            'type' => 'checkgroup',
        ),
        array(
            'name' => 'rolefilter',
            'label' => __('Role'),
            'multiple' => 'checkbox',
            'type' => 'checkgroup',
        ),
        array(
            'name' => 'text',
            'label' => __('Contains (accept regex)'),
            'autoComplete' => true,
        ))
    )
);
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
                if (AuthComponent::user('role') === 'admin'
                    && $user['Raduser']['type'] === 'snack'
                ) {
                    echo '<span class="unknown" title="'
                        . __('Not allowed!')
                        . '">'
                        . '<i class="icon-edit icon-red"></i> '
                        . __('Edit') . '</span>';
                } else {
                    echo '<i class="icon-edit"></i> ';
                    echo $this->Html->link(
                        __('Edit'),
                        array(
                            'action' => 'edit_' . $user['Raduser']['type'],
                            $user['Raduser'][$info['id']]
                        )
                    );
                }
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
                echo '<strong>' . h($user['Raduser'][$field]) . '</strong>';
                break;
            case (preg_match("#is_(cert|loginpass|mac|cisco)#i", $field)
                ? $field : !$field):
                echo $user['Raduser'][$field] ? '<i class="icon-ok"></i>' : '';
                break;
            case 'role':
                echo '<strong>'
                    . $roles[$user['Raduser'][$field]]
                    . '</span>';
                break;
	    case 'username':
		if($user['Raduser']['expiration'] != -1) {
		    $expiration = new DateTime($user['Raduser']['expiration']);
		    $now = new DateTime();
		    $interval = $now->diff($expiration);

		    if($interval->format('%R') == '-') {
			echo '<i class="icon-warning-sign icon-red" title="';
			echo __('User expired since the %s.', $this->element('formatDates', array('date' => $user['Raduser']['expiration'])));
			echo '"></i> ';
		    }
		}

		echo h($user['Raduser'][$field]);

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
        <td colspan="<?php echo count($columns); ?>" style="text-align: center">
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
unset($radusers);
?>
