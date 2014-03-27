<?php

class Logline extends AppModel {
	public $useTable = 'logs';
	public $primaryKey = 'id';
	public $displayField = 'msg';
	public $name = 'Logline';
   
    public $levels = array(
		'debug' => 'Debug',
		'info' => 'Info',
		'notice' => 'Notice',
		'warn' => 'Warning',
		'err' => 'Error',
		'crit' => 'Critical',
		'alert' => 'Alert',
		'emerg' => 'Emergency'
	);

	public $validate = array(
		'datefrom' => array(
			'rule' => array('datetime', 'dmy'),
			'message' => 'Format error with date from.',
			'allowEmpty' => true,
		),
		'dateto' => array(
			'rule' => array('datetime', 'dmy'),
			'message' => 'Format error with date to.',
			'allowEmpty' => true,
		),
		'severity' => array(
            'rule' => array(
                'inList',
                array(
                    'debug', 'info', 'notice', 'warn',
                    'err', 'crit', 'alert', 'emerg'
                )
            ),
			'message' => 'Format error with severity.',
			'allowEmpty' => true,
		)
	);

}

?>
