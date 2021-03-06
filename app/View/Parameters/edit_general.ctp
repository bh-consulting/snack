<?php 
$this->extend('/Common/parameters_tabs');
$this->assign('param_active', 'active');

$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Parameter', array(
    'action' => 'edit',
    'novalidate' => true, 
    'autocomplete' => 'off',
    'class' => 'form-horizontal',
    'inputDefaults' => array(
        'div' => 'form-group',
        'label' => array(
            'class' => $mainLabelOptions
        ),
        'between' => '<div class="col-sm-4 input-group">',
        'after'   => '</div>',
        'class' => 'form-control'
    ),
));
?>

<h4><?php echo __('General information:'); ?></h4>
<dl class="well dl-horizontal">
<?php
$myLabelOptions = array('text' => __('Server IP'));
echo $this->Form->input('ipAddress', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$myLabelOptions = array('text' => __('Scripts path'));
echo $this->Form->input('scriptsPath', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'after'  => '<div class="input-group-addon">../</div></div>',
));

$myLabelOptions = array('text' => __('Certificates path'));
echo $this->Form->input('certsPath', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'after'  => '<div class="input-group-addon">../</div></div>',
));
?>
</dl>

<h4><?php echo __('Certificates configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php

$myLabelOptions = array('text' => __('Country'));
echo $this->Form->input('countryName', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$myLabelOptions = array('text' => __('State or province'));
echo $this->Form->input('stateOrProvinceName', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$myLabelOptions = array('text' => __('Locality'));
echo $this->Form->input('localityName', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$myLabelOptions = array('text' => __('Organization'));
echo $this->Form->input('organizationName', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
?>
</dl>

<h4><?php echo __('Snack configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php
$myLabelOptions = array('text' => __('Pagination count'));
echo $this->Form->input('paginationCount', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
?>
</dl>

<?php
$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => 'btn btn-primary',
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
);
echo $this->Form->end($options);
?>
