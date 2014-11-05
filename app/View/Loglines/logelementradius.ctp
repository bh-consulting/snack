<?php
//echo $this->element('paginator_logs');

echo "Results found : ".$nbResults;

$columns = array(
    'datetime' => array(
        'text' => __('Date'),
        'fit' => true,
    ),
    'level' => array(
        'text' => __('Severity'),
        'fit' => true,
    ),
    'host' => array(
        'text' => __('Host'),
        'fit' => true,
    ),
    'msg' => array(
        'text' => __('Message'),
    ),
);
?>
<table class="table loglinks table-hover table-bordered">
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
    echo $info['text'];
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
                $date = new DateTime($logline['Logline'][$field]);
                echo $this->Html->link(
                    $date->format('Y-m-d H:i:s')    
                    ,
                    '#',
                    array(
                        'onclick' => 'logsSearchFromDate($(this))',
                        'title' => __('Search from this date'),
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
    if(count($this->params['url']) > 3)
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
<?php
echo 'Page generated in '.$total_time.' seconds.<br><br>';
?>