<?php

class LoglinesController extends AppController {
	public $helpers = array('Html', 'Form');

	public function index() {
		$this->set('loglines', $this->Logline->find('all'));
	}

	public function view($id = null) {
		$this->Logline->id = $id;
		$this->set('loglines', $this->Logline->read());
	}
}

?>
