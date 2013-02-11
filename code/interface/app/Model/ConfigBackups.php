<?php

App::uses('Utils', 'Lib');

class ConfigBackup extends AppModel {
    public $useTable = false;

    public $_schema = array(
        'datetime' => array(
            'type' => 'string',
            'null' => false,
            'default' => '',
            'length' => '255',
        ),
        'author' => array(
            'type' => 'string',
            'null' => false,
            'default' => '',
            'length' => '255',
        ),
        'commit' => array(
            'type' => 'string',
            'null' => false,
            'default' => '',
            'length' => '255',
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

