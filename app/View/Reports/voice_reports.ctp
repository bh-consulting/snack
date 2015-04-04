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
<h1><?php echo __('Top called and calling'); ?></h1>
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
$dir = new Folder('/home/snack/logs');
$files = $dir->find('snacklog.*');
sort($files);
//echo $this->Form->create('Reports', array('action' => 'voice_reports'));
$myLabelOptions = array('text' => __('Log file'));
echo  $this->Form->input('logfile', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'options' => $files,
    'selected' => array_search($file, $files),
    'empty' => false,
));

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





<h3><?php echo __('Top called'); ?></h3>
<?php
//debug($resultsCalled);
    echo "<table class='table table-striped table-condensed'>";
    echo "<th>".__('Times')."</th>";
    echo "<th>".__('Called')."</th>";
    foreach($resultsCalled as $key=>$res) {
        echo "<tr>";
        echo "<td>".$res."</td>";
        echo "<td>".$key."</td>";
        echo "</tr>";
    }
    echo "</table>";
?>

<h3><? echo __('Top calling'); ?></h3>

<?php
//echo $G->draw($resultsOutgoingCalling);
//debug($results);
    echo "<table class='table table-striped table-condensed'>";
    echo "<th>".__('Times')."</th>";
    echo "<th>".__('Calling')."</th>";
    foreach($resultsOutgoingCalling as $key=>$res) {
        echo "<tr>";
        echo "<td>".$res."</td>";
        echo "<td>".$key."</td>";
        echo "</tr>";
    }
    echo "</table>";
//App::uses('phpGraph', 'Lib');


?>

