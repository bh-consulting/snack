<?php
App::uses('Utils', 'Lib');
App::import('Model', 'Radusergroup');

class Radgroup extends AppModel {

    public $useTable = 'radgroup';
    public $primaryKey = 'id';
    public $displayField = 'groupname';
    public $name = 'Radgroup';
    public $actsAs = array('Validation');

    public $validationDomain = 'validation';
    public $validate = array(
        'groupname' => array(
            'isUnique' => array(
                'rule' => array('isUnique', 'groupname'),
                'message' => 'Groupname already used'
            ),
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'Groupname cannot be empty',
                'allowEmpty' => false,
                'required' => true,
            )
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

    public function __construct($id = false, $table = null, $ds = null) {
        $this->virtualFields['membercount'] = Utils::generateQuery(
            new Radusergroup(),
            array(
                'fields' => array('COUNT(*)'),
                'conditions' => array('Radusergroup.groupname = Radgroup.groupname'),
            )
        );

        parent::__construct($id, $table, $ds);
    }
}
?>
