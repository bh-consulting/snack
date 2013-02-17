<?php
App::uses('Sanitize', 'Utility');

class FiltersComponent extends Component {
	private $controller, $modelName;
	private $constraints = array();

	public function __construct($collection, $params) {
		$this->controller = $collection->getController();
		$this->modelName = $params['model'];

        if (!isset($this->controller->request->data[$this->modelName])) {
            $this->controller->request->data[$this->modelName] = array();
        }

        $this->controller->request->data[$this->modelName] = array_merge(
            $this->controller->request->data[$this->modelName],
            $this->controller->params['url']
        );

		if ($this->controller->{$this->modelName}->validates()) {
			$this->controller->set('filtersPanOpen', false);
        } else {
			$this->controller->set('filtersPanOpen', true);
        }
	}

    private function addConstraint($template, $fields, $value) {
        if (!empty($fields) && !empty($value)) {
            if (is_array($fields)) {
                $constraint = "(1=0";
                foreach ($fields as $field) {
                    if (!empty($field)) {
                        $atom = str_replace(
                            "%field",
                            $field,
                            $template
                        );
                        $atom = str_replace(
                            "%value",
                            Sanitize::escape($value),
                            $atom
                        );

                        $constraint .= " OR " . $atom;
                    }
                }
                $constraint .= ")";
            } else {
                $atom = str_replace(
                    "%field",
                    $fields,
                    $template
                );
                $atom = str_replace(
                    "%value",
                    Sanitize::escape($value),
                    $atom
                );
                $constraint = "(" . $atom . ")";
            }

            if (!empty($constraint) && $constraint != "(1=0)") {
                array_push(
                    $this->constraints,
                    $constraint
                );
            }
        }
    }

    public function addSelectConstraint($options) {
        $data =  $this->controller->request->data[$this->modelName];
        $title = empty($options['title']) ? __('Select an option..')
            : $options['title'];
        $items = array();
        $emptyValue = isset($options['empty']) ? $options['empty'] : false;

        if (!empty($options['input']) && !empty($options['fields'])) {
            if (isset($options['default'])
                && empty($this->controller->params['url'][$options['input']])
            ) {
                $data[$options['input']] = $options['default'];
            }

            $items[''] = $title;

            if (!empty($options['options'])) {
                $items = array_merge($items, $options['options']);
            }

            if (!empty($options['data'])) {
                foreach ((array)$options['data'] as $field) {
                    $items = array_merge($items, $this->controller
                        ->{$this->modelName}->find(
                            'list',
                            array(
                                'order' => $field . ' ASC',
                                'fields' => array($field, $field),
                                'group' => $field
                            )
                        )
                    );
                }
            }

            $this->controller->set($options['input'] . 's', $items);

            if (!$emptyValue) {
                $items_tmp = array();

                foreach ($items as $key => $item) {
                    if (!empty($item)) {
                        $items_tmp[$key] = $item;
                    }
                }

                $items = $items_tmp;
            }

            $this->addStringConstraint(array(
                'fields' => $options['fields'],
                'input' => $options['input'],
            ));
        }
    }

	public function addSliderConstraint($options) {
        $order = isset($options['order']) ? $options['order'] : 'desc';
        if (!empty($options['input'])
            && !empty($options['options'])
            && !empty($options['fields'])
        ) {
            if (isset($options['default'])
                && empty($this->controller->params['url'][$options['input']])
            ) {
                $this->controller->request
                    ->data[$this->modelName][$options['input']]
                    = $options['default'];
                $selectedValue = $options['default'];
            } else {
                $selectedValue = $this->controller
                    ->params['url'][$options['input']];
            }

            $regex = array();

            if ($order == 'desc') {
                $values = array_reverse($options['options']);
            } else {
                $values = $options['options'];
            }

            foreach (array_keys($values) as $value) {
                array_push($regex, Sanitize::escape($value));

                if($value == $selectedValue) {
                    break;
                }
            }

            $this->controller->set(
                $options['input'] . 's',
                $options['options']
            );

            $this->addConstraint(
                "%field REGEXP '^(%value)\$'",
                $options['fields'],
                implode('|', $regex)
            );
        }
	}

	public function addDatesConstraint($options) {
        $patternDate = '/^(?<day>0[1-9]|[1-2]\d|3[01])\/'
            . '(?<mon>0[1-9]|1[12])\/'
            . '(?<year>20\d{2})\s+'
            . '(?<hour>[01]\d|2[0-3]):(?<min>[0-5]\d):(?<sec>[0-5]\d)$/';

        if (!empty($options['fields'])) {
            if (!empty($options['from'])
                && !empty($this->controller->params['url'][$options['from']])
                && preg_match(
                    $patternDate,
                    $this->controller->params['url'][$options['from']],
                    $date
                )
            ) {
                $template = "%field >= '%value'";

                $this->addConstraint(
                    $template,
                    $options['fields'], 
                    sprintf(
                        "%d-%d-%d %d:%d:0",
                        $date['year'],
                        $date['mon'],
                        $date['day'],
                        $date['hour'],
                        $date['min'],
                        $date['sec']
                    )
                );
            }

            if (!empty($options['to'])
                && !empty($this->controller->params['url'][$options['to']])
                && preg_match(
                    $patternDate,
                    $this->controller->params['url'][$options['to']],
                    $date
                )
            ) {
                $template = "%field <= '%value'";

                $this->addConstraint(
                    $template,
                    $options['fields'], 
                    sprintf(
                        "%d-%d-%d %d:%d:0",
                        $date['year'],
                        $date['mon'],
                        $date['day'],
                        $date['hour'],
                        $date['min'],
                        $date['sec']
                    )
                );
            }
        }
	}

    public function addStringConstraint($options) {
        if (!empty($options['fields'])
            && !empty($options['input'])
            && !empty($this->controller->params['url'][$options['input']])
        ) {
            $template = "%field REGEXP '%value'";

            $this->addConstraint(
                $template,
                $options['fields'],
                $this->controller->params['url'][$options['input']]
            );
        }

        if (!empty($options['ahead'])) {
            $aheadData = array();

            foreach ((array) $options['ahead'] as $field) {
                $aheadData = array_merge($aheadData, $this->controller
                    ->{$this->modelName}->find(
                        'list',
                        array(
                            'fields' => $field,
                            'group' => $field,
                        )
                    )
                );
            }

            $this->controller->set($options['input'] . 'Data', $aheadData);
        }
    }

	public function getConstraints() {
		return $this->constraints;
	}

    public function paginate($dataTitle = null) {
        $dataTitle = is_null($dataTitle) ? strtolower($this->modelName).'s'
            : $dataTitle;

        $this->controller->set(
            $dataTitle,
            $this->controller->paginate(
                $this->modelName,
                $this->constraints
            )
        );
        $this->controller->set(
            'sortIcons',
            array(
                'asc' => 'icon-chevron-down',
                'desc' => 'icon-chevron-up'
            )
        );
	}
}

?>
