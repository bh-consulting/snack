<?php
App::import('Model', 'Nas');
App::import('Model', 'Backup');

class BackupsChangesComponent extends Component {

    public function __construct($collection, $params) {

    }

    public function areThereChangesUnwrittenInThisNAS($nas) {
        $backup = new Backup();

        $areThereUnwritten = $backup->find('count', array(
            'conditions' => array(
                "id = (SELECT MAX(id) FROM backups GROUP BY nas HAVING nas='"
		    . Sanitize::escape($nas['Nas']['nasname'])
		    . "')",
                'action NOT REGEXP' => '^(wrmem|boot)$'
            ),
        ));

	if($areThereUnwritten == 0) {
	    if(!$this->areThereBackupsForThisNAS($nas))
		$areThereUnwritten = 1;
	}

        return $areThereUnwritten != 0;
    }

    public function areThereBackupsForThisNAS($nas) {
        $backup = new Backup();

	$backupsCount = $backup->find('count', array(
		'conditions' => array(
		    'nas' => $nas['Nas']['nasname']
		),
	));

	return $backupsCount > 0;
    }

    public function backupsUnwrittenInThisNAS($nas, $inverse = false) {
        $backup = new Backup();

        $lastWrmem = $backup->find('first', array(
            'conditions' => array(
                'nas'    => $nas['Nas']['nasname'],
                'action REGEXP' => '^(wrmem|boot)$'
            ),
            'fields'     => array('id'),
            'order'      => array('id DESC'),
            'limit'      => 1
        ));

        $condition = ($inverse) ? 'id <' : 'id >';

        $unwrittenBackups = $backup->find('all', array(
            'conditions' => array(
                'nas'    => $nas['Nas']['nasname'],
                $condition   => !empty($lastWrmem['Backup']) ? $lastWrmem['Backup']['id'] : 0
            ),
            'fields'     => array('id')
        ));

        return $unwrittenBackups;
    }

    public function areThereChangesUnwritten() {
        $backup = new Backup();

        $writtenNasCount = $backup->find('count', array(
	    'joins' => array(
		array (
		    'table' => 'nas',
		    'type' => 'RIGHT',
		    'conditions' => array(
			    'nas = nasname'
		    )
		)
	    ),
            'conditions' => array(
		'OR' => array(
		    'nasname NOT IN (SELECT DISTINCT nas FROM backups)',
		    'AND' => array(
			'Backup.id IN (SELECT MAX(id) FROM backups GROUP BY nas)',
			'action NOT REGEXP' => '^(wrmem|boot)$'
		     )
		),
            ),
        ));

        return $writtenNasCount != 0;
    }
}

?>
