<?php 
$this->extend('/Common/parameters_tabs');
$this->assign('param_active', 'active');

echo $this->Form->create('Parameter', array('action' => 'edit', 'autocomplete' => 'off'));
?>

<h4><?php echo __('General information:'); ?></h4>
<dl class="well dl-horizontal">
<?php
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
?>
</dl>

<h4><?php echo __('Certificates configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php

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
?>
</dl>

<h4><?php echo __('Snack configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php    
echo $this->Form->input('paginationCount', array('label' => __('Pagination count')));
?>
</dl>

<?php
$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
);
echo $this->Form->end($options);
?>
