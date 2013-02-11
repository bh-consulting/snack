<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');
Configure::load('parameters');
?>

<h1><?php echo __('Add a user with a certificate'); ?></h1>
<?php
echo $this->Form->create('Raduser');

echo '<fieldset>';
echo '<legend>' . __('Certificate') . '</legend>';

echo $this->Form->input('username');
echo $this->Form->input(
    'country',
    array('default' => Configure::read('Parameters.countryName'))
);
echo $this->Form->input(
    'province',
    array('default' => Configure::read('Parameters.stateOrProvinceName'))
);
echo $this->Form->input(
    'locality',
    array('default' => Configure::read('Parameters.localityName'))
);
echo $this->Form->input(
    'organization',
    array('default' => Configure::read('Parameters.organizationName'))
);

echo '</fieldset>';

echo '<fieldset>';
echo '<legend>' . __('Checks') . '</legend>';

echo $this->Form->input('calling-station-id', array('label' => __('MAC address')));
echo $this->element('check_common_fields');
echo $this->element('doubleListsSelector', array('leftTitle' => __('Groups'), 'rightTitle' => __('Selected groups'), 'contents' => $groups, 'selectedContents' => array()));
echo $this->Form->input('groups', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));

echo '</fieldset>';

echo $this->element('cisco_common_fields', array('type' => 'cert'));

echo '<fieldset>';
echo '<legend>' . __('Replies') . '</legend>';
echo $this->element('reply_common_fields');
echo '</fieldset>';
echo $this->Form->end(__('Create'));
?>

