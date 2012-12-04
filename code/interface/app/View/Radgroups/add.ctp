<? 
$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');
?>
<h1>Add a group</h1>
<?php
echo $this->Form->create('Radgroup');
echo $this->Form->input('groupname', array('label' => 'Name'));
echo $this->element('check_common_fields');
echo $this->Form->end('Create');
?>
