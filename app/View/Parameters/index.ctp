<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('param_active', 'active');
?>

<h1><?php echo __('Server parameters'); ?></h1>

<?php
echo $this->Html->link(
        '<i class="glyphicon glyphicon-wrench glyphicon glyphicon-white"></i> ' . __('Edit server parameters'), array('controller' => 'parameters', 'action' => 'edit'), array('escape' => false, 'class' => 'btn btn-primary')
);
?>

<h4><?php echo __('General information:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('IP server'); ?></dt>
    <dd>
<?php
echo empty($ipAddress) ? __('Not set.') : $ipAddress;
?>
    </dd>
    <dt><?php echo __('Scripts path'); ?></dt>
    <dd>
<?php
echo empty($scriptsPath) ? __('Not set.') : $scriptsPath;
?>
    </dd>
    <dt><?php echo __('Certificates path'); ?></dt>
    <dd>
<?php
echo empty($certsPath) ? __('Not set.') : $certsPath;
?>
    </dd>
</dl>

<h4><?php echo __('Certificates configuration:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('Country'); ?></dt>
    <dd>
<?php
echo empty($countryName) ? __('Not set.') : $countryName;
?>
    </dd>

    <dt><?php echo __('State or province'); ?></dt>
    <dd>
<?php
echo empty($stateOrProvinceName) ?
        __('Not set.') : $stateOrProvinceName;
?>
    </dd>

    <dt><?php echo __('Locality'); ?></dt>
    <dd>
<?php
echo empty($localityName) ? __('Not set.') : $localityName;
?>
    </dd>

    <dt><?php echo __('Organization'); ?></dt> 
    <dd>
<?php
echo empty($organizationName) ?
        __('Not set.') : $organizationName;
?>
    </dd>
</dl>

<h4><?php echo __('Snack configuration:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('Pagination count'); ?></dt>
    <dd>
<?php
echo empty($paginationCount) ? __('Not set.') : $paginationCount;
?>
    </dd>
</dl>

<h4><?php echo __('Email configuration:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('SMTP Server'); ?></dt>
    <dd>
<?php
echo empty($smtp_ip) ? __('Not set.') : $smtp_ip;
?>
    </dd>
    <dt><?php echo __('Port'); ?></dt>
    <dd>
        <?php
        echo empty($smtp_port) ? __('Not set.') : $smtp_port;
        ?>
    </dd>
    <dt><?php echo __('Login'); ?></dt>
    <dd>
        <?php
        echo empty($smtp_login) ? __('Not set.') : $smtp_login;
        ?>
    </dd>
    <dt><?php echo __('Password'); ?></dt>
    <dd>
        <?php
        echo empty($smtp_password) ? __('Not set.') : $smtp_password;
        ?>
    </dd>
    <dt><?php echo __('Email from'); ?></dt>
    <dd>
        <?php
        echo empty($smtp_email_from) ? __('Not set.') : $smtp_email_from;
        ?>
    </dd>
    <dt><?php echo __('Configuration email'); ?></dt>
    <dd>
        <?php
        echo empty($configurationEmail) ? __('Not set.') : $configurationEmail;
        ?>
    </dd>
    <dt><?php echo __('Error email'); ?></dt>
    <dd>
        <?php
        echo empty($errorEmail) ? __('Not set.') : $errorEmail;
        ?>
    </dd>
</dl>

<h4><?php echo __('Proxy configuration:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('Adresse IP'); ?></dt>
    <dd>
<?php
echo empty($proxy_ip) ? __('Not set.') : $proxy_ip;
?>
    </dd>
    <dt><?php echo __('Port'); ?></dt>
    <dd>
        <?php
        echo empty($proxy_port) ? __('Not set.') : $proxy_port;
        ?>
    </dd>
    <dt><?php echo __('Login'); ?></dt>
    <dd>
        <?php
        echo empty($proxy_login) ? __('Not set.') : $proxy_login;
        ?>
    </dd>
    <dt><?php echo __('Password'); ?></dt>
    <dd>
        <?php
        echo empty($proxy_password) ? __('Not set.') : $proxy_password;
        ?>
    </dd>
</dl>

<h4><?php echo __('Cluster configuration:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('Role'); ?></dt>
    <dd>
<?php
echo empty($role) ? __('Not set.') : $role;
?>
    </dd>
    <dt><?php echo __('Master IP Address'); ?></dt>
    <dd>
        <?php
        echo empty($master_ip) ? __('Not set.') : $master_ip;
        ?>
    </dd>
    <dt><?php echo __('Slave IP to monitor'); ?></dt>
    <dd>
        <?php
        echo empty($slave_ip_to_monitor) ? __('Not set.') : $slave_ip_to_monitor;
        ?>
    </dd>
</dl>


