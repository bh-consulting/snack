<?php
$this->extend('/Common/logs_tabs');
$this->assign('snacklogs_active', 'active');

echo $this->element('logs_element', array(
    'controller' => 'snack_logs',
    'program' => 'snack',
));

?>