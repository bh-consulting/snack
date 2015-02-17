<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_email_active', 'active');
?>

<br>

<?php
echo $this->Html->link(
        '<i class="glyphicon glyphicon-wrench glyphicon-white"></i> ' . __('Edit email parameters'), array('controller' => 'parameters', 'action' => 'edit_email'), array('escape' => false, 'class' => 'btn btn-primary')
);
echo " ";
echo $this->Html->link(
        '<i class="glyphicon glyphicon glyphicon-send"></i> ' . __('Send email test'), array('controller' => 'parameters', 'action' => 'send_emailtest'), array('escape' => false, 'class' => 'btn btn-primary')
);
?>

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
        echo empty($smtp_password) ? __('Not set.') : "******";
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
