<?php

class LoglinesController extends AppController {
	public $helpers = array('Html', 'Form', 'JqueryEngine');
	public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));

	public function index() {
		$constraints = array();
		$this->request->data['Logline'] = $this->params['url'];

		$severities = array(
			'emerg' => 'Emergency',
			'alert' => 'Alert',
			'crit' => 'Critical',
			'err' => 'Error',
			'warn' => 'Warning',
			'notice' => 'Notice',
			'info' => 'Info',
			'debug' => 'Debug'
		);

		// Search filter Severity
		if(!empty($this->params['url']['severity'])) {
			$regex = array();

			foreach($severities as $keyword => $severity) {
				if($keyword == $this->params['url']['severity'])
					break;

				array_push($regex, $keyword);
			}

			array_push($regex, $this->params['url']['severity']);
			$constraint = "level REGEXP '^(".implode('|', $regex).")\$'";
			array_push($constraints, $constraint);
		} else
			$this->request->data['Logline']['severity'] = 'debug';

		// Search filter Date From
		if(!empty($this->params['url']['datefrom'])
			&& !empty($this->params['url']['datefrom']['day'])
			&& !empty($this->params['url']['datefrom']['month'])
			&& !empty($this->params['url']['datefrom']['year'])
			&& !empty($this->params['url']['datefrom']['hour'])
			&& !empty($this->params['url']['datefrom']['min'])) {

			array_push($constraints, sprintf("datetime >= '%d-%d-%d %d:%d:0'",
				$this->params['url']['datefrom']['year'],
				$this->params['url']['datefrom']['month'],
				$this->params['url']['datefrom']['day'],
				$this->params['url']['datefrom']['hour'],
				$this->params['url']['datefrom']['min']));
		} else
			$this->request->data['Logline']['datefrom'] = array(
				'year' => '2010',
				'month' => '01',
				'day' => '01',
				'hour' => '00',
				'min' => '00'
			);

		// Search filter Date To
		if(!empty($this->params['url']['dateto'])
			&& !empty($this->params['url']['dateto']['day'])
			&& !empty($this->params['url']['dateto']['month'])
			&& !empty($this->params['url']['dateto']['year'])
			&& !empty($this->params['url']['dateto']['hour'])
			&& !empty($this->params['url']['dateto']['min'])) {

			array_push($constraints, sprintf("datetime <= '%d-%d-%d %d:%d:0'",
				$this->params['url']['dateto']['year'],
				$this->params['url']['dateto']['month'],
				$this->params['url']['dateto']['day'],
				$this->params['url']['dateto']['hour'],
				$this->params['url']['dateto']['min']));
		} else
			$this->request->data['Logline']['dateto'] = array(
				'year' => '2012',
				'month' => '12',
				'day' => '31',
				'hour' => '23',
				'min' => '59'
			);

		// Search filter Message
		if(!empty($this->params['url']['message']))
			array_push($constraints, "msg REGEXP '{$this->params['url']['message']}'");

		// View configuration
		$anyline = $this->Logline->find('first'); // Pas moyen de trouver un getColumns...
		$this->set('loglines', $this->paginate('Logline', $constraints, !empty($anyline['Logline']) ? array_keys($anyline['Logline']) : array()));
		$this->set('sortIcons', array('asc' => 'icon-chevron-down', 'desc' => 'icon-chevron-up'));
		$this->set('severities', $severities);
	}

	public function view($id = null) {
		$this->Logline->id = $id;
		$this->set('contraints', $constraints);
		$this->set('loglines', $this->Logline->read());
	}
}

?>
