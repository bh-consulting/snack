<?php
App::uses('Sanitize', 'Utility');

class FiltersComponent extends Component {
    private $controller, $modelName;
    private $constraints = array();
    private $groups = array();

    public function __construct($collection, $params) {
        $this->controller = $collection->getController();
        $this->modelName = isset($params['model'])
            ? $params['model'] : substr($this->controller->name, 0, -1);
        $this->actions = isset($params['actions'])
            ? (array)$params['actions'] : array('index');

        if (in_array($this->controller->request->action, $this->actions)) {
            if (!isset($this->controller->request->data[$this->modelName])) {
                $this->controller->request->data[$this->modelName] = array();
            }

            $this->controller->request->data[$this->modelName] = array_merge(
                $this->controller->request->data[$this->modelName],
                $this->controller->params['url']
            );

            if (in_array('beforeValidateForFilters', $this->controller->methods)) {
                $this->controller->beforeValidateForFilters();
            }

            if ($this->controller->{$this->modelName}->validates()) {
                $this->controller->set('filtersPanOpen', false);
            } else {
                $this->controller->set('filtersPanOpen', true);
            }
        }
    }

    private function addConstraint($template, $fields, $value) {
        if (isset($fields) && isset($value)) {
            $constraint = "(1 = 0";
            foreach ((array)$fields as $field) {
                if (!empty($field) && !is_array($value) && !empty($value)) {
                    $atom = preg_replace(
                        array("/%field/", "/%value/"),
                        array($field, Sanitize::escape($value)),
                        $template
                    );

                    $constraint .= " OR " . $atom;
                }
            }
            $constraint .= ")";

            if (!empty($constraint) && $constraint != "(1 = 0)") {
                $this->constraints[] = $constraint;
            }
        }
    }

    public function addComplexConstraint($options) {
        foreach ($options as $action=>$params) {
            switch ($action) {
            case 'select':
                $this->addSelectConstraint($params);
                break;
            case 'boolean':
                $this->addBooleanConstraint($params);
                break;
            case 'complex':
                $this->addComplexConstraint($params);
                break;
            case 'slider':
                $this->addSliderConstraint($params);
                break;
            case 'string':
                $this->addStringConstraint($params);
                break;
            case 'callback':
                if (!empty($params[0])
                    && !is_array($params[0])
                    && isset($params[1])
                ) {
                    $this->constraints[] = $this->controller->$params[0]($params[1]);
                }
                break;
            }
        }
    }

    public function addBooleanConstraint($options) {
        if (isset($options['input']) && !is_array($options['input'])) {
            $data = &$this->controller->request
                ->data[$this->modelName][$options['input']];
            $url = $this->controller->params['url'];

            // Set the list of choices.
            if (isset($options['items'])) {
                $this->controller->set(
                    $options['input'] . 's',
                    $options['items']
                );
            }

            // Set default value.
            if (isset($options['default'])
                && !isset($url[$options['input']])
            ) {
                foreach ((array)$options['default'] as $default) {
                    $data[] = $default;
                }
            }

            // Filter results.
            if (!empty($options['fields'])
                && isset($data)
            ) {
                $selected = array();

                foreach ((array)$options['fields'] as $field) {
                    if (!is_array($field) && in_array($field, (array)$data)) {
                        $selected[] = $field;
                    }
                }

                $this->addStringConstraint(array(
                    'fields' => $selected,
                    'input' => $options['input'],
                    'value' => '1',
                    'strict' => true,
                ));
            }
        }
    }

    public function addSelectConstraint($options) {
        if (isset($options['input']) && !is_array($options['input'])) {
            $data =  &$this->controller->request
                ->data[$this->modelName][$options['input']];
            $url = $this->controller->params['url'];

            // Set list of choices.
            $items = array();
            $emptyValue = isset($options['empty']) ? $options['empty'] : false;

            if (isset($options['title']) && !is_array($options['title'])) {
                $title = $options['title'];
            } else {
                $title = __('Select an option..');
            }

            if ($title) {
                $items[''] = $title;
            }

            if (isset($options['items'])) {
                $items = array_merge($items, (array)$options['items']);
            }

            if (isset($options['data'])) {
                //TODO: optimize, do one request and clear table after (unique)
                foreach ((array)$options['data'] as $field) {
                    $itemsData = $this->controller->{$this->modelName}->find(
                        'list',
                        array(
                            'order' => $field . ' ASC',
                            'fields' => array($field, $field),
                            'group' => $field
                        )
                    );

                    if (isset($options['translate'])) {
                        foreach ($itemsData as $text=>&$value) {
                            if (isset($options['translate'][$text])) {
                                $value = $options['translate'][$text];
                            }
                        }
                    }

                    $items = array_merge($items, $itemsData);
                }
            }

            if (!$emptyValue) {
                foreach ($items as $key => $item) {
                    if (empty($item)) {
                        unset($items[$key]);
                    }
                }
            }

            $this->controller->set($options['input'] . 's', $items);

            // Set default value.
            $strict = isset($options['strict']) ? $options['strict'] : true;
            if (isset($options['default'])
                && !isset($url[$options['input']])
            ) {
                if (!$strict) {
                    foreach ((array)$options['default'] as $default) {
                        $data[] = $default;
                    }
                } else {
                    $default = (array)$options['default'];
                    $data = array_shift($default);
                }
            }

            // Filter results.
            if (!empty($options['fields'])) {
                $this->addStringConstraint(array(
                    'fields' => $options['fields'],
                    'input' => $options['input'],
                    'strict' => $strict,
                ));
            }
        }
    }

