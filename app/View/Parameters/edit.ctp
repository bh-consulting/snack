<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('param_active', 'active');

echo '<h1>' . __('Edit server parameters') . '</h1>';

echo $this->Form->create('Parameter', array('action' => 'edit'));
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

<h4><?php echo __('Email configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php 
echo $this->Form->input('smtp_ip', array('label' => __('SMTP IP Address')));
echo $this->Form->input('smtp_port', array('label' => __('SMTP Port')));
echo $this->Form->input('smtp_login', array('label' => __('SMTP Login')));
echo $this->Form->input('smtp_password', array('label' => __('SMTP Password'), 'type' => 'password'));
echo $this->Form->input('smtp_email_from', array('label' => __('SMTP Email From'), 'class' => 'email'));
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
?>
</dl>

<h4><?php echo __('Proxy configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php 
echo $this->Form->input('proxy_ip', array('label' => __('Proxy IP Address')));
echo $this->Form->input('proxy_port', array('label' => __('Proxy Port')));
echo $this->Form->input('proxy_login', array('label' => __('Proxy Login')));
echo $this->Form->input('proxy_password', array('label' => __('Proxy Password'), 'type' => 'password'));
?>
</dl>

<h4><?php echo __('Cluster configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php 
echo $this->Form->input('role', array(
    'class' => 'role',
    'options' => array(
        'master' => __('Master'),
        'slave' => __('Slave'),
    ),
    //'disabled' => true,
    'empty' => false,
    'label' => __('Role'),
));
echo $this->Form->input('master_ip', array('label' => __('Master IP Address'), 'readonly' => true));
echo $this->Form->input('slave_ip_to_monitor', array('label' => __('Slave IP to monitor')));
?>
</dl>
<?php
echo $this->Form->end(__('Update'));
?>
