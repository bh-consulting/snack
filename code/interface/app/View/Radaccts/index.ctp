<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('session_active', 'active');
?>

<h1><?php echo __('Sessions'); ?></h1>
<?php
$columns = array(
    'checkbox' => array('id' => 'radacctid', 'fit' => true),
    'acctuniqueid' => array('id' => 'radacctid', 'text' => __('ID'), 'fit' => true),
    'username' => array('text' => __('Username')),
    'callingstationid' => array('text' => __('User station'), 'fit' => true),
    'acctstarttime' => array('text' => __('Start')),
    'acctstoptime' => array('text' => __('Stop')),
    'nasipaddress' => array('text' => __('NAS'), 'port' => 'nasportid', 'fit' => true),
    'nasporttype' => array('text' => __('Port type'), 'fit' => true),
    'delete' => array('text' => __('Delete'), 'fit' => true),
);

echo $this->Form->create('Session', array('action' => 'delete'));
echo $this->Form->end();

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
        echo $this->Form->select(
            'All',
            array('all' => ''),
            array(
                'class' => 'checkbox rangeAll',
                'multiple' => 'checkbox',
                'hiddenField' => false,
            )
        );
        break;
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

<?php
if (!empty($radaccts)) {
?>
    <tbody>
<?php
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
                echo $this->Form->select(
                    'sessions',
                    array($acct['Radacct'][$info['id']] => ''),
                    array(
                        'class' => 'checkbox range',
                        'multiple' => 'checkbox',
                        'hiddenField' => false,
                    )
                );
                break;
            case 'delete':
                echo '<i class="icon-remove"></i> ';
                echo $this->Html->link(
                    __('Delete'),
                    '#',
                    array(
                        'onClick' => "if (confirm('" . __('Are you sure?') . "')) {"
                        . "$('#SessionDeleteForm').attr('action',"
                        . "$('#SessionDeleteForm').attr('action') + '/"
                        . $acct['Radacct']['radacctid'] . "');"
                        . "$('#SessionDeleteForm').submit(); }"
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
?>
    </tbody>
<?php
} else {
?>
    <tbody>
    <tr>
        <td colspan="<?php echo count($columns); ?>">
<?php
    echo __('No session found.');
?>
        </td>
    </tr>
    </tbody>
<?php
}
?>
</table>

<?php
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
        )
    ));
echo $this->Form->end(array(
    'id' => 'selectionAction',
    'name' => 'action',
    'type' => 'hidden',
    'value' => 'delete'
));
unset($acct);
echo $this->element('paginator_footer');
?>

