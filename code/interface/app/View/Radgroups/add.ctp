<? 
$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');
?>
<h1>Add a group</h1>
<?php
echo $this->Form->create('Radgroup');
echo $this->Form->input('groupname');
echo $this->Form->input('comment');
echo $this->Form->end('Create');
?>


