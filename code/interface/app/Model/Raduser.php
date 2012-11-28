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
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'You have to type a password',
                'allowEmpty' => false
            ),
            'identicalFieldValues' => array(
                'rule' => array('identicalFieldValues', 'confirm_password'),
                'message' => 'Please re-enter your password twice so that the values match'
            )
        ),
        'mac' => array(
            'macFormat' => array(
                'rule' => array('isMACFormat'),
                'message' => 'This is not a MAC address format'
            )
        )
    );

    function identicalFieldValues( $field=array(), $compare_field=null )  
    { 
        foreach( $field as $key => $value ){ 
            $v1 = $value; 
            $v2 = $this->data[$this->name][ $compare_field ];                  
            if($v1 !== $v2) { 
                return false; 
            } else { 
                continue; 
            } 
        } 
        return true; 
    } 

    public function isMACFormat($field=array()) {
        foreach( $field as $key => $value ){ 
            $v1 = $value; 
            if(!preg_match('/^(?:[[:xdigit:]]{2}([-:]?))(?:[[:xdigit:]]{2}\1){4}[[:xdigit:]]{2}$/', $v1)) { 
                return false; 
            } else { 
                continue; 
            } 
        } 
        return true; 
    }

    public function beforeValidate($options = array()){
        if(empty($this->data['Raduser']['password']))
            unset($this->data['Raduser']['password']);
    }
}

?>
