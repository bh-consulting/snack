<?php
App::uses('AppModel', 'Model');

class Snackuser extends AppModel {
    public $useTable = 'snackuser';
    public $roles = array();
    public $name = 'Snackuser';
    public $actsAs = array('Validation');
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
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'You have to type a password'
            )
        ),
        'confirm_password' => array(
            'rule' => array('equalValues', 'password'),
            'message' =>
                'Please re-enter your password twice so that the values match'
        ),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('root', 'admin', 'tech')),
                'message' => 'Merci de rentrer un rôle valide',
                'allowEmpty' => false
            )
        )
    );

    /**
     * Custom constructor.
     * Initializa $roles array.
     */
    public function __construct($id = false, $table = null, $ds = null) {
        $this->roles = array(
            'tech' => 'Tech',
            'admin' => 'Admin',
            'root' => 'Root',
        );

        parent::__construct($id, $table, $ds);
    }

    /*****************************
     * Authentication functions. *
     *****************************/

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
        return $user['Snackuser']['role'];
    }

}
?>