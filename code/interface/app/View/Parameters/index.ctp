<?php
$this->extend('/Common/radius_sidebar'); 
$this->assign('param_active', 'active');
?>

<h1><?php echo __('Server parameters'); ?></h1>

<?php
echo $this->Html->link(
	'<i class="icon-wrench icon-white"></i> ' . __('Edit server parameters'),
	array('controller' => 'parameters', 'action' => 'edit'),
	array('escape' => false, 'class' => 'btn btn-primary')
    );
?>

<h4><?php echo __('General information:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo $contactEmail['label']; ?></dt>
    <dd>
<?php
echo empty($contactEmail['value']) ? __('Not set.') : $contactEmail['value'];
?>
    </dd>
</dl>

<h4><?php echo __('Certificates configuration:'); ?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo $countryName['label']; ?></dt>
    <dd>
<?php
echo empty($countryName['value']) ? __('Not set.') : $countryName['value'];
?>
    </dd>

    <dt><?php echo $stateOrProvinceName['label']; ?></dt>
    <dd>
<?php
echo empty($stateOrProvinceName['value']) ?
    __('Not set.')
    : $stateOrProvinceName['value'];
?>
    </dd>

    <dt><?php echo $localityName['label']; ?></dt> 
    <dd>
<?php
echo empty($localityName['value']) ? __('Not set.') : $localityName['value'];
?>
    </dd>

    <dt><?php echo $organizationName['label']; ?></dt> 
    <dd>
<?php
echo empty($organizationName['value']) ?
    __('Not set.')
    : $organizationName['value'];
?>
    </dd>
</dl>
