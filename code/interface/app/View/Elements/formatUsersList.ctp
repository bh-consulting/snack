<?php

$usersList = array();

foreach($users AS $user) {
    if($user['id'] != -1) {
        $usersList[] = $this->Html->link(
    	"<i class='icon-user'></i> {$user['username']}",
    	array(
    	    'controller' => 'raduser',
    	    'action' => 'edit',
    	    $user['id'],
    	),
    	array(
    	    'escape' => false,
    	)
        );
    } else {
        $usersList[] = $user['username'];
    }
}

echo implode(', ', $usersList);

?>
