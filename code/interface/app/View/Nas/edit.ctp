<? 
$this->extend('/Common/radius_sidebar');
$this->assign('nas_active', 'active');

echo '<h1>Edit NAS ' . $this->data['Nas']['nasname'] . '</h1>';

echo $this->Form->create('Nas', array('action' => 'edit'));
echo $this->Form->input('nasname', array('label' => 'IP address')); 
echo $this->Form->input('shortname', array('label' => 'Name'));
echo $this->Form->input('secret', array('label' => 'Secret key'));
echo $this->Form->input('description');
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->end('Update');
?>
