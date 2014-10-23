<?php
echo $this->Form->input('session-timeout', array(
    'label' => __('Session timeout (time in seconds)'),
));
echo $this->Form->input('idle-timeout', array(
    'label' => __('Idle timeout (time in seconds)'),
));
?>