<?php
$this->extend('/Common/logs_tabs');
$this->assign('radiuslogs_active', 'active');
?>

<?php
echo $this->element('logs_element', array(
    'controller' => 'index',
    'program' => 'freeradius',
));

?>
</div>
