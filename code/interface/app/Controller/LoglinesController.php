<?php

App::uses('Sanitize', 'Utility');

class LoglinesController extends AppController {
    public $helpers = array('Html', 'Form');
    public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));
    public $components = array(
		'Filters' => array('model' => 'Logline')
    );

    public function index() {
		if ($this->request->is('post')) {
		    if (isset($this->request->data['MultiSelection']['logs'])
				&& is_array($this->request->data['MultiSelection']['logs'])
		    ) {
				$success = false;
				foreach( $this->request->data['MultiSelection']['logs'] as $logId ) {
				    switch( $this->request->data['action'] ) {
					    case "delete":
							$success = $this->Logline->delete($logId);
						break;
				    }

				    if($success){
						switch( $this->request->data['action'] ) {
						case "delete":
						    $this->Session->setFlash(
							__('Log lines have been deleted.'),
							'flash_success'
						    );
						    Utils::userlog(__('deleted log line %s', $logId));
						    break;
						}
				    } else {
						switch( $this->request->data['action'] ) {
						case "delete":
						    $this->Session->setFlash(
							__('Unable to delete log lines.'),
							'flash_error'
						    );
						    Utils::userlog(__('error while deleting log line %s', $logId), 'error');
						    break;
						}
				    }
				}
		    } else {
			$this->Session->setFlash(__('Please, select at least one log line !'), 'flash_warning');
		    }
		}
		$this->set('levels', $this->Logline->levels);

		$this->Filters->addListConstraint(array(
		    'column' => 'level', 
		    'default' => 'info',
		    'list' => array_reverse($this->Logline->levels),
		));

		$this->Filters->addDatesConstraint(array(
		    'column' => 'datetime', 
		    'from' => 'datefrom',
		    'to' => 'dateto',
		));

		$this->Filters->addStringConstraint(array(
		    'column' => 'msg', 
		));

		$this->Filters->paginate();
    }
}
?>
