<?php

echo '<fieldset>';
echo '<legend>' . __('Snack rights') . '</legend>';
$mainLabelOptions = array('class' => 'col-sm-4 control-label');
$myLabelOptions = array('text' => __('Role'));
echo $this->Form->input('role', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'class' => 'slidermin',
));

echo '<dl class="well dl-horizontal">';
echo '<dt>' . __('User') . '</dt><dd>' . __('access to the network, not to the interface') . '</dd>';
echo '<dt>' . __('Tech') . '</dt><dd>' . __('view users, download certificates') . '</dd>';
echo '<dt>' . __('Admin') . '</dt><dd>' . __('view, create, update users') . '</dd>';
echo '<dt>' . __('Root') . '</dt><dd>' . __('view, create, update, delete all objects') . '</dd>';
echo '</dl>';
echo '</fieldset>';

?>
