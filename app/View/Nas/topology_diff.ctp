<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

?>

<h1><?php echo __('Compare'); ?></h1>

<h2><?php echo __('Differences'); ?></h2>

<ul class="nav nav-tabs">
<li class="active"><a href="#tab1" data-toggle="tab">
    <?php echo __('Graphical diff'); ?>
</a></li>
<li><a href="#tab2" data-toggle="tab">
    <?php echo __('Raw diff'); ?>
</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="tab1">
        <table class="table-condensed diff-table">
            <th><?php echo __('Line'); ?></th>
            <th><?php echo $dateleft; ?></th>
            <th><?php echo $dateright; ?></th>
<?php
for ($i=0; $i<count($graphicalDiff['left']); ++$i) {
    echo '<tr class="diff-line">';
    echo '<td class="fit diff-number">' . ($i+1) . '</td>';
    if (is_array($graphicalDiff['left'][$i])) {
        switch (key($graphicalDiff['left'][$i])) {
        case 'ADD':
            echo '<td class="diff-cell blank">&nbsp;';
            break;
        case 'DEL':
            echo '<td class="diff-cell del">&nbsp;';
            break;
        case 'UP':
            echo '<td class="diff-cell up">&nbsp;';
            break;
        }
        echo current($graphicalDiff['left'][$i]) . '</td>';
    } else {
        echo '<td class="diff-cell">&nbsp;' . $graphicalDiff['left'][$i] . '</td>';
    }
    if (is_array($graphicalDiff['right'][$i])) {
        switch (key($graphicalDiff['right'][$i])) {
        case 'ADD':
            echo '<td class="diff-cell add">&nbsp;';
            break;
        case 'DEL':
            echo '<td class="diff-cell blank">&nbsp;';
            break;
        case 'UP':
            echo '<td class="diff-cell up">&nbsp;';
            break;
        }
        echo current($graphicalDiff['right'][$i]) . '</td>';
    } else {
        echo '<td class="diff-cell">&nbsp;' . $graphicalDiff['right'][$i] . '</td>';
    }
    echo "</tr>";
}
?>
        </table>
    </div>
    <div class="tab-pane" id="tab2">
        <pre class="well"><?php echo trim($rawDiff) ?></pre>
    </div>
</div>

<?php
echo "<br>";
echo $this->Html->link(
    '<i class="glyphicon glyphicon-arrow-left glyphicon glyphicon-white"></i> '
    . '<i class="glyphicon glyphicon-camera glyphicon glyphicon-white"></i> '
    . __('Go back to topology'),
        array(
            'controller' => 'nas',
            'action' => 'topology',
        ),
        array(
            'escape' => false,
            'class' => 'btn btn-primary',
        )
    );
?>
