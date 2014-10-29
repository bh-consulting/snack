<?php
$this->extend('/Common/logs_tabs');
$this->assign('voicelogs_active', 'active');

if (Configure::read('debug') == 2) {
    $columns = array(
        /*'id' => array(
            'text' => __('Line'),
            'fit' => true,
        ),*/
        'datetime' => array(
            'text' => __('Date'),
            'fit' => true,
        ),
        'calling' => array(
            'text' => __('Calling'),
            'fit' => true,
        ),
        'called' => array(
            'text' => __('Called'),
            'fit' => true,
        ),
        'duration' => array(
            'text' => __('Duration'),
            'fit' => true,
        ),
        'status' => array(
            'text' => __('Status'),
            'fit' => true,
        ),
        'msg' => array(
            'text' => __('Message'),
            'fit' => true,
        ),
    );
}
else {
    $columns = array(
        'datetime' => array(
            'text' => __('Date'),
            'fit' => true,
        ),
        'calling' => array(
            'text' => __('Calling'),
            'fit' => true,
        ),
        'called' => array(
            'text' => __('Called'),
            'fit' => true,
        ),
        'duration' => array(
            'text' => __('Duration'),
            'fit' => true,
        ),
        'status' => array(
            'text' => __('Status'),
            'fit' => true,
        ),
    );
}
?>

