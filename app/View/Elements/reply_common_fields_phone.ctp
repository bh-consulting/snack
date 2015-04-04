<?php
$mainLabelOptions = array('class' => 'col-sm-4 control-label');
$myLabelOptions = array('text' => __('Session timeout (time in seconds)'));
echo $this->Form->input('session-timeout', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$myLabelOptions = array('text' => __('Idle timeout (time in seconds)'));
echo $this->Form->input('idle-timeout', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
?>