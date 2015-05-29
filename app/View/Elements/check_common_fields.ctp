<?php
$mainLabelOptions = array('class' => 'col-sm-4 control-label');
$myLabelOptions = array('text' => __('Comment'));
echo  $this->Form->input('comment', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));

$myLabelOptions = array('text' => __('Expiration date'));
echo $this->Form->input('expiration_date', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions), 
    'class' => 'datetimepicker form-control',
    'between' => '<div class="col-sm-4"><div class="input-group date form_datetime col-sm-12">',
    'after'   => '</div></div>')
);

$myLabelOptions = array('text' => __('Simultaneous Use'));
echo $this->Form->input('simultaneous_use', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'title' => __('Number of simultaneous 802.1x authorized connections with this user. Unlimited by default.'),
    'data-placement' => 'right'
));
?>
