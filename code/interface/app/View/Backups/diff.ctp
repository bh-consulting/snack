<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

?>

<h1><?php echo __('Compare'); ?></h1>
<h2><?php echo __('Context'); ?></h2>

<ul>
<li><?php echo $this->Html->link(
    "<i class='icon-hdd'></i> " . $nas['Nas']['shortname'],
    array(
        'controller' => 'nas',
        'action' => 'view',
        $nas['Nas']['id'],
    ),
    array(
        'escape' => false,
    )
) ?> (<?php echo $nas['Nas']['nasname'] ?>)</li>
<li><?php echo __('<strong>%s:</strong> %s',
    __('From'),
    $this->Html->link(
        "<i class='icon-camera'></i> " . $left['Backup']['datetime'],
        array(
            'controller' => 'backups',
            'action' => 'view',
            $left['Backup']['id'],
            $nas['Nas']['id'],
        ),
        array (
            'escape' => false,
        )
    )
) ?></li>
<li><?php echo __('<strong>%s:</strong> %s',
    __('To'),
    $this->Html->link(
        "<i class='icon-camera'></i> " . $right['Backup']['datetime'],
        array(
            'controller' => 'backups',
            'action' => 'view',
            $right['Backup']['id'],
            $nas['Nas']['id'],
        ),
        array (
            'escape' => false,
        )
    )
) ?></li>
</ul>

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
            <th><?php echo $left['Backup']['datetime']; ?></th>
            <th><?php echo $right['Backup']['datetime']; ?></th>
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
echo $this->Html->link(
    '<i class="icon-arrow-left icon-white"></i> '
    . '<i class="icon-camera icon-white"></i> '
    . __('Go back to backups'),
        array(
            'controller' => 'backups',
            'action' => 'index',
            $nas['Nas']['id'],
        ),
        array(
            'escape' => false,
            'class' => 'btn btn-primary',
        )
    );
?>
