<?php
$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Parameter', array(
    //'url' => 'install',
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

<h4><?php echo __('Certificates configuration:'); ?></h4>
<dl class="well dl-horizontal">
<div id="alert-ca">
    
</div>
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
<div class="col-sm-offset-3 col-sm-6" id="loadingmsg">
</div>
<div class="col-sm-offset-5 col-sm-4" id="loadingicon">
</div>
<div class="col-sm-offset-4 col-sm-4">
    <a class="btn btn-primary" href="#" onClick="generateCA();" role="button">Generate Certificates</a>
</div>