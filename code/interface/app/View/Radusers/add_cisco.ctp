<?php
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
<h1><?php echo __('Add a Cisco user'); ?></h1>
<?php
echo $this->Form->create('Raduser');
echo $this->Form->input('username');
echo $this->Form->input('password', array('label' => __('Password')));
echo $this->Form->input('confirm_password', array('type' => 'password', 'label' => __('Confirm password')));
echo $this->element('doubleListsSelector', array('leftTitle' => __('Groups'), 'rightTitle' => __('Selected groups'), 'contents' => $groups, 'selectedContents' => array()));
echo $this->Form->input('groups', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));

echo '<fieldset>';
echo '<legend>' . __('Checks') . '</legend>';
echo $this->Form->input('nas-port-type', array('options' => array(0 => __('Console'), 5 => __('VTY')), 'empty' => false, 'label' => __('NAS Port Type')));
echo $this->element('check_common_fields');
echo '</fieldset>';

echo '<fieldset>';
echo '<legend>' . __('Replies') . '</legend>';
echo $this->element('reply_common_fields');
echo '</fieldset>';

echo $this->Form->end(__('Create'));
?>
