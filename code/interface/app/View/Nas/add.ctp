<? 
$this->extend('/Common/radius_sidebar'); 
$this->assign('nas_active', 'active');
?>

<h1>Add a NAS</h1>

<?
echo $this->Form->create('Nas');
echo $this->Form->input('nasname', array('label' => 'IP address')); 
echo $this->Form->input('shortname', array('label' => 'Name'));
echo $this->Form->input('secret', array('label' => 'Secret key'));
echo $this->Form->input('description');

echo $this->Form->end('Create');
?>
