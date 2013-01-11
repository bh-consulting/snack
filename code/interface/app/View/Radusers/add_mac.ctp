<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
<h1> <? echo __('Add a user with MAC address'); ?></h1>
<?php
echo $this->Form->create('Raduser');
echo $this->Form->input('mac', array('label' => __('MAC address')));
echo $this->element('check_common_fields');
echo $this->element('doubleListsSelector', array('leftTitle' => __('Groups'), 'rightTitle' => __('Selected groups'), 'contents' => $groups, 'selectedContents' => array()));
echo $this->Form->input('groups', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));
echo $this->Form->end(__('Create'));
?>
