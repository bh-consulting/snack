<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

echo '<h1>' . __('Edit NAS') . ' ' . $this->data['Nas']['nasname'] . '</h1>';

echo $this->Form->create('Nas', array('action' => 'edit'));
echo $this->Form->input('nasname', array('label' => __('IP address'))); 
echo $this->Form->input('shortname', array('label' => __('Name')));
echo $this->Form->input('secret', array('label' => __('Secret key')));
echo $this->Form->input('description');
echo $this->Form->input('id', array('type' => 'hidden'));

$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
    );
echo $this->Form->end($options);
?>
