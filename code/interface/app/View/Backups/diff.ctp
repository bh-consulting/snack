<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');
?>

<h1><?php echo __('Compare'); ?></h1>

<pre class="well">
<?php echo $diff ?>
</pre>

<?php
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
