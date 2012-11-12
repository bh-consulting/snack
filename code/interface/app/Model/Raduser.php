<?

class Raduser extends AppModel
{
    public $useTable = 'raduser';
    public $primaryKey = 'username';
    public $displayField = 'username';
    public $name = 'Raduser';

    // association to Radcheck
    public $hasMany = array(
        'Radcheck' => array(
            'className' => 'Radcheck',
            'dependent' => true,
            'foreignKey' => 'username'
        )
    );

    // validation rules
    public $validate = array(
        'username' => array(
            'rule' => 'alphaNumeric',
            'message' => 'Usernames can only contains letters and numbers, not empty username.',
            'allowEmpty' => false
        )
    );
}

?>
