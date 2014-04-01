<?php

echo '<fieldset>';
echo '<legend>' . __('Cisco') . '</legend>';

echo $this->Form->input(
    'cisco',
    array(
	'type' => 'checkbox',
	'label' => __('Cisco user'),
	'class' => 'switchbtn form-control'
    )
);

if($type != 'loginpass'){
    echo $this->Form->input('passwd', array('type' => 'password',
    	'label' => __('Password')));
    echo $this->Form->input('confirm_password',	array('type' => 'password',
	    'label' => __('Confirm password')));
}

echo $this->Form->input('nas-port-type', array(
    'options' => array(
	'Async' => __('Console'),
	'Virtual' => __('Telnet/SSH'),
	'both' => __('Both'),
    ),
    //'disabled' => true,
    'empty' => false,
    'label' => __('NAS Port Type'),
));

$priv = array();
for($i=1;$i<16;$i++) {
    $priv[$i]=$i;
}
echo $this->Form->input('privilege', array(
      'options' => $priv,
      'default' => '15',
      'label' => __('Privilege'),
  ));

echo '</fieldset>';

?>
