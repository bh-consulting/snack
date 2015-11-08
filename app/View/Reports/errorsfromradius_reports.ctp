<?php
$this->extend('/Common/reports_tabs');
$this->assign('errorsfromradiusreports_active', 'active');
echo "<br>";
echo $this->Form->create('Reports', array(
    //'action' => $action,
    'novalidate' => true, 
    'autocomplete' => 'off',
    'class' => 'form-inline col-sm-offset-4',
    'inputDefaults' => array(
        'div' => 'form-group',
        'class' => 'form-control'
    ),
));

$mainLabelOptions = array('class' => 'label-inline control-label');
$myLabelOptions = array('text' => __('Date'));
echo  $this->Form->input('choosedate', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),//__('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NAS &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'),
    'options' => $list,
    'selected' => $id,
    'empty' => false,
));
$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => 'btn btn-primary',
    'before' => '<div class="col-sm-offset-1 col-sm-2">',
    'after' => '</div>'
);
echo $this->Form->end($options);

?>

<h2><? echo __('Users connected on SNACK'); ?></h2>
<?php
    $nb = count($snack_users);
    echo "<h4>$nb connections<br></h4>";
    if ($nb > 0)  {
        echo "<table class='table table-striped table-condensed'>";
        echo "<th>".__('User')."</th>";
        echo "<th>".__('Date')."</th>";
        foreach ($snack_users as $key => $value) {
            
            if (preg_match('/(.*): logged in/', $value, $matches)) {
                echo "<tr>";
                echo "<td>".$matches[1]."</td>";
                echo "<td>".$key."</td>";
                echo "</tr>";
            }
            //echo $key." : ".$value."<br>";
        }    
        echo "</table>";
    }
?>

<h2><? echo __('Failure connections'); ?></h2>
<?php
    $nb = count($failures);
    echo "<h4>$nb failures of connections order by users<br></h4>";
    if ($nb > 0)  {
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
    }
    
    $nb = count($nasfailures);
    if ($nb > 0)  {
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
    }
?>
