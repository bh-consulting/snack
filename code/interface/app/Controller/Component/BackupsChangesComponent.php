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
                "commit = (SELECT commit FROM backups WHERE nas='"
		    . Sanitize::escape($nas['Nas']['nasname'])
		    . "' ORDER BY id DESC LIMIT 1)",
		'nas' => $nas['Nas']['nasname'],
                'action REGEXP' => '^(wrmem|boot)$'
            ),
        ));

        return $areThereUnwritten == 0;
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

        $areThereChangesUnwritten = $backup->query("
	    SELECT (
		SELECT COUNT(*)
		FROM (
		    SELECT id
		    FROM backups b, (
			SELECT nas,commit
			FROM (
			    SELECT *
			    FROM backups
			    ORDER BY id DESC) b
			GROUP BY b.nas) l
			WHERE b.nas=l.nas
			AND b.commit=l.commit
			AND action REGEXP '^(wrmem|boot)\$') c
	    ) = (
		SELECT COUNT(*)
		FROM nas) synchronized
	    FROM DUAL");

	return !$areThereChangesUnwritten[0][0]['synchronized'];
    }
}

?>
