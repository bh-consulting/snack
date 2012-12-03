<?php

class Radgroupcheck extends AppModel
{

    public $useTable = 'radgroupcheck';
    public $primaryKey = 'id';
    public $displayField = 'groupname';
    public $name = 'Radgroupcheck';

    public $validate = array(
        'groupname' => array(
            'rule' => 'notEmpty',
            'message' => 'Group name cannot be empty',
            'allowEmpty' => false
        )
    );
}
