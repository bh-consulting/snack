<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('param_active', 'active');

echo '<h1>' . __('Edit server parameters') . '</h1>';

echo $this->Form->create('Parameter', array('action' => 'edit'));

echo $this->Form->input(
    'configurationEmail',
    array(
        'label' => __('Configuration email'),
        'class' => 'email',
    )
);
echo $this->Form->input(
    'errorEmail',
    array(
        'label' => __('Error email'),
        'class' => 'email',
    )
);
echo $this->Form->input(
    'ipAddress',
    array(
        'label' => __('Server IP'),
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
echo $this->Form->input('paginationCount', array('label' => __('Pagination count')));

echo $this->Form->end(__('Update'));
?>
