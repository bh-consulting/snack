<?php

class BackupsController extends AppController {
    public $helpers = array('Html', 'Form');
    public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));
    public $uses = array('Backup');
    public $components = array(
        'Filters',
        'Users' => array()
    );

    private $git = '~snack/backups.git/';

    public function getRegexSynchronisation($args = array()) {
        if (!empty($args['input']) && !empty($args['nas'])) {
            $data = &$this->request->data['Backup'][$args['input']];
            $regex = '(1 = 1';
            $ids = array();
            $flag = false;

            foreach ((array)$data as $choice) {
                switch ($choice) {
                case 'changed':
                    $ids = array_merge(
                        $ids,
                        (array)$this->getUnwrittenBackups($args['nas'])
                    );
                    $flag = true;
                    break;
                case 'notchanged':
                    $ids = array_merge(
                        $ids,
                        (array)$this->getUnwrittenBackups($args['nas'], true)
                    );
                    $flag = true;
                    break;
                }
            }

            if (!empty($ids)) {
                $regex .= ' AND id IN ('
                    . implode($ids, ',')
                    . '))';
            } else if ($flag) {
                $regex = '(1=0)'; 
            } else {
                $regex .= ')';
            }

            if (!empty($regex) && $regex != '(1 = 1)') {
                return $regex;
            }
        }
    }

    public function getUnwrittenBackups($nas, $inverse = false) {
        $backups = $this->BackupsChanges
            ->backupsUnwrittenInThisNAS($nas, $inverse);

		$backupIds = array();

		foreach($backups as $backup) {
		    $backupIds[] = $backup['Backup']['id'];
        }

        return $backupIds;
    }

    public function index($id = null) {
		$this->loadModel('Nas');
		$nas = $this->Nas->findById($id);

		$this->set('nasID', $nas['Nas']['id']);
		$this->set('nasIP', $nas['Nas']['nasname']);
		$this->set('nasShortname', $nas['Nas']['shortname']);

		$this->Filters->addDatesConstraint(array(
		    'field' => 'datetime', 
		    'from' => 'datefrom',
		    'to' => 'dateto',
		));

		$this->Filters->addStringConstraint(array(
		    'fields' => 'users', 
		    'input' => 'author', 
            'ahead' => array('users'),
		));

		$this->Filters->addStringConstraint(array(
		    'fields' => 'nas', 
		    'input' => 'nas', 
		    'value'  => $nas['Nas']['nasname'],
            'strict' => true,
		));

        $this->Filters->addSelectConstraint(array(
            'fields' => array('action'),
            'items' => $this->Backup->actions,
            'input' => 'action',
            'title' => false,
        ));

        $this->Filters->addComplexConstraint(array(
            'select' => array(
                'items' => array(
                    'notchanged' => '<i class="icon-ok-sign icon-green"></i> '
                    . __('Synchronized'),
                    'changed' => ' <i class="icon-exclamation-sign icon-red"></i> '
                    . __('Not synchronized'),
                ),
                'input' => 'writemem',
                'title' => false,
            ),
            'callback' => array(
                'getRegexSynchronisation',
                array(
                    'input' => 'writemem',
                    'nas' => $nas,
                ),
            )
        ));

		$this->Filters->addGroupConstraint('commit');

		$backups = $this->Filters->paginate();

		$users = array();

		foreach($backups as &$backup) {
		    $users[$backup['Backup']['id']] =
			$this->Users->extendUsers($backup['Backup']['users']);

            if (isset($backup['Backup']['datetime'])) {
                $backup['Backup']['datetime'] = Utils::formatDate(
                    $backup['Backup']['datetime'],
                    'display'
                );
            }
		}

		$this->set('users', $users);
		$this->set('unwrittenids', $this->getUnwrittenBackups($nas));
		$this->set('backups', $backups);
    }

    private function gitDiffNas($nas, $a, $b = null) {
		$backupA = $this->Backup->findById($a);

		if(!$backupA) {
		    throw new BadBackupOrNasID(
			'Please select an existant version A for this NAS.'
		    );
		}

		$commitA = $backupA['Backup']['commit'];

		$this->set('dateA', $backupA['Backup']['datetime']);
		$this->set('idA', $backupA['Backup']['id']);
		$this->set('actionA', $backupA['Backup']['action']);
		$this->set('usersA', $this->Users->extendUsers($backupA['Backup']['users']));

		if ($b != null) {
		    $b = $this->params['url']['b'];
		    $backupB = $this->Backup->findById($b);

		    if(!$backupB) {
			throw new BadBackupOrNasID(
			    'Please select an existant version B for this NAS.'
			);
		    }

		    $commitB = $backupB['Backup']['commit'];

		    $this->set('dateB', $backupB['Backup']['datetime']);
		    $this->set('idB', $backupB['Backup']['id']);
		    $this->set('actionB', $backupB['Backup']['action']);
		    $this->set('usersB', $backupB['Backup']['users']);

		} else
		    $commitB = null; // last

		$this->loadModel('Nas');
		$nas = $this->Nas->findById($nas);

		if(!$nas) {
		    throw new BadBackupOrNasID(
			'Please select an existant NAS.'
		    );
		}

		$this->set('nasID', $nas['Nas']['id']);
		$this->set('nasIP', $nas['Nas']['nasname']);
		$this->set('nasShortname', $nas['Nas']['shortname']);

		exec("cd $this->git; git diff $commitB $commitA", $output);
		$this->set('diff', implode("\n", $output));

		return $backupA;
    }

    public function diff() {
		try {
		    if(!isset($this->params['url']['nas'])
			|| !isset($this->params['url']['a'])
			|| !isset($this->params['url']['b'])) {

				throw new BadBackupOrNasID(
				    'Please select specific versions for a specific NAS.'
				);
		    }

		    $this->gitDiffNas(
				$this->params['url']['nas'],
				$this->params['url']['a'],
				$this->params['url']['b']
		    );

		} catch(BadBackupOrNasID $e) {
		    $this->Session->setFlash(
				$e->getMessage(),
				'flash_error'
		    );
		}
    }

    public function view($id = null, $nas = null) {
		try {
		    if($id != null && $nas != null) {
				$this->loadModel('Nas');
				$nas = $this->Nas->findById($nas);

				if(!$nas) {
				    throw new BadBackupOrNasID(
					'Please select an existant NAS.'
				    );
				}

				$backup = $this->gitDiffNas($nas['Nas']['id'], $id);
				$commit = $backup['Backup']['commit'];

				exec("cd $this->git; git show $commit:{$nas['Nas']['nasname']}", $output);
				$this->set('config', implode("\n", $output));
				$this->set('backupID', $id);

				$this->Filters->addStringConstraint(array(
				    'fields' => 'commit', 
				    'input' => 'commit', 
				    'value'  => $commit,
				));

				$this->Filters->addStringConstraint(array(
				    'fields' => 'nas', 
				    'input' => 'nas', 
				    'value'  => $nas['Nas']['nasname'],
				));

				$backups = $this->Filters->paginate();
				$users = array();

				foreach($backups AS $backup) {
				    $users[$backup['Backup']['id']] =
					$this->Users->extendUsers($backup['Backup']['users']);
				}

				$this->set('users', $users);
		    } else {
				throw new BadBackupOrNasID(
				    'Please select a NAS and a configuration version.'
				);
		    }

		} catch(BadBackupOrNasID $e) {
		    $this->Session->setFlash(
				$e->getMessage(),
				'flash_error'
		    );
		}
    }

    public function restore($id, $nas) {
	
    }
}

?>
