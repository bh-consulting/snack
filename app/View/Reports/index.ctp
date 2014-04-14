-<?php
    $this->extend('/Common/radius_sidebar');
    $this->assign('radius_active', 'active');
    $this->assign('reports_active', 'active');
?>

<h1><? echo __('Reports'); ?></h1>

<br>
<h2><? echo __('Users connected on SNACK'); ?></h2>
<?php
    foreach ($snack_users as $key => $value) {
        echo $key." : ".$value."<br>";
    }
?>

<h2><? echo __('Failure connections'); ?></h2>

<?php
    
    $nb = count($failures);
    echo "$nb tentatives de connexions<br>";
    echo "<table>";
    echo "<th>".__('User')."</th>";
    echo "<th>".__('Nb')."</th>";
    echo "<th>".__('Last')."</th>";
    $infos = explode(",", $this->element('formatUsersList', array(
            'users' => $users
        )));
    $i=0;
    foreach ($failures as $key => $value) {
        echo "<tr>";
        echo "<td>".$infos[$i]."</td>";
        echo "<td>".$value."</td>";
        echo "<td>".$lasts[$key]."</td>";
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



<!--
<h2><? //echo __('Send Report'); ?></h2>
<div class="col-md-6">
<?php
/*
echo $this->Form->create('Reports', array('action' => 'send'));
echo $this->Form->input(
    'dstEmail',
    array(
        'label' => __('Email'),
        'class' => 'email',
    )
);
echo $this->Form->end(__('Send Report'));*/
?>
</div>
-->
