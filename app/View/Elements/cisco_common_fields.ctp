<?php

echo '<fieldset>';
echo '<legend>' . __('Cisco') . '</legend>';

echo $this->Form->input('cisco', array(
    'type' => 'checkbox',
    'between' => '',
    'after'   => '',
    'class' => '', 
    //'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'before' => '<label class="col-sm-4 control-label">'.__('Cisco user').'</label><div class="col-sm-1">',
    'between' => '',
    'after'   => '</div>',
    'label' => false,
));

$mainLabelOptions = array('class' => 'col-sm-4 control-label');

$myLabelOptions = array('text' => __('NAS Port Type'));
echo  $this->Form->input('nas-port-type', array(
    'label' => array_merge($mainLabelOptions, $myLabelOptions),
    'options' => array(
        'Async' => __('Console'),
        'Virtual' => __('Telnet/SSH'),
        'both' => __('Both'),
    ),
    'readonly' => true,
    'empty' => false,
));

$priv = array();
for($i=1;$i<16;$i++) {
    $priv[$i]=$i;
}
$mainLabelOptions = array('class' => 'col-sm-4 control-label');
$myLabelOptions = array('text' => __('Privilege'));
echo $this->Form->input('privilege', array(
      'options' => $priv,
      'default' => '15',
      'label' => array_merge($mainLabelOptions, $myLabelOptions),
      'readonly' => true,
  ));

echo '</fieldset>';

?>
