<?php

App::uses('Utils', 'Lib');

class Nas extends AppModel {
	public $useTable = 'nas';
	public $primaryKey = 'id';
	public $displayField = 'nasname';
	public $name = 'Nas';

	public $validationDomain = 'validation';

	public $validate = array(
		'nasname' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have to type the NAS IP.',
				'allowEmpty' => false,
				'required' => true,
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'IP already in the database.',
			),
			'ipFormat' => array(
				'rule' => 'isIPFormat',
				'message' => 'This is not an IP address format.',
			),
		),
		'secret' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have to type the NAS secret.',
				'required' => true,
				'allowEmpty' => false,
			),
		)
	);

	public function isIPFormat($field=array()) {
		$value = array_shift($field);
		if(Utils::isIP($value)) { 
			return true; 
		}
		return false; 
	}
}
?>
