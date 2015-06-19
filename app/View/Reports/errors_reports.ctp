<?php
$this->extend('/Common/reports_tabs');
$this->assign('errorsreports_active', 'active');
?>

<br>
<h2><? echo __('Errors from NAS'); ?></h2>
<?php
    echo "<table class='table table-striped table-condensed'>";
    echo "<th></th>";
    echo "<th>".__('Host')."</th>";
    echo "<th>".__('Type')."</th>";
    echo "<th>".__('Msg')."</th>";
    echo "<th>".__('Nb')."</th>";
    echo "<th>".__('Last')."</th>";
    $id=0;
    foreach ($err as $host => $value) {
        foreach ($value as $errtype => $value2) {
            echo "<tr><td onclick='javascript:reportsexpanderror(\"err\", $id);'><span class='glyphicon glyphicon-plus-sign' aria-hidden='true'></span></td><td>".$host."</td><td>".$errtype."</td><td></td><td></td></tr>";
            echo "<tbody class='reports-msg reports-err-msg-".$id."'>";
            foreach ($value2 as $msg => $nb) {
                echo "<tr><td></td><td></td><td></td><td>".$msg."</td><td>".$nb."</td><td>".$lasts[$host][$errtype][$msg]."</td></tr>";
            }
            echo "</tbody>";
            $id++;
        }
        $id++;
    }
    echo "</table>";
?>

<h2><? echo __('Warnings from NAS'); ?></h2>
<?php
    echo "<table class='table table-striped table-condensed'>";
    echo "<th></th>";
    echo "<th>".__('Host')."</th>";
    echo "<th>".__('Type')."</th>";
    echo "<th>".__('Msg')."</th>";
    echo "<th>".__('Nb')."</th>";
    echo "<th>".__('Last')."</th>";
    $id=0;
    foreach ($warn as $host => $value) {
        foreach ($value as $errtype => $value2) {
            echo "<tr><td onclick='javascript:reportsexpanderror(\"warn\", $id);'><span class='glyphicon glyphicon-plus-sign' aria-hidden='true'></span></td><td>".$host."</td><td>".$errtype."</td><td></td><td></td></tr>";
            echo "<tbody class='reports-msg reports-warn-msg-".$id."'>";
            foreach ($value2 as $msg => $nb) {
                echo "<tr><td></td><td></td><td></td><td>".$msg."</td><td>".$nb."</td><td>".$warnlasts[$host][$errtype][$msg]."</td></tr>";
            }
            echo "</tbody>";
            $id++;
        }
        $id++;
    }
    echo "</table>";
?>

<h2><? echo __('Users connected on SNACK'); ?></h2>
<?php
   /* foreach ($snack_users as $key => $value) {
        echo $key." : ".$value."<br>";
    }*/
?>

<h2><? echo __('Failure connections'); ?></h2>

<?php
    $nb = count($failures);
    echo "<h4>$nb failures of connections order by users<br></h4>";
    echo "<table class='table table-striped table-condensed'>";
    echo "<th>".__('User')."</th>";
    echo "<th>".__('Nb')."</th>";
    echo "<th>".__('Last')."</th>";
    echo "<th>".__('Vendor')."</th>";
    echo "<th>".__('NAS')."</th>";
    echo "<th>".__('Port')."</th>";
    echo "<th>".__('Why ?')."</th>";
    //debug($users);
    $infos = explode(",", $this->element('formatUsersList', array(
            'users' => $usernames
        )));
    $i=0;
    foreach ($failures as $key => $value) {
        echo "<tr>";
        echo "<td>".$infos[$i]."</td>";
        echo "<td>".$value."</td>";
        echo "<td>".$users[$logins[$i]]['last']."</td>";
        echo "<td>".$users[$logins[$i]]['vendor']."</td>";
        echo "<td>".$users[$logins[$i]]['nas']."</td>";
        echo "<td>".$users[$logins[$i]]['port']."</td>";
        echo "<td>".$users[$logins[$i]]['info']."</td>";
        //echo " : ".$value." tentatives";
        $i++;
        echo "</tr>";
    }
    echo "</table>";
    
    
    $nb = count($nasfailures);
    echo "<h4>$nb failures of connections order by nas</h4>";
    echo "<table class='table table-striped table-condensed'>";
    echo "<th>".__('NAS')."</th>";
    echo "<th>".__('Nb')."</th>";
    echo "<th>".__('Last')."</th>";
    $i=0;
    foreach ($nasfailures as $key => $value) {
        echo "<tr>";
        echo "<td>".$key."</td>";
        echo "<td>".$value."</td>";
        echo "<td>".$naslasts[$key]."</td>";
        //echo " : ".$value." tentatives";
        $i++;
        echo "</tr>";
    }
    echo "</table>";
?>
    
<h2><? echo __('Users connected on NAS'); ?></h2>
<?php
    $nb = count($sessions);
    echo "$nb connected users on $str_date<br>";
    foreach($sessions as $session) {
        echo $session['radacct']['acctstarttime']." : ".$session['radacct']['username']." - ".$session['user']['comment']."<br>";
        
    }
?>