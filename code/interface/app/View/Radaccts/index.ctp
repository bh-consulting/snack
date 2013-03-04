<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('session_active', 'active');

$columns = array(
    'checkbox' => array(
        'id' => 'radacctid',
        'fit' => true,
    ),
    'acctuniqueid' => array(
        'id' => 'radacctid',
        'text' => __('ID'),
        'fit' => true,
    ),
    'username' => array(
        'text' => __('Username'),
    ),
    'callingstationid' => array(
        'text' => __('User station'),
        'fit' => true,
    ),
    'acctstarttime' => array(
        'text' => __('Start'),
    ),
    'acctstoptime' => array(
        'text' => __('Stop'),
    ),
    'nasipaddress' => array(
        'text' => __('NAS'),
        'port' => 'nasportid',
        'fit' => true,
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

echo $this->Form->create('MultiSelection', array('class' => 'form-inline'));
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
            case 'acctuniqueid':
                echo $this->Html->link(
                    h($acct['Radacct'][$field]),
                    array(
                        'controller' => 'Radaccts',
                        'action' => 'view',
                        $acct['Radacct'][$info['id']]
                    )
                );
                break;
            case 'nasipaddress':
                echo h($acct['Radacct'][$field]);

                if (!empty($acct['Radacct'][$info['port']])) {
                    echo ":" . $acct['Radacct'][$info['port']];
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
        <td colspan="<?php echo count($columns); ?>">
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
    echo $this->element('MultipleAction', array('action' => 'end'));
}
echo $this->element('paginator_footer');
unset($acct);
?>

