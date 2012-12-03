<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo '<h1>Edit ' . $this->data['Raduser']['username'] . ' (MAC user)</h1>';

echo $this->Form->create('Raduser', array('action' => 'edit_mac'));
echo $this->Form->input('comment');
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('username', array('type' => 'hidden'));
echo $this->Form->end('Update');
?>
