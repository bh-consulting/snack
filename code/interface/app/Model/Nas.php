<?php

class Nas extends AppModel
{
    public $useTable = 'nas';
    public $primaryKey = 'id';
    public $displayField = 'nasname';
    public $name = 'Nas';

    public $validate = array(
    	'nasname' => array(
    		'notEmpty' => array(
    			'rule' => 'notEmpty',
    			'message' => 'You have to type the NAS IP',
    			'allowEmpty' => false
    		)
    	),
    	'secret' => array(
    		'notEmpty' => array(
    			'rule' => 'notEmpty',
    			'message' => 'You have to type the NAS secret',
    			'allowEmpty' => false
    		)
    	)
    );

}
?>
