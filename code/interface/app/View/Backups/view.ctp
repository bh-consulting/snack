<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

if(isset($diff) && isset($config)):
?>

<h1><?php echo __('View and restore'); ?></h1>
<h2><?php echo __('Configuration'); ?></h2>

<ul>
    <li><?php echo __('<strong>%s:</strong> %s',
			__('NAS'),
			$nasShortname) ?></li>
    <li><?php echo __('<strong>%s:</strong> %s',
			__('IP'),
			$nasIP) ?></li>
    <li><?php echo __('<strong>%s:</strong> %s',
			__('Date'),
			$dateA) ?></li>
</ul>

<h2><?php echo __('Comparison with the current configuration'); ?></h2>

<pre class="well">
<?php echo $diff ?>
</pre>

<h2><?php echo __('Contents'); ?></h2>

<pre class="well">
<?php echo $config ?>
</pre>

<?php
echo $this->Html->link(
	'<i class="icon-repeat icon-white"></i> ' . __('Restore'),
	array(
	    'controller' => 'backups',
	    'action' => 'restore',
	    $idA,
	    $nasID,
	),
	array(
	    'onclick' => "return confirm('" . __('Are you sure?') . "')",
	    'escape' => false,
	    'class' => 'btn btn-primary',
	)
    );
?>

<?php
endif;

echo $this->Html->link(
	'<i class="icon-arrow-left icon-white"></i> <i class="icon-camera icon-white"></i> ' . __('Go back to backups'),
	'#',
	array(
	    'onclick' => 'history.go(-1)',
	    'escape' => false,
	    'class' => 'btn btn-primary',
	)
    );
?>
