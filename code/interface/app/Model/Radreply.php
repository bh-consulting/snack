<?php

class Radreply extends AppModel
{
    public $useTable = 'radreply';
    public $primaryKey = 'id';
    public $displayField = 'username';
    public $name = 'Radreply';
    
    public $validationDomain = 'validation';
    public $validate = array(
        'username' => array(
            'rule' => 'notEmpty',
            'message' => 'Username cannot be empty',
            'allowEmpty' => false
        )
    );
}

?>