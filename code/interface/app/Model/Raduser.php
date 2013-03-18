<?php

App::uses('Utils', 'Lib');

class Raduser extends AppModel {
    public $useTable = 'raduser';
    public $primaryKey = 'id';
    public $displayField = 'username';
    public $name = 'Raduser';
    public $actsAs = array('Validation');

    public $roles = array();
    public $virtualFields = array('mac' => 'username');

    public $validationDomain = 'validation';
    
    public $validate = array(
        'username' => array(
            'isUnique' => array(
                'rule' => array('isUniqueValue', 'username', 'user'),
                'message' => 'Username already used',
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Username cannot be empty',
                'allowEmpty' => false,
                'required' => true,
                'on' => 'create',
            ),
        ),
        'passwd' => array(
            'rule' => array('notEmptyIfCiscoOrLoginpass', 'was_cisco'),
            'message' => 'You have to type a password',
        ),
        'confirm_password' => array(
            'rule' => array('equalValues', 'passwd'),
    	    'message' =>
        		'Please re-enter your password twice so that the values match'
        ),
        'mac' => array(
            'macFormat' => array(
                'rule' => 'isMACFormat',
                'message' => 'This is not a MAC address format.',
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'You have to type a MAC address',
                'allowEmpty' => false,
            ),
            'isUnique' => array(
                'rule' => array('isUniqueValue', 'username', 'mac'),
                'message' => 'MAC already used',
            ),
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

    /**
     * Custom constructor.
     * Initializa $roles array.
     */
    public function __construct($id = false, $table = null, $ds = null) {
        $this->roles = array(
            'user' => __('User'),
            'tech' => __('Tech'),
            'admin' => __('Admin'),
            'root' => __('Root'),
        );

        parent::__construct($id, $table, $ds);
    }

    /*****************************
     * Authentication functions. *
     *****************************/

    /*
     * Check if user has 'user rights'.
     * Every user, tech, admin and root have these rights.
     */
    public function isUser($id){
        $role = $this->getRole($id);
        return $role == 'user'
            || $role == 'tech'
            || $role == 'admin'
            || $role == 'root';
    }

    /*
     * Check if user has 'tech rights'.
     * Every tech, admin and root have these rights.
     */
    public function isTech($id){
        $role = $this->getRole($id);
        return $role == 'tech'
            || $role == 'admin'
            || $role == 'root';
    }

    /*
     * Check if user has 'admin rights'.
     * Only admin and root have these rights.
     */
    public function isAdmin($id){
        $role = $this->getRole($id);
        return $role == 'admin'
            || $role == 'root';
    }

    /*
     * Check if user has 'root rights'.
     * Only root have these rights.
     */
    public function isRoot($id){
        return $this->getRole($id) == 'root';
    }

    /*
     * Return the role of the specified user.
     */
    public function getRole($id) {
        $user = $this->findById($id);
        return $user['Raduser']['role'];
    }

    /********************
     * Model functions. *
     ********************/

    /**
     * Delete password field if it's empty before saving data.
     * Avoid override current password with empty password.
     */
    public function beforeSave($options = array()) {
        if (empty($this->data[$this->name]['passwd'])) {
            unset($this->data[$this->name]['passwd']);
        }

        return true;
    }
}
?>
