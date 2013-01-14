<?php

App::uses('Utils', 'Lib');

class Logline extends AppModel {
	var $useTable = 'logs';
	var $primaryKey = 'id';
	var $displayField = 'msg';
	var $name = 'Logline';

	public $severities = array(
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
			'rule' => 'date', /* HEEEERRRE */
			'message' => 'Format error with date from.'
		)
	);
}

?>
