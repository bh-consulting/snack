<?php
echo $this->Form->input('tunnel-private-group-id', array(
    'label' => __('VLAN number (if using a VLAN)'),
    'placeholder' => __('Ex: 42')
));
echo $this->Form->input('session-timeout', array(
    'label' => __('Session timeout (time in seconds)'),
    'placeholder' => __('Ex: 10')
));
?>