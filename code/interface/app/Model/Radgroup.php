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
}

?>
