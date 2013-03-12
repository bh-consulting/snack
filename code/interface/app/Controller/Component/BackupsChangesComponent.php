<?php
App::import('Model', 'Nas');
App::import('Model', 'Backup');

class BackupsChangesComponent extends Component {

    public function __construct($collection, $params) {

    }

    public function areThereChangesUnwrittenInThisNAS($nas) {
	$backup = new Backup();

	$lastWrmem = $backup->find('first', array(
	    'conditions' => array(
		'nas'    => $nas['Nas']['nasname'],
		'action' => 'wrmem'
	    ),
	    'fields'     => array('id'),
	    'order'      => array('id DESC'),
	    'limit'      => 1
	));

	$unwrittenCount = $backup->find('count', array(
	    'conditions' => array(
		'nas'    => $nas['Nas']['nasname'],
		'id >'   => isset($lastWrmem['Backup']) ? $lastWrmem['Backup']['id'] : 0
	    ),
	    'fields'     => array('id')
	));

	return $unwrittenCount > 0;
    }

    public function backupsUnwrittenInThisNAS($nas) {
	$backup = new Backup();

	$lastWrmem = $backup->find('first', array(
	    'conditions' => array(
		'nas'    => $nas['Nas']['nasname'],
		'action' => 'wrmem'
	    ),
	    'fields'     => array('id'),
	    'order'      => array('id DESC'),
	    'limit'      => 1
	));

	$unwrittenBackups = $backup->find('all', array(
	    'conditions' => array(
		'nas'    => $nas['Nas']['nasname'],
		'id >'   => $lastWrmem['Backup']['id']
	    ),
	    'fields'     => array('id')
	));

	return $unwrittenBackups;
    }

    public function areThereChangesUnwritten() {
	$nas = new Nas();
	$allnas = $nas->find('all');

	foreach($allnas AS $nas) {
	    if($this->areThereChangesUnwrittenInThisNAS($nas))
		return true;
	}

	return false;
    }
}

?>
