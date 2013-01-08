<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo '<h1>Edit ' . $this->data['Raduser']['username'] . ' (login / password user)</h1>';

echo $this->Form->create('Raduser', array('action' => 'edit_loginpass'));
echo $this->Form->input('password');
echo $this->Form->input('confirm_password', array('type' => 'password', 'label' => 'Confirm password'));
echo $this->element('check_common_fields');
?>
<div class="lists-wrapper">
	<div class="pull-left list-container">
		<div class="list-title">Groups</div>
		<ul id="groups-available" class="sortList connectedList" subClass="label label-info">
		<?php
		foreach( $groups as $id=>$groupname )
			if( !in_array( $id, $groups_selected) )
				echo '<li id="' . $id . '">' . $groupname . '</li>';
		?>
		</ul>
	</div>
	<div class="pull-left list-container">
		<div class="list-title">Selected groups</div>
		<ul id="groups" class="sortList connectedList" subClass="label label-warning">
		<?php
		foreach( $groups_selected as $groupId )
			echo '<li id="' . $groupId . '">' . $groups[$groupId] .'</li>';
		?>
		</ul>
	</div>
</div>
<?php
echo $this->Form->input('groups', array('type' => 'select', 'id' => 'select-groups', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple', 'selected' => isset($groups_selected) ? array_values($groups_selected) : ''));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('username', array('type' => 'hidden'));
echo $this->Form->end('Update');
?>
