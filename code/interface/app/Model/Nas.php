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

	public function readBackups($nas) {
	    $git = '/home/pi/bh-consulting/trunk/code/db/backups-clone.git';
	    $commits = array();

	    exec("cd $git; /usr/bin/git log $nas", $lines);

	    for($i = 0; $i < count($lines); $i++) {
		$lineParts = preg_split('/\s+/', $lines[$i]);

		if($lineParts[0] == 'commit') {
		    $commit['commit'] = $lineParts[1];

		    $lineParts = preg_split('/\s+/', $lines[++$i]);
		    $commit['author'] = $lineParts[1];

		    $lineParts = preg_split('/\s+/', $lines[++$i], 2);
		    $commit['datetime'] = $lineParts[1];

		    $commits[] = $commit;
		}
	    }

	    return $commits;
	}
}
?>
