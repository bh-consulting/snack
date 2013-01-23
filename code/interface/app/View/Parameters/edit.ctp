<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('param_active', 'active');

echo '<h2>' . __('Edit server parameters') . '</h2>';

echo $this->Form->create(false, array('action' => 'edit'));

echo $this->Form->input(
    $contactEmail['id'],
    array(
	'label' => $contactEmail['label'],
	'value' => $contactEmail['value'],
	'class' => 'email',
    )
);
echo $this->Form->input(
    $countryName['id'],
    array(
	'label' => $countryName['label'],
	'value' => $countryName['value'],
    )
);
echo $this->Form->input(
    $stateOrProvinceName['id'],
    array(
	'label' => $stateOrProvinceName['label'],
	'value' => $stateOrProvinceName['value'],
    )
);
echo $this->Form->input(
    $localityName['id'],
    array(
	'label' => $localityName['label'],
	'value' => $localityName['value'],
    )
);
echo $this->Form->input(
    $organizationName['id'],
    array(
	'label' => $organizationName['label'],
	'value' => $organizationName['value'],
    )
);

echo $this->Form->end(__('Update'));
?>
