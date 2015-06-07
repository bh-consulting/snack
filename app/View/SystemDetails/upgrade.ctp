<?php
$this->extend('/Common/systemdetails_tabs');
$this->assign('systemdetails_upgrade_active', 'active');
?>
<h2>
<?php
switch ($status) {
	case '0':
		if ($updates != 0) {
			echo "<p>".$this->Html->link(
			    '<i class="glyphicon glyphicon-refresh glyphicon-white"></i> ' . __('Upgrade'), array('controller' => 'systemDetails', 'action' => 'upgrade/true'), array('class' => 'btn btn-success btn-large', 'escape' => false)
			)."</p>";
		}
		echo "Last Upgrade :";
		break;
	case '1':
		echo __("Upgrade stated");
		break;
	case '2':
		echo __("Upgrade in progress ...");
		break;	
}
?>
</h2>
<pre>
<?php
	echo $logupgrade;
?>
</pre>
