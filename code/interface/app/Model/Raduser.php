<?

App::uses('AuthComponent', 'Controller/Component');
class Raduser extends AppModel
{
    public $useTable = 'raduser';
    public $primaryKey = 'id';
    public $displayField = 'username';
    public $name = 'Raduser';

    public $validate = array(
        'username' => array(
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Username already used'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Username cannot be empty',
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
