<?php

App::uses('Utils', 'Lib');

class Nas extends AppModel
{
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
				'allowEmpty' => false
				),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'IP already in the database.'
				),
			'ipFormat' => array(
				'rule' => array('isIPFormat'),
				'message' => 'This is not an IP address format.'
				)
			),
		'secret' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have to type the NAS secret.',
				'allowEmpty' => false
				)
			)
		);

	public function isIPFormat($field=array()) {
		foreach( $field as $key => $value ){ 
			$v1 = $value; 
			if(!Utils::isIP($v1)) { 
				return false; 
			} else { 
				continue; 
			} 
		} 
		return true; 
	}
}
?>
