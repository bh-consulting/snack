<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');
?>

<h1><? echo __('Users'); ?></h1>
<?php
$dropdownUsersButtonItems = array(
    _('Active') => array(
        $this->Html->link(
            '<i class="icon-plus-sign"></i> ' . __('Certificate'), 
            array('action' => 'add_cert'),
            array('escape' => false, 'class' => 'secure_auth')
        ),
        $this->Html->link(
            '<i class="icon-plus-sign"></i> ' . __('Login / Password'), 
            array('action' => 'add_loginpass'),
            array('escape' => false, 'class' => 'warning_auth')
        ),
    ),
    _('Passive') => array(
        $this->Html->link(
            '<i class="icon-plus-sign"></i> ' . __('MAC address'), 
            array('action' => 'add_mac'),
            array('escape' => false)
        )
    ),
    _('Snack') => array(
        $this->Html->link(
            '<i class="icon-plus-sign"></i> ' . __('Admin'), 
            array('action' => 'add_admin'),
            array('escape' => false)
        )
    ),
);
if(AuthComponent::user('role') != 'superadmin'){
    unset($dropdownUsersButtonItems[__('Snack')]);
}

if(AuthComponent::user('role') != 'tech'){
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

if(AuthComponent::user('role') === 'tech'){
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
$columns = array(
    'id' => array('text' => __('ID'), 'fit' => true),
    'username' => array('text' => __('Username')),
    'comment' => array('text' => __('Comment')),
    'is_cert' => array('text' => __('Certificate'), 'fit' => true),
    'is_loginpass' => array('text' => __('Login / Password'), 'fit' => true),
    'is_mac' => array('text' => __('MAC'), 'fit' => true),
    'is_cisco' => array('text' => __('Cisco'), 'fit' => true),
    'role' => array('text' => __('Admin'), 'fit' => true),
);

if(AuthComponent::user('role') == 'superadmin'){
    echo $this->Form->create('Radusers', array('action' => 'delete'));
    echo $this->Form->end();
} else {
    // hum hum (ala blank.gif)
    echo '<div></div><br />';
}

echo $this->Form->create('MultiSelection', array('class' => 'form-inline'));
?>

<table class="table">
    <thead>
    <tr>
        <th class="fit">
<?php
echo $this->Form->select(
    'All',
    array('all' => ''),
    array(
        'class' => 'checkbox rangeAll',
        'multiple' => 'checkbox',
        'hiddenField' => false
    )
);
?>
        </th>
<?php
foreach ($columns as $field => $info) {
    $sort = '';

    if (preg_match("#$field$#", $this->Paginator->sortKey())) {
        $sort = '<i class="'
            .  $sortIcons[$this->Paginator->sortDir()]
            . '"></i>';
    }

    if (isset($info['fit']) && $info['fit']) {
        echo '<th class="fit">';
    } else {
        echo '<th>';
    }

    echo $this->Paginator->sort(
        $field,
        $info['text'] . ' '. $sort,
        array('escape' => false)
    )
    . '</th>';
}
    if(in_array(AuthComponent::user('role'), array('superadmin', 'admin'))){
        echo '<th class="fit">' . __('Edit') . '</th>';
        if(AuthComponent::user('role') == 'superadmin'){
            echo '<th class="fit">' . __('Delete') . '</th>';
        }
    }
    ?>
    </tr>
    </thead>

    <tbody>
<?php
if (!empty($radusers)) {
    foreach ($radusers as $rad) {
?>
    <tr>
        <td class="fit">
<?php
        echo $this->Form->select(
            'users',
            array($rad['Raduser']['id'] => ''),
            array(
                'class' => 'checkbox range',
                'multiple' => 'checkbox',
                'hiddenField' => false,
            )
        );
?>
        </td>
        <td class="fit">
<?php
        echo $this->Html->link(
            $rad['Raduser']['id'],
            array(
                'controller' => 'Radusers',
                'action' => 'view_' . $rad['Raduser']['type'],
                $rad['Raduser']['id']
            )
        );
?>
        </td>
        <td>
<?php
        echo $rad['Raduser']['username'];
?>
        </td>
        <td>
<?php
        echo $rad['Raduser']['comment'];
?>
        </td>
        <td class="fit" style="text-align:center;">
<?php
        echo $rad['Raduser']['is_cert'] ? '<i class="icon-ok"></i>' : '';
?>
        </td>
        <td class="fit" style="text-align:center;">
<?php
        echo $rad['Raduser']['is_loginpass'] ? '<i class="icon-ok"></i>' : '';
?>
        </td>
        <td class="fit" style="text-align:center;">
<?php
        echo $rad['Raduser']['is_mac'] ? '<i class="icon-ok"></i>' : '';
?>
        </td>
        <td class="fit" style="text-align:center;">
<?php
        echo $rad['Raduser']['is_cisco'] ? '<i class="icon-ok"></i>' : '';
?>
        </td>
        <td class="fit" style="text-align:center;">
<?php
        echo __($rad['Raduser']['role']);
?>
        </td>
    <?php if(in_array(AuthComponent::user('role'), array('superadmin', 'admin'))){ ?>
        <td class="fit">
            <i class="icon-edit"></i>
<?php
        echo $this->Html->link(
            __('Edit'),
            array(
                'action' => 'edit_' . $rad['Raduser']['type'],
                $rad['Raduser']['id']
            )
        );
?>
        </td>
        <?php } ?>
    <?php if(AuthComponent::user('role') =='superadmin'){ ?>
        <td class="fit">
            <i class="icon-remove"></i>
<?php
        echo $this->Html->link(
            __('Delete'),
            '#',
            array(
                'onClick' => "if (confirm('" . __('Are you sure?') . "')) {"
                . "$('#RadusersDeleteForm').attr('action',"
                . "$('#RadusersDeleteForm').attr('action') + '/"
                . $rad['Raduser']['id'] . "');"
                . "$('#RadusersDeleteForm').submit(); }"
            )
        );
?>
        </td>
        <?php } ?>
    </tr>
<?php
    }
} else {
?>
    <tr>
        <td colspan="<?php echo count($columns) + 3; ?>">
<?php
    echo __('No users yet.');
?>
        </td>
    </tr>
<?
}
?>
    </tbody>
</table>
<?php
if(AuthComponent::user('role') == 'superadmin'){
    echo $this->element('dropdownButton', array(
        'buttonCount' => 1,
        'title' => 'Action',
        'icon' => '',
        'items' => array(
            $this->Html->link(
                '<i class="icon-remove"></i> ' . __('Delete selected'),
                '#',
                array(
                    'onClick' =>	"$('#selectionAction').attr('value', 'delete');"
                    . "if (confirm('" . __('Are you sure?') . "')) {"
                    . "$('#MultiSelectionIndexForm').submit();}",
                        'escape' => false,
                )
            ),
            $this->Html->link(
                '<i class="icon-download"></i> ' . __('Export selected'),
                '#',
                array(
                    'onClick' => "$('#selectionAction').attr('value', 'export');"
                    . "$('#MultiSelectionIndexForm').submit();",
                        'escape' => false,
                )
            ),
        )
    ));

    echo $this->Form->end(array(
        'id' => 'selectionAction',
        'name' => 'action',
        'type' => 'hidden'
    ));
}

echo $this->element('paginator_footer');
unset($rad);
?>
