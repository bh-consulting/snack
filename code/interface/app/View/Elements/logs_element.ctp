<?php

$columns = array(
    'id' => array(
        'text' => __('Line'),
        'fit' => true,
    ),
    'datetime' => array(
        'text' => __('Date'),
        'fit' => true,
    ),
    'level' => array(
        'text' => __('Severity'),
        'fit' => true,
    ),
    'msg' => array(
        'text' => __('Message'),
    ),
);
?>

<?php
echo $this->element('filters_panel', array(
    'controller' => 'loglines/' . $controller,
    'inputs' => array(
        array(
            'name' => 'level',
            'label' => __('Severity from'),
            'type' => 'slidermax',
            'options' => array('id' => 'severity'),
        ),
        array(
            'name' => 'datefrom',
            'label' => __('From'),
            'type' => 'datetimepicker',
            'options' => array('id' => 'datefrom'),
        ),
        array(
            'name' => 'dateto',
            'label' => __('To'),
            'type' => 'datetimepicker',
            'options' => array('id' => 'dateto'),
        ),
        array(
            'name' => 'text',
            'label' => __('Message contains (accept regex)'),
            'options' => array('id' => 'logmessage'),
            'autoComplete' => true,
        ))
    )
);
?>

<table class="table loglinks">
    <thead>
        <tr>
<?php
foreach ($columns as $field => $info) {
    if (isset($info['fit']) && $info['fit']) {
        echo '<th class="fit">';
    } else {
        echo '<th>';
    }

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
}
?>
        </tr>
    </thead>

    <tbody>
<?php
if (!empty($loglines)) {
    foreach ($loglines as $logline) {
        echo "<tr class='loglevel{$logline['Logline']['level']}'>";

        foreach ($columns as $field=>$info) {
            if (isset($info['fit']) && $info['fit']) {
                echo "<td class='fit logcell$field'>";
            } else {
                echo "<td class='logcell$field'>";
            }

            switch ($field) {
            case 'datetime':
                echo $this->Html->link(
                    $logline['Logline'][$field],
                    '#',
                    array(
                        'onclick' => 'logsSearchFromDate($(this))',
                        'title' => __('Search from this date'),
            'datetime' => $logline['Logline'][$field],
            'escape' => false
                    )
                );
                break;
            case 'level':
        echo '<strong>';
                echo $this->Html->link(
                    $logline['Logline'][$field],
                    '#',
                    array(
                        'onclick' => 'logsSearchFromSeverity($(this))',
                        'title' => __('Search from this severity')
                    )
                );
        echo '</strong>';
                break;
            default:
                echo $logline['Logline'][$field];
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
    echo __('No logs found').' (';

    if(count($this->params['url']) > 0)
        echo $this->Html->link(__('retry with no filters'), '.');
    else
        echo __('no filters');

    echo ').';
?>
            </td>
        </tr>
<?php
}
?>
    </tbody>
</table>
<div>
<?php
if(AuthComponent::user('role') == 'superadmin'){
    echo $this->Form->postLink(
        __('Delete all'),
        array('action' => 'deleteAll', $program),
        array('class' => 'btn btn-primary'),
        __('Are you sure?')
    );
}
?>
</div>
<?php
echo $this->element('paginator_footer');
?>
