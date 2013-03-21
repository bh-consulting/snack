<?php
App::uses('Utils', 'Lib');

//$read = Utils::readFile('/var/www/interface/app/webroot/files/diff');
$start = false;
$actions = array(
    'old' => array(),
    'new' => array(),
    'action' => array()
);
foreach ($read as $line) {
    switch (substr($line, 0, 1)) {
    case '@':
        $start = true;
        break;
    case '-':
        if ($start) {
            array_push($actions['old'], $line);
            array_push($actions['new'], '');
            array_push($actions['action'], 'delete');
        }
        break;
    case '+':
        if ($start) {
            array_push($actions['old'], '');
            array_push($actions['new'], $line);
            array_push($actions['action'], 'add');
        }
        break;
    default:
        if ($start) {
            array_push($actions['old'], $line);
            array_push($actions['new'], $line);
            array_push($actions['action'], 'normal');
        }
        break;
    }
}
echo '<div class="span6">';
echo '<table class="table-condensed">';
for($i=0;$i<count($actions['old']);++$i) {
    switch ($actions['action'][$i]) {
    case 'delete':
        $class = 'error';
        break;
    case 'add':
        $class = 'success';
        break;
    case 'normal':
        $class = '';
        break;
    default:
        $class = 'warning';
        break;
    }

    echo '<tr class="' . $class . '">'
        . '<td>'.htmlentities($actions['old'][$i]).'</td>'
        . '<td>'.htmlentities($actions['new'][$i]).'</td>'
        . '</tr>';
}
echo '</table>';
echo '</div>';
?>
