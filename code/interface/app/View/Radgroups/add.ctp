<? 
$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');
?>
<h1><? echo __('Add a group'); ?></h1>
<?php
echo $this->Form->create('Radgroup');
echo $this->Form->input('groupname', array('label' => 'Name'));
echo $this->element('check_common_fields');
echo $this->element('doubleListsSelector', array('leftTitle' => __('Users'), 'rightTitle' => __('Selected users'), 'contents' => $users, 'selectedContents' => array()));
echo $this->Form->input('users', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));
echo $this->Form->end(__('Create'));
?>
