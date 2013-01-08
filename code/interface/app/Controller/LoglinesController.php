<?php

App::uses('Sanitize', 'Utility');

class LoglinesController extends AppController {
	public $helpers = array('Html', 'Form');
	public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));

	public function index() {
		$constraints = array();
		$patternDate = '/^(?<day>0[1-9]|[1-2]\d|3[01])\/(?<mon>0[1-9]|1[12])\/(?<year>20\d{2})\s+(?<hour>[01]\d|2[0-3]):(?<min>[0-5]\d):(?<sec>[0-5]\d)$/';
		$this->request->data['Logline'] = $this->params['url'];

		$severities = array(
			'debug' => 'Debug',
			'info' => 'Info',
			'notice' => 'Notice',
			'warn' => 'Warning',
			'err' => 'Error',
			'crit' => 'Critical',
			'alert' => 'Alert',
			'emerg' => 'Emergency'
		);

		// Search filter Severity
		{
			$valSeverity = null;

			if(empty($this->params['url']['severity'])) {
				$valSeverity = 'info';
				$this->request->data['Logline']['severity'] = 'info';
			} else
				$valSeverity = $this->params['url']['severity'];

			$regex = array();

			foreach(array_reverse($severities) as $keyword => $severity) {
				if($keyword == $valSeverity)
					break;

				array_push($regex, $keyword);
			}

			array_push($regex, Sanitize::escape($valSeverity));
			$constraint = "level REGEXP '^(".implode('|', $regex).")\$'";

			array_push($constraints, $constraint);
		}

		// Search filter Date From
		if(!empty($this->params['url']['datefrom']) && preg_match($patternDate, $this->params['url']['datefrom'], $date))
			array_push($constraints, sprintf("datetime >= '%d-%d-%d %d:%d:0'",
				$date['year'],
				$date['mon'],
				$date['day'],
				$date['hour'],
				$date['min'],
				$date['sec']));
		else
			$this->request->data['Logline']['datefrom'] = null;

		// Search filter Date To
		if(!empty($this->params['url']['dateto']) && preg_match($patternDate, $this->params['url']['dateto'], $date))
			array_push($constraints, sprintf("datetime <= '%d-%d-%d %d:%d:0'",
				$date['year'],
				$date['mon'],
				$date['day'],
				$date['hour'],
				$date['min'],
				$date['sec']));
		else
			$this->request->data['Logline']['dateto'] = null;

		// Search filter Message
		if(!empty($this->params['url']['message']))
			array_push($constraints, "msg REGEXP '".Sanitize::escape($this->params['url']['message'])."'");

		// View configuration
		$columnNames = array_keys($this->Logline->schema());
		$this->set('loglines', $this->paginate('Logline', $constraints, $columnNames));
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
