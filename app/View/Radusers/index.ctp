<?php 
/* TODO: change role to a nice output
remove link from id, set blank
shorter name for role in fr
*/
$this->extend('/Common/radusers_tabs');
$this->assign('radusers_users_active', 'active');


$columns = array(
    'checkbox' => array(
        'id' => 'id',
        'fit' => true,
    ),
    'id' => array(
        'id' => 'id',
        'text' => __('ID'),
        'fit' => true,
        'bold' => true,
    ),
    'username' => array(
        'text' => __('Username'),
    ),
    'comment' => array(
        'text' => __('Comment'),
    ),
    'vlan' => array(
        'text' => __('VLAN'),
    ),
    'type' => array(
        'text' => __('Type'),
        'fit' => true,
    ),
   'action' => array(
        'id' => 'id',
        'text' => __('Action'),
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
<br/>
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
	__('Cisco Phones'),
	array('action' => 'add_phone'),
	array('escape' => false)
    ),
    $this->Html->link(
        __('Passive (MAC)'), 
        array('action' => 'add_mac'),
        array('escape' => false)
    ),
);

if(AuthComponent::user('role') != 'root'){
    unset($dropdownUsersButtonItems[__('SNACK')]);
}

if(AuthComponent::user('role') == 'admin' || AuthComponent::user('role') == 'root'){
    echo $this->element('dropdownButton', array(
        'buttonCount' => 1,
        'class' => 'btn-primary',
        'title' => __('Add user'),
        'icon' => 'glyphicon glyphicon-user',
        'items' => $dropdownUsersButtonItems,
    ));
}

$dropdownCsvButtonItems = array(
    $this->Html->link(
        '<i class="glyphicon glyphicon-download"></i> ' . __('Export'),
        array('action' => 'exportAll'),
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

if(AuthComponent::user('role') != 'tech'){
    echo $this->element('dropdownButton', array(
        'buttonCount' => 1,
        'class' => 'btn-primary',
        'title' => __('CSV'),
        'icon' => 'glyphicon glyphicon-file',
        'items' => $dropdownCsvButtonItems
    ));
}



echo '<div id="modalimport">';
echo $this->element('modalImport', array(
    'id'   => 'import',
    'url' => array(
        'controller' => 'Radusers',
        'action' => 'import',
    ),
    'link' => $this->Html->link(
        '<i class="glyphicon glyphicon-upload glyphicon-white"></i> ' . __('Upload'),
        array(
            'controller' => 'Radusers',
            'action' => 'import',
        ),
        array(
            'escape' => false,
            'class'  => 'btn btn-primary'
        )
    )
));
echo '</div>';

echo '<div id="modalimportSimple">';
echo $this->element('modalImport', array(
    'id'   => 'importSimple',
    'url' => array(
        'controller' => 'Radusers',
        'action' => 'importSimple',
    ),
    'link' => $this->Html->link(
        '<i class="glyphicon glyphicon-upload glyphicon-white"></i> ' . __('Upload'),
        array(
            'controller' => 'Radusers',
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
    'controller' => 'radusers/index',
    'inputs' => array(
        array(
            'name' => 'authtype',
            'label' => __('Authentication type'),
            'multiple' => 'checkbox',
            'type' => 'checkgroup',
        ),
        array(
            'name' => 'datefrom',
            'label' => __('Not connected since'),
            'type' => 'datetimepicker',
        ),
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
?>

<?php
if(AuthComponent::user('role') == 'root'){
    echo $this->element(
        'delete_links',
        array('action' => 'form', 'model' => 'Raduser')
    );
}

echo $this->element('MultipleAction', array('action' => 'start'));
?>

<table class="table table-hover table-bordered">
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
if (!empty($radusers)) {
    foreach ($radusers as $user) {
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
                        'name' => 'users',
                        'id' => $user['Raduser'][$info['id']]
                    )
                );
                break;
            case 'action':
                echo $this->Html->link(
                    '<i class="glyphicon glyphicon-eye-open" data-toggle="tooltip" data-placement="top" title='.__('View').'></i> ',
                    array(
                        'action' => 'view_' . $user['Raduser']['type'],
                        'controller' => 'radusers',
                        $user['Raduser'][$info['id']],
                    ),
                    array('escape' => false)
                );
                if (AuthComponent::user('role') == 'tech') {
                    echo '<span class="unknown" title="'
                        . __('Not allowed!')
                        . '">'
                        . '<i class="glyphicon glyphicon-edit glyphicon-red" data-toggle="tooltip" data-placement="top" title='.__('Edit').'></i> </span>';
                } else {
                    //echo '<i class="glyphicon glyphicon-edit"></i> ';
                    echo $this->Html->link(
                        '<i class="glyphicon glyphicon-edit" data-toggle="tooltip" data-placement="top" title='.__('Edit').'></i> ',
                        array(
                            'action' => 'edit_' . $user['Raduser']['type'],
                            $user['Raduser'][$info['id']]
                        ),
                        array('escape' => false)
                    );
                }
                if ($user['Raduser']['type'] != "windowsad") {
                    echo $this->element(
                        'delete_links',
                        array(
                            'model' => 'Raduser',
                            'action' => 'link',
                            'id' => $user['Raduser'][$info['id']],
                        )
                    );
                }
                break;
            /*case (preg_match("#is_(cert|loginpass|windowsad|phone|mac|cisco)#i", $field)
                ? $field : !$field):
                echo $user['Raduser'][$field] ? '<i class="glyphicon glyphicon-ok"></i>' : '';
                break;*/
            case 'type':
                if ($user['Raduser']['is_windowsad']) {
                    echo $this->Html->image('windows.png', array('alt' => __('Login/Pwd by ActiveDirectory'), 'title' => __('Login/Pwd by ActiveDirectory')));
                }
                if ($user['Raduser']['is_phone']) {
                    echo $this->Html->image('phone.png', array('alt' => __('Phone'), 'title' => __('Phone')));
                }
                if ($user['Raduser']['is_loginpass']) {
                    echo $this->Html->image('user_password.png', array('alt' => __('Login/Pwd'), 'title' => __('Login/Pwd')));
                }
                if ($user['Raduser']['is_cert']) {
                    echo $this->Html->image('certificate.png', array('alt' => __('Certificate'), 'title' => __('Certificate')));
                }
                if ($user['Raduser']['is_mac']) {
                    echo $this->Html->image('mac.png', array('alt' => __('MAC'), 'title' => __('MAC')));
                }
                if ($user['Raduser']['is_cisco']) {
                    echo $this->Html->image('cisco.png', array('alt' => __('Cisco'), 'title' => __('Cisco')));
                }
                break;
            case 'username':
		        if($user['Raduser']['expiration'] != -1) {
                    echo '<span title="'
                        . __(
                            'User expired since the %s.',
                            $user['Raduser']['expiration']
                        )
                        . '"><i class="glyphicon glyphicon-warning-sign glyphicon-red"></i> '
                        . h($user['Raduser'][$field])
                        . '</span>';
                } else {
                    echo h($user['Raduser'][$field]);
                }
                break;
            case 'vlan':
                if (h($user['Raduser']['group']) != '') {
                    echo '<b><center><a data-toggle="tooltip" data-placement="top" title="'.h($user['Raduser']['group']).'">'.h($user['Raduser'][$field]).'</a></center></b>';
                }
                else {
                    echo '<center>'.h($user['Raduser'][$field]).'</center>';
                }
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
unset($radusers);
?>
