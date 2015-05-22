<?php
$this->extend('/Common/reports_tabs');
$this->assign('voicereports_active', 'active');
?>
<br>
<?php
if (isset($directorynumber)) {
    echo $this->Html->link(
        '<i class="fa fa-file-pdf-o fa-2x" title="' . __('Export to pdf') . '"></i>',
        array(
            'controller' => 'reports',
            'action' => 'voice_reports_pdf',
            $directorynumber,
        ),
        array('escape' => false)
    );
} else {
    echo $this->Html->link(
        '<i class="fa fa-file-pdf-o fa-2x" title="' . __('Export to pdf') . '"></i>',
        array(
            'controller' => 'reports',
            'action' => 'voice_reports_pdf',
        ),
        array('escape' => false)
    );
}
?>

<br>


<br>
<h2><? echo __('Total Number calls');  
    if (isset($directorynumber)) {
        echo " for ".$directorynumber;
    }
    ?>
</h2>
<?php
$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Reports', array(
    'action' => 'voice_reports',
    'novalidate' => true, 
    'autocomplete' => 'off',
    'class' => 'form-horizontal',
    'inputDefaults' => array(
        'div' => 'form-group',
        'label' => array(
            'class' => $mainLabelOptions
        ),
        'between' => '<div class="col-sm-4">',
        'after'   => '</div>',
        'class' => 'form-control'
    ),
));
$myLabelOptions = array('text' => __('Directory Number'));
echo $this->Form->input('directorynumber', array('directorynumber' => array_merge($mainLabelOptions, $myLabelOptions)));

$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => "btn btn-primary",
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
    );
echo $this->Form->end($options);

?>
<div class="container">
<?php
echo $graph;
?>
    
</div>
<!-- Top called/callings -->
<!--
<h1><?php //echo __('Top called and calling'); ?></h1>

<h3><?php //echo __('Top called'); ?></h3>
<?php
    /*echo "<table class='table table-striped table-condensed'>";
    echo "<th>".__('Times')."</th>";
    echo "<th>".__('Called')."</th>";
    foreach($resultsCalled as $key=>$res) {
        echo "<tr>";
        echo "<td>".$res."</td>";
        echo "<td>".$key."</td>";
        echo "</tr>";
    }
    echo "</table>";*/
?>

<h3><? //echo __('Top calling'); ?></h3>

<?php
   /* echo "<table class='table table-striped table-condensed'>";
    echo "<th>".__('Times')."</th>";
    echo "<th>".__('Calling')."</th>";
    foreach($resultsOutgoingCalling as $key=>$res) {
        echo "<tr>";
        echo "<td>".$res."</td>";
        echo "<td>".$key."</td>";
        echo "</tr>";
    }
    echo "</table>";*/
//App::uses('phpGraph', 'Lib');


?>
-->

