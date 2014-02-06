<?php
echo $this->Form->input('comment');
echo $this->Form->input('expiration_date', array('label' => __('Expiration date'), 'class' => 'datetimepicker'));
echo $this->Form->input('simultaneous_use', array(
    'label' => __('Simultaneous Use'),
    'title' => __('Number of simultaneous 802.1x authorized connections with this user. Unlimited by default.'),
    'data-placement' => 'right'
));
?>
