<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');
Configure::load('parameters');

echo '<h1>'
    . __('Edit') . ' '
    . $this->data['Raduser']['username']
    . ' (' . __('certificate user') . ')'
    . '</h1>';

echo $this->Form->create('Raduser', array('action' => 'edit_cert'));

echo '<fieldset>';
echo '<legend>' . __('Certificate') . '</legend>';
echo $this->Form->input(
    'cert_gen',
    array('type' => 'checkbox', 'label' => __('Generate a new certificate'))
);
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
echo $this->element(
    'doubleListsSelector',
    array(
	'leftTitle' => __('Groups'),
	'rightTitle' => __('Selected groups'),
	'contents' => $groups,
	'selectedContents' => $selectedGroups,
    )
);
echo $this->Form->input(
    'groups',
    array(
	'type' => 'select',
	'id' => 'select-right',
	'label' => '',
	'class' => 'hidden',
	'multiple' => 'multiple',
    )
);
echo '</fieldset>';

echo $this->element('cisco_common_fields', array('type' => 'cert'));

echo '<fieldset>';
echo '<legend>' . __('Replies') . '</legend>';
echo $this->element('reply_common_fields');
echo '</fieldset>';

echo $this->element('snack_edit_form');

echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('username', array('type' => 'hidden'));
echo $this->Form->input('was_cisco', array('type' => 'hidden'));

echo $this->Form->end(__('Update'));
?>
