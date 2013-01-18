<?php 

$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');

echo '<h1>' . __('Add a group') . '</h1>';

echo $this->Form->create('Radgroup');
echo $this->Form->input('groupname', array('label' => __('Name')));
echo $this->element('doubleListsSelector', array('leftTitle' => __('Users'), 'rightTitle' => __('Selected users'), 'contents' => $users, 'selectedContents' => array()));
echo $this->Form->input('users', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));

echo '<fieldset>';
echo '<legend>' . __('Checks') . '</legend>';
echo $this->element('check_common_fields');
echo '</fieldset>';

echo '<fieldset>';
echo '<legend>' . __('Replies') . '</legend>';
echo $this->element('reply_common_fields');
echo '</fieldset>';

echo $this->Form->end(__('Create'));
?>
