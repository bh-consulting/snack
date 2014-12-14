<h1>Voice Reports</h1>
<br>
<h2><? echo __('Total Number calls');  
    if (isset($directorynumber)) {
        echo " for ".$directorynumber;
    }
    ?>
</h2>

<div class="container">
<img src="img/graph.svg" />
</div>
<!-- Top called/callings -->
<h1><?php echo __('Top called and calling'); ?></h1>

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

