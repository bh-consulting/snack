<?php

class Radgroupreply extends AppModel
{
    public $useTable = 'radgroupreply';
    public $primaryKey = 'id';
    public $displayField = 'groupname';
    public $name = 'Radgroupreply';

    public $validationDomain = 'validation';
    public $validate = array(
        'groupname' => array(
            'rule' => 'notBlank',
            'message' => 'Groupname cannot be empty',
            'allowEmpty' => false
        )
    );
}

?>