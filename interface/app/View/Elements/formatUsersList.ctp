<?php

$usersList = array();

foreach($users AS $user) {
    if($user['id'] != -1) {
        $usersList[] = $this->Html->link(
	    "<i class='icon-user'></i>&nbsp;{$user['username']}",
	    array(
		'controller' => 'radusers',
		'action' => 'view_snack',
		$user['id'],
	    ),
	    array(
		'escape' => false,
	    )
        );
    } else {
	$usersList[] = "<span class='unknown' title='" . __('User unknown') . "'><i class='icon-user icon-red'></i>&nbsp;{$user['username']}</span>";
    }
}

echo implode(', ', $usersList);

?>
