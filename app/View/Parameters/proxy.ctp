<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_proxy_active', 'active');
?>

<br>

<?php
echo $this->Html->link(
        '<i class="glyphicon glyphicon-wrench glyphicon glyphicon-white"></i> ' . __('Edit proxy parameters'), array('controller' => 'parameters', 'action' => 'edit_proxy'), array('escape' => false, 'class' => 'btn btn-primary')
);
?>

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