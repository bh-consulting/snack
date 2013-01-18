<?php
echo $this->Form->input('tunnel-private-group-id', array('label' => __('VLAN number (if using a VLAN)'), 'placeholder' => '42'));
echo $this->Form->input('reply-message', array('label' => __('Message displayed to user'), 'placeholder' => __('Hello, %User-Name')));
echo $this->Form->input('exec-program-wait', array('label' => __('Run a program and wait its end'), 'placeholder' => '/usr/local/bin/program 3 dupont "30 Mar 2007 00:00:00'));
echo $this->Form->input('session-timeout', array('label' => __('Session timeout (time in seconds)'), 'placeholder' => '10'));
?>