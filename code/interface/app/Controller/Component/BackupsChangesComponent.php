<?php
App::import('Model', 'Nas');
App::import('Model', 'Backup');

class BackupsChangesComponent extends Component {

    public function __construct($collection, $params) {

    }

    public function areThereChangesUnwrittenInThisNAS($nas) {
        $backup = new Backup();

        $writtenCount = $backup->find('count', array(
            'conditions' => array(
                "id = (SELECT MAX(id) FROM backups GROUP BY nas HAVING nas='"
		    . Sanitize::escape($nas['Nas']['nasname'])
		    . "')",
                'action !=' => 'wrmem'
            ),
        ));

        return $writtenCount != 0;
    }

    public function backupsUnwrittenInThisNAS($nas, $inverse = false) {
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
            'conditions' => array(
                'id IN (SELECT MAX(id) FROM backups GROUP BY nas)',
                'action !=' => 'wrmem'
            ),
        ));

        return $writtenNasCount != 0;
    }
}

?>
