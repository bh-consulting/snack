<?php
$this->extend('/Common/reports_tabs');
$this->assign('sessionsreports_active', 'active');
?>


<h2><? echo __('Sessions Reports'); ?></h2>
<?php
    echo "<br/><table class='table table-striped table-condensed'>";
    echo "<th>".__('Start')."</th>";
    echo "<th>".__('Duration')."</th>";
    echo "<th>".__('Username')."</th>";
    echo "<th>".__('Comment')."</th>";
    echo "<th>".__('Type')."</th>";
    echo "<th>".__('IP')."</th>";
    echo "<th>".__('Station')."</th>";
    echo "<th>".__('NAS')."</th>";
    echo "<th>".__('Port')."</th>";
    echo "<th>".__('Port type ')."</th>";

    //debug($sessions);
    foreach ($sessions as $session) {
        //debug($session);
        echo "<tr>";
        echo "<td>".$session['radacct']['acctstarttime']."</td>";
        if ($session['radacct']['acctstoptime'] == null) {
            echo '<td><span title="' . __('User still connected') . '">';
            echo '<i class="glyphicon glyphicon-time"></i> ';
            echo "<span ";
            echo " data-duration='{$session['radacct']['durationsec']}'";
            echo " data-duration-format='" . __('y,m,d,h,min,s') . "'>";
            echo $session['radacct']['duration'];
            echo '</span></span></td>';
        } else {
            echo '<td><span title="' . __('Session ended the %s.', $session['radacct']['acctstoptime']) . '">';
            echo $session['radacct']['duration'];
            echo '</span></td>';
        }
        //echo "<td>".$session['radacct']['acctsessiontime']."</td>";
        echo "<td>".$session['radacct']['username']."</td>";
        echo "<td>".$users[$session['radacct']['radacctid']][0]['comment']."</td>";
        echo "<td>";
        if ($users[$session['radacct']['radacctid']][0]['is_cisco'] == 1)
            echo $this->Html->image('cisco.png', array('alt' => __('Cisco'), 'title' => __('Cisco')));
        if ($users[$session['radacct']['radacctid']][0]['is_loginpass'] == 1)
            echo $this->Html->image('user_password.png', array('alt' => __('Login/Pwd'), 'title' => __('Login/Pwd')));
        if ($users[$session['radacct']['radacctid']][0]['is_windowsad'] == 1)
            echo $this->Html->image('windows.png', array('alt' => __('Login/Pwd by ActiveDirectory'), 'title' => __('Login/Pwd by ActiveDirectory')));
        if ($users[$session['radacct']['radacctid']][0]['is_phone'] == 1)
            echo $this->Html->image('phone.png', array('alt' => __('Phone'), 'title' => __('Phone')));
        if ($users[$session['radacct']['radacctid']][0]['is_cert'] == 1)
            echo $this->Html->image('certificate.png', array('alt' => __('Certificate'), 'title' => __('Certificate')));
        if ($users[$session['radacct']['radacctid']][0]['is_mac'] == 1)
            echo $this->Html->image('mac.png', array('alt' => __('MAC'), 'title' => __('MAC')));
        echo "</td>";
        echo "<td>".$session['radacct']['framedipaddress']."</td>";
        echo "<td>".$session['radacct']['callingstationid']."</td>";
        echo "<td>".$session['radacct']['nasipaddress']."</td>";
        echo "<td>".$session['radacct']['nasportid']."</td>";
        echo "<td>".$session['radacct']['nasporttype']."</td>";
        //echo "<td>".$session['radacct']['']."<td>";
        //echo "<td>".$session['radacct']['']."<td>";
        //echo "<td>".$session['radacct']['']."<td>";
        echo "</tr>";
    }



    echo "</table>";
?>