<?php
echo $this->element('filters_panel', array(
    'controller' => 'loglines/voice_logs',
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
            'name' => 'host',
            'label' => __('Host'),
            'options' => array('host' => 'host'),
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
<div id="voicelivelogs">
<?php
echo "Results found : ".$nbResults;
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
                echo $info['text'];
                /*if (preg_match("#$field$#", $this->Paginator->sortKey())) {
                    $sort = '<i class="'
                            . $sortIcons[$this->Paginator->sortDir()]
                            . '"></i>';
                }

                echo $this->Paginator->sort(
                        $field, $info['text'] . ' ' . $sort, array(
                    'escape' => false,
                    'url' => array('page' => 1),
                        )
                );*/
            }
            ?>
        </tr>
    </thead>

    <tbody>
<?php
if (!empty($loglines)) {
    $listfcid = array();
    $end = count($loglines)-1;
    for ($i = 0; $i < $end; $i = $i + 2) {
        $logline = $loglines[$i];
        if (!(preg_match('/%VOIPAAA-5-VOIP_FEAT_HISTORY:/', $logline['Logline']['msg'], $matches))) {
            $i++;
            $logline = $loglines[$i];
            $end--;
        }
        $logline2 = $loglines[$i + 1];
        //debug($logline);
        //debug($logline2['Logline']['msg']);
        if (preg_match('/%VOIPAAA-5-VOIP_CALL_HISTORY: CallLegType 1/', $logline2['Logline']['msg'], $matches) && !preg_match('/ConnectionId 0000/', $logline2['Logline']['msg'], $matches)) {
            //debug("test");
            //debug($logline2['Logline']['msg']);
            if (preg_match('/fcid:(.*),legID:/', $logline['Logline']['msg'], $matches)) {                
                if (!in_array($matches[1], $listfcid)) {
                    $listfcid[] = $matches[1];
                    echo "<tr class='loglevel{$logline['Logline']['level']}'>";
                    foreach ($columns as $field => $info) {
                        if (isset($info['fit']) && $info['fit']) {
                            echo "<td class='fit logcell$field'>";
                        } else {
                            echo "<td class='logcell$field'>";
                        }

                        switch ($field) {
                            case 'calling':
                                if (preg_match('/cgn:([0-9]+)/', $logline['Logline']['msg'], $matches)) {
                                    echo $matches[1];
                                }
                                break;
                            case 'called':
                                if (preg_match('/cdn:([0-9]+)/', $logline['Logline']['msg'], $matches)) {
                                    echo $matches[1];
                                }
                                break;
                            case 'datetime':
                                if (preg_match('/ConnectTime \**([0-9]{2}:[0-9]{2}:[0-9]{2})/', $logline2['Logline']['msg'], $matches)) {
                                    $datetime1 = new DateTime($matches[1]);
                                    if (preg_match('/(CET|UTP|cet|utp|est|cest)\+*\d*\s+(.*), PeerAddress/', $logline2['Logline']['msg'], $matches)) {
                                        $datetime2 = new DateTime($matches[2]);
                                        echo $datetime2->format('Y-m-d');
                                        echo " ";
                                        echo $datetime1->format('H:i:s');
                                    }
                                }
                                break;
                            case 'duration':
                                if (preg_match('/ConnectTime \**([0-9]{2}:[0-9]{2}:[0-9]{2})/', $logline2['Logline']['msg'], $matches)) {
                                    $datetime1 = new DateTime($matches[1]);
                                    if (preg_match('/DisconnectTime \**([0-9]{2}:[0-9]{2}:[0-9]{2})/', $logline2['Logline']['msg'], $matches)) {
                                        $datetime2 = new DateTime($matches[1]);
                                        $interval = $datetime2->diff($datetime1);
                                        echo $interval->format('%H:%I:%S');
                                    }
                                }
                                break;
                            case 'status':
                                if (preg_match('/DisconnectText (.*) \(([0-9]+)\)/', $logline2['Logline']['msg'], $matches)) {
                                    if ($matches[2] == "16") {
                                        echo '<i class="glyphicon glyphicon-ok glyphicon-white"></i> ';
                                    } else {
                                        echo '<i class="glyphicon glyphicon-remove glyphicon-white"></i> ' . $matches[1];
                                    }
                                }
                                break;
                            case 'msg':
                                echo $logline['Logline'][$field];
                                echo $logline2['Logline'][$field];
                                break;
                            default:
                                //echo $logline2['Logline'][$field];
                                break;
                        }

                        echo '</td>';
                    } // foreach
                    echo '</tr>';
                }
            }
        }
        else if (preg_match('/%VOIPAAA-5-VOIP_CALL_HISTORY: CallLegType 2/', $logline2['Logline']['msg'], $matches) && !preg_match('/ConnectionId 0000/', $logline2['Logline']['msg'], $matches)) {
            //debug("test");
            //debug($logline2['Logline']['msg']);
            if (preg_match('/fcid:(.*),legID:/', $logline['Logline']['msg'], $matches)) {                
                if (!in_array($matches[1], $listfcid)) {
                    $listfcid[] = $matches[1];
                    echo "<tr class='loglevel{$logline['Logline']['level']}'>";
                    foreach ($columns as $field => $info) {
                        if (isset($info['fit']) && $info['fit']) {
                            echo "<td class='fit logcell$field'>";
                        } else {
                            echo "<td class='logcell$field'>";
                        }

                        switch ($field) {
                            case 'calling':
                                if (preg_match('/cgn:([0-9]+)/', $logline['Logline']['msg'], $matches)) {
                                    echo $matches[1];
                                }
                                break;
                            case 'called':
                                if (preg_match('/cdn:([0-9]+)/', $logline['Logline']['msg'], $matches)) {
                                    echo $matches[1];
                                }
                                break;
                            case 'datetime':
                                if (preg_match('/ConnectTime \**([0-9]{2}:[0-9]{2}:[0-9]{2})/', $logline2['Logline']['msg'], $matches)) {
                                    $datetime1 = new DateTime($matches[1]);
                                    if (preg_match('/(CET|UTP|cet|utp|est|cest)\+*\d*\s+(.*), PeerAddress/', $logline2['Logline']['msg'], $matches)) {
                                        $datetime2 = new DateTime($matches[2]);
                                        echo $datetime2->format('Y-m-d');
                                        echo " ";
                                        echo $datetime1->format('H:i:s');
                                    }
                                }
                                break;
                            case 'duration':
                                if (preg_match('/ConnectTime ([0-9]{2}:[0-9]{2}:[0-9]{2})/', $logline2['Logline']['msg'], $matches)) {
                                    $datetime1 = new DateTime($matches[1]);
                                    if (preg_match('/DisconnectTime ([0-9]{2}:[0-9]{2}:[0-9]{2})/', $logline2['Logline']['msg'], $matches)) {
                                        $datetime2 = new DateTime($matches[1]);
                                        $interval = $datetime2->diff($datetime1);
                                        echo $interval->format('%H:%I:%S');
                                    }
                                }
                                break;
                            case 'status':
                                if (preg_match('/DisconnectText (.*) \(([0-9]+)\)/', $logline2['Logline']['msg'], $matches)) {
                                    if ($matches[2] == "16") {
                                        echo '<i class="glyphicon glyphicon-ok glyphicon-white"></i> ';
                                    } else {
                                        echo '<i class="glyphicon glyphicon-remove glyphicon-white"></i> ' . $matches[1];
                                    }
                                }
                                break;
                            case 'msg':
                                echo $logline['Logline'][$field];
                                echo $logline2['Logline'][$field];
                                break;
                            default:
                                //echo $logline2['Logline'][$field];
                                break;
                        }

                        echo '</td>';
                    } // foreach
                    echo '</tr>';
                }
            }
        }
    }
} else {
    ?>
            <tr>
                <td colspan="<?php echo count($columns); ?>" style="text-align: center">
    <?php
    echo __('No logs found') . ' (';
    if (count($this->params['url']) > 3)
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
</div>
<?php
echo $this->element('paginator_logs');

$this->start('script');
echo $this->Html->script('loglines');
$this->end();
?>
