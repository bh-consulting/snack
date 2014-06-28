<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_general_active', 'active');
?>

<br>

<?php
echo $this->Html->link(
        '<i class="glyphicon glyphicon-wrench glyphicon glyphicon-white"></i> ' . __('Edit parameters'), array('controller' => 'parameters', 'action' => 'edit_general'), array('escape' => false, 'class' => 'btn btn-primary')
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
