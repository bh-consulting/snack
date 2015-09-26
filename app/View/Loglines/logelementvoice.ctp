<?php

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

echo "Results found : ".$nbResults;
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
    //debug($loglines);
    $listcalls = array();
    $end = count($loglines)-1;
    $datetime1 = new DateTime();
    $datetime2 = new DateTime();
    for ($i = 0; $i < count($loglines); $i++) {
        //debug($listcalls);
        $logline = $loglines[$i];
    //foreach($loglines as $logline) {
        //debug($logline);
        if (preg_match('/%VOIPAAA-5-VOIP_CALL_HISTORY: CallLegType \d+, ConnectionId ([^,]+),/', $logline['Logline']['msg'], $matches) && !preg_match('/ConnectionId 0000/', $logline['Logline']['msg'], $matches2)) {
            $fcid = $matches[1];
            if (preg_match('/PeerAddress (\d+).*CallOrigin 2/', $logline['Logline']['msg'], $matches5)) {
                $listcalls[$fcid]['cgn'] = $matches5[1];
            }
            if (preg_match('/PeerAddress (\d+).*CallOrigin 1/', $logline['Logline']['msg'], $matches5)) {
                $listcalls[$fcid]['cdn'] = $matches5[1];
            }
            if (preg_match('/ConnectTime \**([0-9]{2}:[0-9]{2}:[0-9]{2})/', $logline['Logline']['msg'], $matches3)) {
                $datetime1 = DateTime::createFromFormat("H:i:s", $matches3[1]);
                if (preg_match('/(CET|UTP|UTC|cet|utp|est|cest)\+*\d*\s+(.*), PeerAddress/', $logline['Logline']['msg'], $matches4)) {
                    $datetime2 = new DateTime($matches4[2]);
                    $listcalls[$fcid]['date'] = $datetime2->format('Y-m-d');
                    $listcalls[$fcid]['time'] = $datetime1->format('H:i:s');
                }
                if (preg_match('/DisconnectTime \**([0-9]{2}:[0-9]{2}:[0-9]{2})/', $logline['Logline']['msg'], $matches4)) {
                    $datetime2 = DateTime::createFromFormat("H:i:s", $matches4[1]);
                    //debug($datetime2);
                    $interval = $datetime2->diff($datetime1);
                    $listcalls[$fcid]['duration'] =  $interval->format('%H:%I:%S');
                }
                //DisconnectText normal call clearing (16)
            }
            if (preg_match('/DisconnectText ([^\(]*) \((\d+)\)/', $logline['Logline']['msg'], $matches3)) {
                $listcalls[$fcid]['statusmsg'] = $matches3[1];
                $listcalls[$fcid]['statusnum'] = $matches3[2];
            }
            $listcalls[$fcid]['text'] = $logline['Logline']['msg'];
        }
    }
    unset($datetime1);
    unset($datetime2);
    //debug($listcalls);
    foreach ($listcalls as $key=>$call) {
        //debug($key);
        if (isset($call['date'])) {
            //if (isset($call['cdn'])) {
                echo "<tr class='loglevel{$logline['Logline']['level']}'>";
                echo "<td>".$call['date']." ".$call['time']."</td>";
                if (isset($call['cgn'])) {
                    echo "<td>".$call['cgn']."</td>";
                } else {
                    echo "<td></td>";
                }
                if (isset($call['cdn'])) {
                    echo "<td>".$call['cdn']."</td>";
                } else {
                    echo "<td></td>";
                }
                if (isset($call['duration'])) {
                    echo "<td>".$call['duration']."</td>";
                } else {
                    echo "<td></td>";
                }
                if (isset($call['statusnum'])) {
                    if ($call['statusnum'] == "16") {
                        echo '<td><i class="glyphicon glyphicon-ok glyphicon-white"></i></td>';
                    } else {
                        echo '<td><i class="glyphicon glyphicon-remove glyphicon-white"> '.$call['statusmsg'].'</i></td>';
                    }
                }
                if (Configure::read('debug') == 2) {
                    echo "<td>".$call['text']."</td>";
                }
                //echo "<td>".$key."</td>";
                
                echo "</tr>";
            //}
        }
    }
   //debug($listcalls);
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
<?php
echo 'Page generated in '.$total_time.' seconds.<br><br>';
?>