<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_email_active', 'active');

echo $this->Form->create('Parameter', array('action' => 'edit_email', 'autocomplete' => 'off'));
?>

<h4><?php echo __('Email configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php 
echo $this->Form->input('smtp_ip', array('label' => __('SMTP IP Address')));
echo $this->Form->input('smtp_port', array('label' => __('SMTP Port')));
echo $this->Form->input('smtp_login', array('label' => __('SMTP Login')));
echo $this->Form->input('smtp_password', array('label' => __('SMTP Password'), 'type' => 'password'));
echo $this->Form->input('smtp_email_from', array('label' => __('SMTP Email From'), 'class' => 'email', 'empty' => true));
echo $this->Form->input(
    'configurationEmail',
    array(
        'label' => __('Email destination'),
        'class' => 'email',
    )
);
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
