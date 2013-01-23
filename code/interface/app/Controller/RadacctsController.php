<?php

class RadacctsController extends AppController
{
    public $helpers = array('Html', 'Form');
    public $paginate = array('limit' => 10, 'order' => array( 'acctuniqueid' => 'asc' ) );

    public function index() {
	if ($this->request->is('post')) {
	    if (isset($this->request->data['MultiSelection']['sessions'])
		&& is_array($this->request->data['MultiSelection']['sessions'])
	    ) {
		$success = false;
		foreach( $this->request->data['MultiSelection']['sessions'] as $sessionId ) {
		    switch( $this->request->data['action'] ) {
		    case "delete":
			$success = $this->Radacct->delete($sessionId);
			break;
		    }

		    if($success){
			switch( $this->request->data['action'] ) {
			case "delete":
			    $this->Session->setFlash(
				__('Sessions have been deleted.'),
				'flash_success'
			    );
			    break;
			}
		    } else {
			switch( $this->request->data['action'] ) {
			case "delete":
			    $this->Session->setFlash(
				__('Unable to delete sessions.'),
				'flash_error'
			    );
			    break;
			}
		    }
		}
	    } else {
		$this->Session->setFlash(__('Please, select at least one session !'), 'flash_warning');
	    }
	}
	$this->set(
	    'radaccts',
	    $this->paginate(
		'Radacct',
		array(),
		array(
		    'acctuniqueid', 'username',
		    'acctstarttime', 'acctstoptime',
		    'nasipaddress', 'nasportid',
		)
	    )
	);
	$this->set(
	    'sortIcons',
	    array(
		'asc' => 'icon-chevron-down',
		'desc' => 'icon-chevron-up',
	    )
	);
    }

    public function view($id = null)
    {
	$this->Radacct->id = $id;
	$this->set('radacct', $this->Radacct->read());
    }

    public function delete($id) {
	if ($this->request->is('get')) {
	    throw new MethodNotAllowedException();
	}

	if ($this->Radacct->delete($id)) {
	    $this->Radacct->setFlash(
		__('The Session with id:')
		. $id
		. __(' has been deleted.'),
		'flash_success'
	    );
	    $this->redirect(array('action' => 'index'));
	}
    }
}

?>
