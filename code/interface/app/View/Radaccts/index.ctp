<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('session_active', 'active');

$columns = array(
    'checkbox' => array(
        'id' => 'radacctid',
        'fit' => true,
    ),
    'acctstarttime' => array(
        'text' => __('Start'),
    ),
    'acctstoptime' => array(
        'text' => __('Stop'),
    ),
    'duration' => array(
        'text' => __('Durée'),
    ),
    'username' => array(
        'text' => __('Username'),
    ),
    'callingstationid' => array(
        'text' => __('User station'),
        'fit' => true,
    ),
    'nasipaddress' => array(
        'text' => __('NAS'),
        'port' => 'nasportid',
    ),
    'nasporttype' => array(
        'text' => __('Port type'),
        'fit' => true,
    ),
    'view' => array(
        'text' => __('View'),
        'fit' => true,
    ),
    'delete' => array(
        'text' => __('Delete'),
        'fit' => true,
    ),
);

if(AuthComponent::user('role') != 'superadmin'){
    unset($columns['delete']);
}
?>

<h1><?php echo __('Sessions'); ?></h1>

<?php
echo $this->element('filters_panel', array(
    'controller' => 'radaccts/index',
    'inputs' => array(
        array(
            'name' => 'datefrom',
            'label' => __('From'),
            'type' => 'datetimepicker',
        ),
        array(
            'name' => 'dateto',
            'label' => __('To'),
            'type' => 'datetimepicker',
        ),
        array(
            'name' => 'porttype',
            'label' => __('Port type'),
        ),
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
        array('action' => 'form', 'model' => 'Radacct')
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
if (!empty($radaccts)) {
    foreach ($radaccts as $acct) {
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
                        'name' => 'sessions',
                        'id' => $acct['Radacct'][$info['id']]
                    )
                );
                break;
            case 'view':
                echo '<i class="icon-eye-open"></i> ';
                echo $this->Html->link(
                    __('View'),
                    array(
                        'action' => 'view',
                        'controller' => 'radaccts',
                        $acct['Radacct']['radacctid'],
                    )
                );
                break;
            case 'delete':
                echo '<i class="icon-remove"></i> ';
                echo $this->element(
                    'delete_links',
                    array(
                        'model' => 'Radacct',
                        'action' => 'link',
                        'id' => $acct['Radacct']['radacctid'],
                    )
                );
                break;
            case 'nasipaddress':
                echo $this->element('formatNasLink', array(
                    'nas' => $devices[$acct['Radacct']['radacctid']]
                ));

                if (!empty($acct['Radacct'][$info['port']])) {
                    echo ':' . (strpos($acct['Radacct'][$info['port']], '500') !== false ?
                        $acct['Radacct'][$info['port']] - 50000 :
                        h($acct['Radacct'][$info['port']]));
                }
                break;
            case 'username':
                echo $this->element('formatUsersList', array(
                    'users' => $users[$acct['Radacct']['radacctid']]
                ));

                break;
            case 'callingstationid':
                echo str_replace('-', ':', h($acct['Radacct'][$field]));
                break;
            case 'nasporttype':
                $value = $acct['Radacct'][$field];
                echo isset($types[$value]) ? $types[$value] : $value;
                break;
            case 'acctstarttime':
            case 'duration':
                echo $acct['Radacct'][$field];
                break;
            case 'acctstoptime':
                if (empty($acct['Radacct'][$field])) {
                    echo '<em>'.__('still connected').'</em>';
                } else {
                    echo $acct['Radacct'][$field];
                }
                break;
            default:
                echo h($acct['Radacct'][$field]);
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
    echo __('No session found.');
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
            'action' => 'end',
            'options' => array('delete'),
        )
    );
}
echo $this->element('paginator_footer');
unset($acct);
?>

