<?php

class BackupsController extends AppController
{
    public $helpers = array('Html', 'Form');
    public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));
    public $uses = array('Backup');
    public $components = array(
	'Filters' => array('model' => 'Backup'),
    );

    public function index($id = null)
    {
	$this->loadModel('Nas');
	$nas = new Nas($id);
	$nas = $nas->read();

	$this->set('nas', $nas['Nas']['nasname']);

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

    public function diff()
    {
	$git = '/home/pi/bh-consulting/trunk/code/db/backups-clone.git/';

	$a = $this->params['url']['a'];
	$b = $this->params['url']['b'];

	$this->loadModel('Nas');
	$backupA = new Backup($a);
	$backupA = $backupA->read();
	$backupB = new Backup($b);
	$backupB = $backupB->read();

	$commitA = $backupA['Backup']['commit'];
	$commitB = $backupB['Backup']['commit'];

	exec("cd $git; git diff $commitB $commitA", $output);

	$this->set('diff', implode("\n", $output));
    }
}

?>
