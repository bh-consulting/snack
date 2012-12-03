<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo '<h1>Edit ' . $this->data['Raduser']['username'] . ' (certificate user)</h1>';

echo $this->Form->create('Raduser', array('action' => 'edit_cert'));
echo $this->Form->input('cert_gen', array('type' => 'checkbox', 'label' => 'Generate a new certificate'));
echo $this->Form->input('comment');
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('username', array('type' => 'hidden'));
echo $this->Form->end('Update');
?>

