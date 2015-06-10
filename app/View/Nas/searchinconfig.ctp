<?php

$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');
?>

<h1><? echo __('Search in all configurations'); ?></h1>

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

$myLabelOptions = array('text' => __('Text to search'));
echo $this->Form->input('searchtext', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$options = array(
    'label' => __('Search'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => "btn btn-primary",
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
    );
echo $this->Form->end($options);

if ($post) {
	//debug($results);
	echo "<h2>".__('Results')."</h2>";
	if (count($results) > 0) {
		echo '<table class="table table-hover table-bordered table-condensed">';
		echo "<th>Nasname</th>";
		echo "<th>Config Line</th>";
		foreach ($results as $nasname => $result) {	
			echo "<tr>";
			echo "<td>".$nasname."</td>";
			echo "<td></td>";
			echo "</tr>";
			foreach ($result as $res) {
				echo "<tr>";
				echo "<td></td>";
				echo "<td>";
				if(preg_match('/(.*)'.$pattern.'(.*)/', $res, $matches)) {
					echo $matches[1]."<b><span class='text-danger'>".$pattern."</span></b>".$matches[2];
				}
				echo "</td>";
				echo "</tr>";
			}
			
		}
		echo "</table>";
	}
}

?>