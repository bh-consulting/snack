<?php
$this->extend('/Common/logs_tabs');
$this->assign('naslogs_active', 'active');

echo $this->element('logs_element', array(
    'controller' => 'nas_logs',
    'program' => 'snack',
));

?>
