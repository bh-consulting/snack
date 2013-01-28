<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('param_active', 'active');

echo '<h2>' . __('Edit server parameters') . '</h2>';

echo $this->Form->create('Parameter', array('action' => 'edit'));

echo $this->Form->input(
    'contactEmail',
    array(
        'label' => __('Contact email'),
        'class' => 'email',
    )
);
echo $this->Form->input(
    'scriptsPath',
    array(
        'label' => __('Scripts path'),
        'class' => 'path',
    )
);
echo $this->Form->input(
    'certsPath',
    array(
        'label' => __('Certificates path'),
        'class' => 'path',
    )
);
echo $this->Form->input('countryName', array('label' => __('Country')));
echo $this->Form->input(
    'stateOrProvinceName',
    array(
        'label' => __('State or province'),
    )
);
echo $this->Form->input('localityName', array('label' =>  __('Locality')));
echo $this->Form->input(
    'organizationName',
    array('label' => __('Organization'))
);

echo $this->Form->end(__('Update'));
?>
