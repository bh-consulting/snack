<?php
$this->extend('/Common/reports_tabs');
$this->assign('errorsfromnasreports_active', 'active');
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


<h2><?php echo __('Errors from NAS'); ?></h2>
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

<h2><?php echo __('Warnings from NAS'); ?></h2>
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