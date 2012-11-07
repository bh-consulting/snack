<?php

App::uses('AuthComponent', 'Controller/Component');
class Radcheck extends AppModel
{
    var $useTable = 'radcheck';
    var $primaryKey = 'id';
    var $displayField = 'username';
    var $name = 'Radcheck';

    // validation rules
    var $validate = array(
        'username' => array(
            'rule' => 'alphaNumeric',
            'message' => 'Usernames can only contains letters and numbers, not empty username.',
            'allowEmpty' => false
        )
    );

}
?>
