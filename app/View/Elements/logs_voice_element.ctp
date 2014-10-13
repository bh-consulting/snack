<?php

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
                if (preg_match('/UTC (.*), PeerAddress/', $logline2['Logline']['msg'], $matches)) {
                    $datetime2 = new DateTime($matches[1]);
                    echo $datetime2->format('Y-m-d');
                    echo " ";
                    echo $datetime1->format('H:i:s');
                } elseif (preg_match('/CET (.*), PeerAddress/', $logline2['Logline']['msg'], $matches)) {
                    $datetime2 = new DateTime($matches[1]);
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
?>