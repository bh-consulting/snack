<?

class Raduser extends AppModel
{
    var $useTable = 'raduser';
    var $primaryKey = 'username';
    var $displayField = 'username';
    var $name = 'Raduser';

    // validation rules
    var $validate = array(
        'username' => array(
            'rule' => 'alphaNumeric',
            'message' => 'Usernames can only contains letters and numbers, not empty username.',
            'allowEmpty' => false
        )
    );
}

?>