    public function addSliderConstraint($options) {
        if (isset($options['input']) && !is_array($options['input'])) {
            $data =  &$this->controller->request
                ->data[$this->modelName][$options['input']];
            $url = $this->controller->params['url'];

            $order = isset($options['order']) ? $options['order'] : 'desc';

            // Set list of choices.
            if (isset($options['items'])) {
                if ($order == 'desc') {
                    $values = array_reverse($options['items']);
                } else {
                    $values = $options['items'];
                }

                $this->controller->set(
                    $options['input'] . 's',
                    (array)$options['items']
                );
            } else {
                $values = array();
            }

            // Set default value.
            if (isset($options['default'])
                && !isset($url[$options['input']])
            ) {
                $data = $options['default'];
                $selectedValue = $options['default'];
            } else {
                $selectedValue = $url[$options['input']];
            }

            // Filter results.
            $regex = array();

            foreach (array_keys($values) as $value) {
                $regex[] = $value;

                if($value == $selectedValue) {
                    break;
                }
            }

            $this->addConstraint(
                "%field REGEXP '^(%value)\$'",
                $options['fields'],
                implode($regex, '|')
            );
        }
    }

    public function addDatesConstraint($options) {
        $url = $this->controller->params['url'];
        $patternDate = '/^(?<year>20\d{2})-'
            . '(?<mon>0[1-9]|1[12])-'
            . '(?<day>0[1-9]|[1-2]\d|3[01])\s+'
            . '(?<hour>[01]\d|2[0-3]):(?<min>[0-5]\d):(?<sec>[0-5]\d)$/';

        if (!empty($options['fields'])) {
            if (isset($options['from'])
                && isset($url[$options['from']])
                && preg_match(
                    $patternDate,
                    $url[$options['from']],
                    $date
                )
            ) {
                $this->addConstraint(
                    "%field >= '%value'",
                    $options['fields'], 
                    sprintf(
                        "%d-%d-%d %d:%d:%d",
                        $date['year'],
                        $date['mon'],
                        $date['day'],
                        $date['hour'],
                        $date['min'],
                        $date['sec']
                    )
                );
            }

            if (isset($options['to'])
                && isset($url[$options['to']])
                && preg_match(
                    $patternDate,
                    $url[$options['to']],
                    $date
                )
            ) {
                $this->addConstraint(
                    "%field <= '%value'",
                    $options['fields'], 
                    sprintf(
                        "%d-%d-%d %d:%d:%d",
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
        if (isset($options['input']) && !is_array($options['input'])) {
            $data = &$this->controller->request
                ->data[$this->modelName][$options['input']];
            $url = $this->controller->params['url'];

            // Set ahead data.
            if (isset($options['ahead'])) {

                $aheadData = $this->controller->{$this->modelName}->find(
                    'customList',
                    array(
                        'fields' => $options['ahead'],
                        'limit' => 100
                    )
                );

                $this->controller->set($options['input'] . 'Data', $aheadData);
            }

            // Set default value.
            if (isset($options['default'])
                && !isset($url[$options['input']])
            ) {
                $data = $options['default'];
            }

            // Filter results.
            if (!empty($options['fields'])) {
                if (isset($options['value'])) {
                    $value = $options['value'];
                } else if (isset($data)) {
                    $value = implode((array)$data, '|');
                }

                if (isset($value)) {
                    if (isset($options['strict']) && $options['strict']) {
                        $template = "%field REGEXP '^(%value)\$'";
                    } else {
                        $template = "%field REGEXP '%value'";
                    }

                    $this->addConstraint(
                        $template,
                        $options['fields'],
                        $value
                    );
                }
            }
        }

    }

    public function addGroupConstraint($groups = array()) {
        if (!empty($groups)) {
            $this->group = array_merge($this->groups, (array)$groups);
        }
    }

    public function getConstraints() {
        return $this->constraints;
    }

    public function paginate($dataTitle = null) {
        // Add 'group by' conditions.
        if (isset($this->controller->paginate['group'])) {
            $this->groups = array_merge(
                $this->controller->paginate['group'],
                $this->groups
            );
        }

        $this->controller->paginate['group'] = $this->groups;

        // Paginate whith filters conditions.
        $data = $this->controller->paginate(
            $this->modelName,
            $this->constraints
        );

        // Set view data.
        $dataTitle = is_null($dataTitle) ? strtolower($this->modelName).'s'
            : $dataTitle;

        $this->controller->set(
            $dataTitle,
            $data
        );

        return $data;
    }
}

?>
