<?php

//App::uses('AuthComponent', 'Controller/Component');
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
                'rule' => 'notEmptyIfCiscoOrLoginpass',
                'message' => 'You have to type a password',
                'on' => 'create',
        ),
        'confirm_password' => array(
            'rule' => array('identicalFieldValues', 'passwd'),
            'message' => 'Please re-enter your password twice so that the values match'
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
    );

    public $virtualFields = array(
        'ntype' => 'is_mac' //TODO Revoir conception user type
    );

    public function identicalFieldValues( $field=array(), $compare_field=null )  
    { 
        foreach( $field as $key => $value ){ 
            if(!isset($this->data[$this->name][$compare_field])){
                continue;
            }

            $v1 = $value; 
            $v2 = $this->data[$this->name][ $compare_field ];
            if($v1 !== $v2) { 
                return false; 
            }
        } 
        return true; 
    } 

    public function isMACFormat($field=array()) {
        $value = array_shift($field);
        if(!Utils::isMAC($value) && !empty($value)) { 
            return false; 
        }
        return true; 
    }

    public function isUniqueMAC($field=array()) {
        $value = array_shift($field);
        $value = str_replace(':', '', $value);
        $value = str_replace('-', '', $value);
        if($this->find('count', array(
            'conditions' => array('username' => $value)
        ))){
            return false;
        }
        return true;
    }

    public function notEmptyIfCiscoOrLoginpass($field=array()) {
        $value = array_shift($field);
        $this->log('empty value:' . empty($value));
        $this->log($this->data[$this->name]['is_cisco']);
        $this->log($this->data[$this->name]['is_loginpass']);
        
        if($value == "" && ($this->data[$this->name]['is_cisco'] == 1
            || $this->data[$this->name]['is_loginpass'] == 1)
        ){
            return false;
        }
        return true;
    }

    public function beforeValidate($options = array()){
        if(empty($this->data['Raduser']['password']))
            unset($this->data['Raduser']['password']);
    }
}

?>
