<?

App::uses('AuthComponent', 'Controller/Component');
class Raduser extends AppModel
{
    public $useTable = 'raduser';
    public $primaryKey = 'id';
    public $displayField = 'username';
    public $name = 'Raduser';

    // association to Radcheck
    /* public $hasMany = array(
        'Radcheck' => array(
            'className' => 'Radcheck',
            'dependent' => true,
            'foreignKey' => false,
            'conditions' => array('Radcheck.username' => '$this->username')
        )
    ); */

    // validation rules
    public $validate = array(
        'username' => array(
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Username already used',
                'allowEmpty' => false
            )
        ),
        'password' => array(
            'rule' => 'notEmpty',
            'message' => 'You have to type a password',
            'allowEmpty' => false
        )
    );
}

?>
