<?php
echo $this->Form->input('tunnel-private-group-id', array(
    'label' => __('VLAN number (if using a VLAN)'),
));
echo $this->Form->input('session-timeout', array(
    'label' => __('Session timeout (time in seconds)'),
));
echo $this->Form->input('idle-timeout', array(
    'label' => __('Idle timeout (time in seconds)'),
));
?>