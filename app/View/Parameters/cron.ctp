<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_cron_active', 'active');
?>

<h4><?php echo __('Cron configuration:'); ?></h4>
<table>
    
<?php
echo $this->Html->tableHeaders(
    array('Script', 'Frequence', 'Actions'),
    array('class' => 'table table-striped table-condensed'),
    array('class' => '')
);
$infos = explode("\n", $listcron);
//debug($infos);
foreach ($infos as $ligne) {
    if (preg_match("/^(#*)(\*\/\d+|\d+|\*)\s+(\*\/\d+|\d+|\*)\s+(\*\/\d+|\d+|\*)\s+(\*\/\d+|\d+|\*)\s+(\*\/\d+|\d+|\*)\s+(www-data|root)\s+(.*)$/", $ligne, $matches)) {
        $i=1;
        $disabled = $matches[$i];
        $i++;
        $min = $matches[$i];
        $i++;
        $hour = $matches[$i];
        $i++;
        $day = $matches[$i];
        $i++;
        $month = $matches[$i];
        $i++;
        $dayofmonth = $matches[$i];
        $i++;
        $user = $matches[$i];
        $i++;
        $script = $matches[$i];
        $freq = "";
        if (preg_match("/^\*\/(\d+)$/", $min, $matches)) {
            $freq = "Every ".$matches[1]." minutes";
        }
        if (preg_match("/^\*\/(\d+)$/", $hour, $matches)) {
            $freq = "Every ".$matches[1]." hours";
        }
        if (preg_match("/^(\d+)$/", $hour, $matches)) {
            if (preg_match("/^(\d+)$/", $min, $matches2)) {
                $freq = "At $matches[1]:$matches2[1]";
                
            }
        }
        if (preg_match("/^#+$/", $disabled, $matches)) {
            $freq = "Disabled";
        }
        if ($script == "/home/snack/interface/app/Console/cake snack_check_updates") {
            $name = "Updates";
            $scriptname = "snack_check_updates";
        }
        if ($script == "/home/snack/interface/app/Console/cake SnackCheckErrors") {
            $name = "CheckErrors";
            $scriptname = "SnackCheckErrors";
        }
        if ($script == "/home/snack/interface/app/Console/cake SnackCheckErrors2") {
            $name = "CheckErrors";
            $scriptname = "SnackCheckErrors2";
        }
        if ($script == "/home/snack/interface/tools/scriptCluster.sh") {
            $name = "Cluster Replication";
            $scriptname = "scriptCluster";
        }
        if ($script == "/home/snack/interface/tools/scriptWatchdog.sh") {
            $name = "Watchdog";
            $scriptname = "scriptWatchdog";
        }
        if ($script == "/home/snack/interface/tools/scriptLogsRotate.sh") {
            $name = "Log Rotate";
            $scriptname = "scriptLogsRotate";
        }
        if ($script == "/home/snack/interface/app/Console/cake SnackSendReports") {
            $name = "Send Reports";
            $scriptname = "SnackSendReports";
        }
        if (AuthComponent::user('role') === 'admin' && $user['Raduser']['type'] === 'snack'
        ) {
            $actions .= '<span class="unknown" title="'
            . __('Not allowed!')
            . '">'
            . '<i class="glyphicon glyphicon-edit glyphicon-red"></i> '
            . __('Edit') . '</span>';
            
        } else {
            //echo '<i class="glyphicon glyphicon-edit"></i> ';
            $actions = $this->Html->link(
                    '<i class="glyphicon glyphicon-edit" data-toggle="tooltip" data-placement="top" title=' . __('Edit') . '></i> ', array(
                'action' => 'edit_cron',
                $scriptname,
                    ), array('escape' => false)
            );
            if (preg_match("/^#+$/", $disabled, $matches)) {
               $actions .= $this->Html->link(
                        '<i class="glyphicon glyphicon-ok-circle" data-toggle="tooltip" data-placement="top" title=' . __('Disable') . '></i> ', array(
                    'action' => 'enable_cron',
                    $scriptname,
                        ), array('escape' => false)
                ); 
            } else {
                $actions .= $this->Html->link(
                        '<i class="glyphicon glyphicon-ban-circle" data-toggle="tooltip" data-placement="top" title=' . __('Disable') . '></i> ', array(
                    'action' => 'disable_cron',
                    $scriptname,
                        ), array('escape' => false)
                );
            }
        }
        echo $this->Html->tableCells(array(
            array($name, $freq, array($actions, array('class' => 'fit'))))
        );
        //echo "<td>".$min."</td><td>".$hour."</td><td>".$day."</td><td>".$month."</td><td>".$dayofmonth."</td><td>".$user."</td><td>".$name."</td>";
    }
}
?>
</table>