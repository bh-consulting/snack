<?php

class LoglinesController extends AppController {
	public $helpers = array('Html', 'Form');
	//public $paginate = array('limit' => 1, 'order' => array('test1' => 'asc'));

	public function index() {
		//$this->set('logs', $this->paginate('Log', array(), array(
		$this->set('loglines', $this->Logline->read());
		$this->set('sortIcons', array('asc' => 'icon-chevron-down', 'desc' => 'icon-chevron-up'));
	}
}

?>
