<?php
App::import('Model', 'Nas');
App::import('Model', 'Backup');

class BackupsChangesComponent extends Component {

    public function __construct($collection, $params) {

    }

    public function areThereChangesNotWrittenInThisNAS($nas) {
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

	$noWriteMemed = $backup->find('count', array(
	    'conditions' => array(
		'nas'    => $nas['Nas']['nasname'],
		'id >'   => $lastWrmem['Backup']['id']
	    ),
	    'fields'     => array('id')
	));

	return $noWriteMemed > 0;
    }

    public function areThereChangesNotWritten() {
	$nas = new Nas();
	$allnas = $nas->find('all');

	foreach($allnas AS $nas) {
	    if($this->areThereChangesNotWrittenInThisNAS($nas))
		return true;
	}

	return false;
    }
}

?>
