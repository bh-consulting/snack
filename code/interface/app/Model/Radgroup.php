<?

App::uses('AuthComponent', 'Controller/Component');
App::uses('Utils', 'Lib');

class Radgroup extends AppModel
{
    public $useTable = 'radgroup';
    public $primaryKey = 'id';
    public $displayField = 'groupname';
    public $name = 'Radgroup';

    public $validationDomain = 'validation';
    public $validate = array(
        'groupname' => array(
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Groupname already used'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Groupname cannot be empty',
                'allowEmpty' => false
            )
        ),
    );
}

?>
