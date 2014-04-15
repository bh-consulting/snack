<?php

App::uses('Utils', 'Lib');
Configure::load('parameters');

class Parameter extends AppModel {
    public $useTable = false;

    public $_schema = array(
        'configurationEmail' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'errorEmail' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'ipAddress' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '15',
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
        'paginationCount' => array(
            'type' => 'integer',
            'null' => false,
            'default' => 10,
            'length' => 5,
        ),
        'smtp_ip' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'smtp_port' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'smtp_login' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'smtp_password' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'proxy_ip' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'proxy_port' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'proxy_login' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
        'proxy_password' => array(
            'type' => 'string',
            'null' => true,
            'default' => '',
            'length' => '255',
        ),
    );

    public $validate = array(
        'configurationEmail' => array(
            'email' => array(
                'rule' => 'email',
                'message' => 'Invalid email format.',
                'required' => true
            )
        ),
        'errorEmail' => array(
            'email' => array(
                'rule' => 'email',
                'message' => 'Invalid email format.',
                'required' => true
            )
        ),
        'ipAddress' => array(
			'ipFormat' => array(
				'rule' => 'isIPFormat',
				'message' => 'This is not an IP address format.',
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
        'paginationCount' => array(
            'rule' => 'numeric',
            'message' => 'The pagination count must be a number.'
        ),
        'smtp_ip' => array(
			'ipFormat' => array(
				'rule' => 'isIPFormat',
				'message' => 'This is not an IP address format.',
                'required' => true
            )
        ),
        'smtp_port' => array(
			'rule' => 'numeric',
            'message' => 'The smtp port must be a number.'
        ),
        'proxy_ip' => array(
			'ipFormat' => array(
				'rule' => 'isIPFormat',
				'message' => 'This is not an IP address format.',
                'allowEmpty' => true,
            ),
        ),
        'proxy_port' => array(
			'rule' => 'numeric',
            'message' => 'The smtp port must be a number.',
            'allowEmpty' => true,
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
            return Configure::dump(
                'parameters.php',
                'default',
                array('Parameters')
            );
        } else {
            return false;
        }
    }

    public function dirExists($check) {
        $value = array_values($check);
        $value = $value[0];

        return is_dir($value);
    }

	public function isIPFormat($field=array()) {
		$value = array_shift($field);
		if(Utils::isIP($value)) { 
			return true; 
		}
		return false; 
	}
}
?>
