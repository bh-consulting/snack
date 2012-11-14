<?php

class Radcheck extends AppModel
{
    public $useTable = 'radcheck';
    public $primaryKey = 'id';
    public $displayField = 'username';
    public $name = 'Radcheck';

    // association to Raduser
    /*
    public $belongsTo = array(
        'Raduser' => array(
            'className' => 'Raduser',
            'dependent' => true,
            'foreignKey' => false,
            'conditions' => array('Radcheck.username = Raduser.username')
        )
    );
     */
}
?>
