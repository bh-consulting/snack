<?php

class FiltersComponent extends Component {
	private $controller, $modelName;
	private $constraints = array();

	public function __construct($collection, $params) {
		$this->controller = $collection->getController();
		$this->modelName = $params['model'];

		$this->controller->request->data[$this->modelName] = $this->controller->params['url'];
		$this->controller->{$this->modelName}->set($this->controller->request->data);

		if($this->controller->{$this->modelName}->validates())
			$this->controller->set('filtersPanOpen', false);
		else
			$this->controller->set('filtersPanOpen', true);

	}

	public function addListConstraint($options) {
		$val = null;

		if(empty($this->controller->params['url'][$options['column']])) {
			$val = $options['default'];
			$this->controller->request->data[$this->modelName][$options['column']] = $options['default'];
		} else
			$val = $this->controller->params['url'][$options['column']];

		$regex = array();

		foreach(array_keys($options['list']) as $keyword) {
			if($keyword == $val)
				break;

			array_push($regex, $keyword);
		}

		array_push($regex, Sanitize::escape($val));
		$constraint = $options['column']." REGEXP '^(".implode('|', $regex).")\$'";

		array_push($this->constraints, $constraint);
	}

	public function addDatesConstraint($options) {
		$patternDate = '/^(?<day>0[1-9]|[1-2]\d|3[01])\/(?<mon>0[1-9]|1[12])\/(?<year>20\d{2})\s+(?<hour>[01]\d|2[0-3]):(?<min>[0-5]\d):(?<sec>[0-5]\d)$/';

		if(!empty($this->controller->params['url'][$options['from']]) && preg_match($patternDate, $this->controller->params['url'][$options['from']], $date))
			array_push($this->constraints, sprintf($options['column']." >= '%d-%d-%d %d:%d:0'",
				$date['year'],
				$date['mon'],
				$date['day'],
				$date['hour'],
				$date['min'],
				$date['sec']));
		else
			$this->controller->request->data[$this->modelName][$options['from']] = null;

		if(!empty($this->controller->params['url'][$options['to']]) && preg_match($patternDate, $this->controller->params['url'][$options['to']], $date))
			array_push($this->constraints, sprintf($options['column']." <= '%d-%d-%d %d:%d:0'",
				$date['year'],
				$date['mon'],
				$date['day'],
				$date['hour'],
				$date['min'],
				$date['sec']));
		else
			$this->controller->request->data[$this->modelName][$options['to']] = null;
	}

	public function addStringConstraint($options) {
		if(!empty($this->controller->params['url'][$options['column']]))
			array_push($constraints, $options['column']." REGEXP '".Sanitize::escape($this->controller->params['url'][$options['column']])."'");
	}

	public function getConstraints() {
		return $this->constraints;
	}

	public function paginate() {
		$columnNames = array_keys($this->controller->{$this->modelName}->schema());
		$this->controller->set(strtolower($this->modelName).'s', $this->controller->paginate($this->modelName, $this->constraints, $columnNames));
		$this->controller->set('sortIcons', array('asc' => 'icon-chevron-down', 'desc' => 'icon-chevron-up'));
	}
}

?>
