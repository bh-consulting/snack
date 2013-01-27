<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('param_active', 'active');

echo '<h2>' . __('Edit server parameters') . '</h2>';

echo $this->Form->create('Parameter', array('action' => 'edit'));

echo $this->Form->input(
    'contactEmail',
    array(
	'label' => __('Contact email'),
	'value' => $contactEmail,
	'class' => 'email',
    )
);
echo $this->Form->input(
    'scriptsPath',
    array(
	'label' => __('Scripts path'),
	'value' => $scriptsPath,
    )
);
echo $this->Form->input(
    'certsPath',
    array(
	'label' => __('Certificates path'),
	'value' => $certsPath,
    )
);
echo $this->Form->input(
    'countryName',
    array(
	'label' => __('Country'),
	'value' => $countryName,
    )
);
echo $this->Form->input(
    'stateOrProvinceName',
    array(
	'label' => __('State or province'),
	'value' => $stateOrProvinceName,
    )
);
echo $this->Form->input(
    'localityName',
    array(
	'label' =>  __('Locality'),
	'value' => $localityName,
    )
);
echo $this->Form->input(
    'organizationName',
    array(
	'label' => __('Organization'),
	'value' => $organizationName,
    )
);

echo $this->Form->end(__('Update'));
?>
