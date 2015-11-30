<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');
?>
<h1><?php echo __('Find MAC Address'); ?></h1>

<?php
$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Nas', array(
    'novalidate' => true, 
    'autocomplete' => 'off',
    'class' => 'form-horizontal',
    'inputDefaults' => array(
        'div' => 'form-group',
        'label' => array(
            'class' => $mainLabelOptions
        ),
        'between' => '<div class="col-sm-4">',
        'after'   => '</div>',
        'class' => 'form-control'
    ),
));

$myLabelOptions = array('text' => __('MAC Address'));
echo $this->Form->input('macaddress', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
$options = array(
    'label' => __('Find'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => "btn btn-primary",
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>',
    'onclick' => 'load();'
    );
echo $this->Form->end($options);


echo "<div class='load'></div>";

if (isset($results)) {
	echo "<h1>".__('Results')."</h1>";
	echo "<table class='table table-bordered table-striped table-condensed'>";
	echo "<th>Hostname</th><th>VLAN</th><th>Status</th><th>interface</th>";
	foreach($results as $hostname=>$result) {
		if (count($result[0]) > 0 || count($result)>1) {
			echo "<tr>";
			echo "<td>".$hostname."</td>";
			echo "</tr>";
			foreach($result as $res) {
				echo "<tr>";
				echo "<td></td>";
				echo "<td>";
				echo isset($res['vlan']) ? $res['vlan'] : "";
				echo "</td>";
				echo "<td>";
				echo isset($res['status']) ? $res['status'] : "";
				echo "</td>";
				echo "<td>";
				echo isset($res['interface']) ? $res['interface'] : "";
				echo "</td>";
				echo "</tr>";
			}
		}
	}
	echo "</table>";
}
