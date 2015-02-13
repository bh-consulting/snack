<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_nagios_active', 'active');

echo $this->Form->create('Parameter', array('action' => 'edit_nagios', 'autocomplete' => 'off'));
?>

<h4><?php echo __('Nagios configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php 
echo $this->Form->input('nagios_ip', array('label' => __('Nagios IP Address')));
echo $this->Form->input('nagios_password', array('label' => __('Nagios Password'), 'type' => 'password'));
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
