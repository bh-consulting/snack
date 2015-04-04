<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_ad_active', 'active');

$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Parameter', array(
    'action' => 'edit_ad',
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
<h4><?php echo __('ActiveDirectory configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php 

    echo '<div class="col-sm-4"></div>Status : ';
    if (empty($adstatus)) {
        echo __('Not set.');
    }
    elseif(preg_match("/^Joined .*/", $adstatus, $matches)) {
        echo "<center><b><font color='green'>".$adstatus."</font></b></center><br>";
    }
    else {
        echo "<center><b><font color='red'>".$adstatus."</font></b></center><br>";
    }
    $myLabelOptions = array('text' => __('Active Directory IP'));
    echo $this->Form->input('adip', array(
        'label' => array_merge($mainLabelOptions, $myLabelOptions),
    ));

    $myLabelOptions = array('text' => __('Domain'));
    echo $this->Form->input('addomain', array(
        'label' => array_merge($mainLabelOptions, $myLabelOptions),
    ));
    
    $myLabelOptions = array('text' => __('Admin Username'));
    echo $this->Form->input('adminusername', array(
        'label' => array_merge($mainLabelOptions, $myLabelOptions),
    ));

    $myLabelOptions = array('text' => __('Admin Password'));
    echo $this->Form->input('adminpassword', array(
        'label' => array_merge($mainLabelOptions, $myLabelOptions),
    ));
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