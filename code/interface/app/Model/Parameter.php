<?php

App::uses('Utils', 'Lib');
Configure::load('parameters');

class Parameter extends AppModel {
    public $useTable = false;

    public $_schema = array(
        'contactEmail' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'scriptsPath' => array(
            'type' => 'string',
            'null' => false,
            'default' => '',
            'length' => '255',
        ),
        'certsPath' => array(
            'type' => 'string',
            'null' => false,
            'default' => '',
            'length' => '255',
        ),
        'countryName' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'stateOrProvinceName' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'localityName' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'organizationName' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
    );

    public $validate = array(
        'contactEmail' => array(
            'email' => array(
                'rule' => 'email',
                'message' => 'Invalid email format.',
                'required' => true
            )
        ),
        'scriptsPath' => array(
            'exists' => array(
                'rule' => 'dirExists',
                'message' => 'Directory does not exist.',
                'required' => true
            )
        ),
        'certsPath' => array(
            'exists' => array(
                'rule' => 'dirExists',
                'message' => 'Directory does not exist.',
                'required' => true
            )
        ),
    );

    public function read($fields = null, $id = null) {
        foreach ($this->schema() as $key => $label) {
            if (is_null($fields) || array_key_exists($key, $fields)) {
                $this->set($key, Configure::read('Parameters.' . $key));
            }
        }

        return $this->data;
    }

    public function set($one, $two = null){

        if (is_array($one) && isset($one['Parameter'])) {
            foreach ($one['Parameter'] as $key => $value) {
                $this->set($key, $value);
            }
        } elseif (!is_array($one)) {
            Configure::write(
                'Parameters.' . $one,
                $two
            );
        }

        parent::set($one, $two);
    }

    public function save($data = null, $validate = true, $fieldList = array()) {
        if ($this->validates()) {
            Configure::dump(
                'parameters.php',
                'default',
                array('Parameters')
            );
            return true;
        } else {
            return false;
        }
    }

    public function dirExists($check) {
        $value = array_values($check);
        $value = $value[0];

        return is_dir($value);
    }
}
?>
