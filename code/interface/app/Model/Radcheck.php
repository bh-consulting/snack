<?php

App::uses('AuthComponent', 'Controller/Component');
class Radcheck extends AppModel
{
    public $useTable = 'radcheck';
    public $primaryKey = 'id';
    public $displayField = 'username';
    public $name = 'Radcheck';

    // association to Raduser
    public $belongsTo = array(
        'Raduser' => array(
            'className' => 'Raduser',
            'dependent' => true,
            'foreignKey' => 'username'
        )
    );

    // validation rules
    public $validate = array(
        'username' => array(
            'rule' => 'alphaNumeric',
            'message' => 'Usernames can only contains letters and numbers, not empty username.',
            'allowEmpty' => false
        )
    );

}
?>
