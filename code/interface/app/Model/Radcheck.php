<?php

class Radcheck extends AppModel
{
    public $useTable = 'radcheck';
    public $primaryKey = 'id';
    public $displayField = 'username';
    public $name = 'Radcheck';

    public $validate = array(
        'username' => array(
            'rule' => 'notEmpty',
            'message' => 'Username cannot be empty',
            'allowEmpty' => false
        )
    );
}
?>
