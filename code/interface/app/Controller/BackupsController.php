<?php

class BackupsController extends AppController
{
    public $helpers = array('Html', 'Form');
    public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));
    public $uses = array('Backup');
    public $components = array(
	'Filters' => array('model' => 'Backup'),
    );

    private $git = '/home/pi/bh-consulting/trunk/code/db/backups-clone.git/';

    public function index($id = null)
    {
	$this->loadModel('Nas');
	$nas = new Nas($id);
	$nas = $nas->read();

	$this->set('nasID', $nas['Nas']['id']);
	$this->set('nasIP', $nas['Nas']['nasname']);
	$this->set('nasShortname', $nas['Nas']['shortname']);

	$this->Filters->addDatesConstraint(array(
	    'column' => 'datetime', 
	    'from' => 'datefrom',
	    'to' => 'dateto',
	));

	$this->Filters->addStringConstraint(array(
	    'column' => 'author', 
	));

	$this->Filters->paginate();
    }

    private function gitDiffNas($nas, $a, $b = null)
    {
	$backupA = $this->Backup->findById($a);

	if(!$backupA) {
	    throw new BadBackupOrNasID(
		'Please select an existant version A for this NAS.'
	    );
	}

        $commitA = $backupA['Backup']['commit'];

	$this->set('dateA', $backupA['Backup']['datetime']);
	$this->set('idA', $backupA['Backup']['id']);

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

    public function diff()
    {
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
		$this->params['url']['b'],
	    );

	} catch(BadBackupOrNasID $e) {
	    $this->Session->setFlash(
		$e->getMessage(),
		'flash_error'
	    );
	}
    }

    public function view($id = null, $nas = null)
    {
	try {
	    if($id != null && $nas != null) {
		$backup = $this->gitDiffNas($nas, $id);
		$commit = $backup['Backup']['commit'];

		exec("cd $this->git; git show $commit:testeuh", $output);
		$this->set('config', implode("\n", $output));

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
