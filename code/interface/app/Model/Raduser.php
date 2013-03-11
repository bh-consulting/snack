<?php

App::uses('Utils', 'Lib');

class Raduser extends AppModel {
    public $useTable = 'raduser';
    public $primaryKey = 'id';
    public $displayField = 'username';
    public $name = 'Raduser';

    public $validationDomain = 'validation';
    
    public $validate = array(
        'username' => array(
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Username already used'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Username cannot be empty',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
        'passwd' => array(
            'rule' => array('notEmptyIfCiscoOrLoginpass', 'was_cisco'),
            'message' => 'You have to type a password',
        ),
        'confirm_password' => array(
            'rule' => array('identicalFieldValues', 'passwd'),
    	    'message' =>
        		'Please re-enter your password twice so that the values match'
        ),
        'mac' => array(
            'macFormat' => array(
                'rule' => 'isMACFormat',
                'message' => 'This is not a MAC address format.'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'You have to type a MAC address',
                'allowEmpty' => false,
            ),
            'isUnique' => array(
                'rule' => 'isUniqueMAC',
                'message' => 'MAC already used',
            ),
        ),
        'mac_active' => array(
            'macFormat' => array(
                'rule' => 'isMACFormat',
                'message' => 'This is not a MAC address format.'
            )
        ),
        'country' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'You have to specify a country.',
                'allowEmpty' => false,
            ),
    	),
        'province' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'You have to specify a state or province.',
                'allowEmpty' => false,
            ),
    	),
        'locality' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'You have to specify a locality.',
                'allowEmpty' => false,
            ),
    	),
        'organization' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'You have to specify an organization.',
                'allowEmpty' => false,
            ),
    	),
        'simultaneous_use' => array(
            'rule' => 'decimal',
            'message' => 'Simultaneous uses has to be a number.',
            'allowEmpty' => true,
        ),
        'tunnel-private-group-id' => array(
            'rule' => 'decimal',
            'message' => 'VLAN number has to be a number.',
            'allowEmpty' => true,
        ),
        'session-timeout' => array(
            'rule' => 'decimal',
            'message' => 'Session timeout has to be a number.',
            'allowEmpty' => true,
        ),
    );

    public function isUser($id){
        $user = $this->findById($id);
        $role = $user['Raduser']['role'];
        return $role == 'user'
            || $role == 'tech'
            || $role == 'admin'
            || $role == 'superadmin';
    }

    public function isTech($id){
        $user = $this->findById($id);
        $role = $user['Raduser']['role'];
        return $role == 'tech'
            || $role == 'admin'
            || $role == 'superadmin';
    }

    public function isAdmin($id){
        $user = $this->findById($id);
        $role = $user['Raduser']['role'];
        return $role == 'admin'
            || $role == 'superadmin';
    }

    public function isSuperAdmin($id){
        $user = $this->findById($id);
        $role = $user['Raduser']['role'];
        return $role == 'superadmin';
    }

    public function getRole($id) {
        $user = $this->findById($id);
        return $user['Raduser']['role'];
    }

    public function identicalFieldValues($field=array(), $compare_field=null) {
        foreach ($field as $key => $value) { 
            if (!isset($this->data[$this->name][$compare_field])) {
                continue;
            }

            $v1 = $value; 
            $v2 = $this->data[$this->name][$compare_field];
            if ($v1 !== $v2) { 
                return false; 
            }
        } 
        return true; 
    } 

    public function isMACFormat($field=array()) {
        $value = array_shift($field);
        if (!Utils::isMAC($value) && !empty($value)) { 
            return false; 
        }
        return true; 
    }

    public function isUniqueMAC($field=array()) {
        $value = array_shift($field);
        $value = str_replace(':', '', $value);
        $value = str_replace('-', '', $value);
	if ($this->find('count', array(
		'conditions' => array('username' => $value)
	    )
	)) {
            return false;
        }
        return true;
    }

    public function notEmptyIfCiscoOrLoginpass($field=array(), $was_cisco=null) {
        $value = array_shift($field);
        if(isset($this->data[$this->name][$was_cisco])){
            $was_cisco = $this->data[$this->name][$was_cisco];
        } else {
            $was_cisco = false;
        }

        // NEW raduser (no id set)
        if(!isset($this->data[$this->name]['id'])){
            if (empty($value) 
                && ($this->data[$this->name]['is_cisco'] == 1
                || $this->data[$this->name]['is_loginpass'] == 1
                || $this->data[$this->name]['role'] > 'user')
            ) {
                return false;
            }
        // raduser UPDATE (id isset)
        } else {
            if(isset($this->data[$this->name]['is_cisco'])){
                if(empty($value)
                    && !$was_cisco
                    && $this->data[$this->name]['is_cisco']
                    && !(isset($this->data[$this->name]['is_loginpass'])
                        && $this->data[$this->name]['is_loginpass'])
                ){
                    return false;
                }
            }
        }
        return true;
    }

    public function beforeSave($options = array()) {
        if (empty($this->data[$this->name]['passwd'])) {
            unset($this->data[$this->name]['passwd']);
        }
        return true;
    }
}

?>
