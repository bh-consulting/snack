<?php

App::uses('Sanitize', 'Utility');

class LoglinesController extends AppController {
	public $helpers = array('Html', 'Form');
	public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));
	public $components = array(
		'Filters' => array('model' => 'Logline')
	);
	
	public function index() {
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

	public function view($id = null) {
		$this->Logline->id = $id;
		$this->set('loglines', $this->Logline->read());
	}
}

?>
