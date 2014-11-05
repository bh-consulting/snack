<?php
$this->extend('/Common/reports_tabs');
$this->assign('voicereports_active', 'active');
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
echo $this->Form->create('Reports', array('action' => 'voice_reports'));
echo $this->Form->input('directorynumber', array(
    'label' => __('Directory Number'),
    )
);
$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
);
echo $this->Form->end($options);

?>
<div class="container">
<?php
debug($nbappelsstats);
$G = new phpGraph();

echo $G->draw($nbappelsstats,array(
            //'steps' => 50,
            'height'=>300,
            'width'=>700,
            'filled'=>true,
            'tooltips'=>true,
            'diskLegends' => true,
            'diskLegendsType' => 'label',
            'type' => array(
                '0'=>'bar',
                '1'=>'bar',
                '2'=>'bar',
                '4'=>'pie',
            ),
            'stroke' => array(
                '0'=>'red',
                '1'=>'blue',
                '2'=>'green'
            ),
            'legends' => array(
                '0'=>'Total',
                '1'=>'Calling',
                '2'=>'Called',
            ),
            'tooltipLegend' => array(
                'calling'=>'Sample of legend : ',
                'called'=>'Sample of legend : ',
            ),
            'title' => 'Calls',
        )
    );
?>
</div>
<!-- Top called/callings -->
<h1><?php echo __('Top called and calling'); ?></h1>
<?php

$dir = new Folder('/home/snack/logs');
$files = $dir->find('snacklog.*');
sort($files);
echo $this->Form->create('Reports', array('action' => 'voice_reports'));
echo $this->Form->input('logfile', array(
    'options' => $files,
    //'disabled' => true,
    'empty' => false,
    'selected' => array_search($file, $files),
    'label' => __('Log file'),
    ));
$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
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

