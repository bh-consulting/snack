<?php

class LoglinesController extends AppController {
	public $helpers = array('Html', 'Form');
	public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));

	public function index() {
		$anyline = $this->Logline->find('first'); // Pas moyen de trouver un getColumns...
		$this->set('loglines', $this->paginate('Logline', array(), array_keys($anyline['Logline'])));
		$this->set('sortIcons', array('asc' => 'icon-chevron-down', 'desc' => 'icon-chevron-up'));
	}

	public function view($id = null) {
		$this->Logline->id = $id;
		$this->set('loglines', $this->Logline->read());
	}
}

?>
