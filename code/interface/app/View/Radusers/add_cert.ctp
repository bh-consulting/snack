<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>

<h1><? echo __('Add a user with a certificate'); ?></h1>
<?php
echo $this->Form->create('Raduser');
echo $this->Form->input('username');
echo $this->element('doubleListsSelector', array('leftTitle' => __('Groups'), 'rightTitle' => __('Selected groups'), 'contents' => $groups, 'selectedContents' => array()));
echo $this->Form->input('groups', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));

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

