<?php

App::uses('Component', 'Controller');
App::import('Model', 'Radcheck');
App::import('Model', 'Raduser');
App::import('Model', 'Radgroupcheck');
App::import('Model', 'Radgroup');
App::import('Model', 'Radusergroup');

class ChecksComponent extends Component
{

	private $displayName;
	private $checkClass;
	private $checkClassName;
	private $baseClass;
	private $baseClassName;

	public function __construct($collection, $params){
		$this->displayName = $params['displayName'];
		$this->checkClass = new $params['checkClass'];
		$this->checkClassName = $params['checkClass'];
		$this->baseClass = new $params['baseClass'];
		$this->baseClassName = $params['baseClass'];
	}

    public function getChecks($id){
        $this->baseClass->id = $id;

        // sorry for that...
        $findAllFunc = 'findAllBy' . ucfirst($this->displayName);
        return $this->checkClass->$findAllFunc($this->baseClass->field($this->displayName));
    }

    public function getType($rad, $nice = false) {
        $types = array(
            array('cisco', 'Cisco'),
            array('loginpass', 'Login / Password'),
            array('mac', 'MAC'),
            array('cert', 'Certificate')
        );

        if($rad['is_cisco'])
            return $types[0][$nice];
        if($rad['is_loginpass'])
            return $types[1][$nice];
        if($rad['is_mac'])
            return $types[2][$nice];
        if($rad['is_cert']  )
            return $types[3][$nice];
        return "";
    }

    public function formatMAC( $mac, $delimiter = ':' )
    {
	    return ( strlen( $mac ) == 12 ) ? substr($mac, 0, 2) . $delimiter . substr($mac, 2, 2) . $delimiter . substr($mac, 4, 2) . $delimiter . substr($mac, 6, 2) . $delimiter . substr($mac, 8, 2) . $delimiter . substr($mac, 10, 2) : $mac;
    }


    public function index()
    {
        $rads = $this->baseClass->find('all');
        if($this->baseClassName == 'Raduser'){
            foreach ($rads as &$r) {
                $r[$this->baseClassName]['ntype'] = $this->getType($r[$this->baseClassName], true);
                $r[$this->baseClassName]['type'] = $this->getType($r[$this->baseClassName], false);
            }
        }
        return $rads;
    }

    public function view($id = null) {
        $this->baseClass->id = $id;
        return array(
        	'base' => $this->baseClass->read(), 
		'checks' => $this->getChecks($id),
		'groups' => $this->getUserGroups($id));
    }

    public function create_check($displayName, $attribute, $op, $value){
        if($value != "" && $attribute != ""){
            $data = array(
                $this->displayName => $displayName
                ,
                'attribute' => $attribute,
                'op' => $op,
                'value' => $value
                );
            $rad = new $this->checkClass;
            $rad->create();
            return $rad->save($data);
        } else {
            return true;
        }
    }

    public function add($request, $checks){
        if($request->is('post')){
                
            // add common checks values (expiration date and simultaneous uses) 
            $name = $request->data[$this->baseClassName][$this->displayName];
            $checks = array_merge($checks, array(
                array($name,
                    'Expiration',
                    ':=',
                    $request->data[$this->baseClassName]['expiration_date']
                    ),
                array($name,
                    'Simultaneous-Use',
                    ':=',
                    $request->data[$this->baseClassName]['simultaneous_use']
                    )
                ));

            $this->baseClass->create();
            $success = $this->baseClass->save($request->data);

            // add users or groups linked to this group or user
            if(isset($request->data[$this->baseClassName]['groups']))
                $this->addUsersOrGroups($this->baseClass->id, $request->data[$this->baseClassName]['groups']);
            else if(isset($request->data[$this->baseClassName]['users']))
                $this->addUsersOrGroups($this->baseClass->id, $request->data[$this->baseClassName]['users']);

            // create all checks
            foreach($checks as $rc)
                $success = $success && $this->create_check($rc[0], $rc[1], $rc[2], $rc[3]);

            return $success;
        }
    }

    public function addUsersOrGroups($id, $idToAdd)
    {
	if(!empty($idToAdd)){
		$this->baseClass->id = $id;
		$Radusergroup = new Radusergroup();

		if($this->baseClassName == 'Raduser')
			$ClassToAdd = new Radgroup();
		else if($this->baseClassName == 'Radgroup')
			$ClassToAdd = new Raduser();

		$priority = 1;
		foreach($idToAdd as $aid){
			$ClassToAdd->id = $aid;
			$Radusergroup->create();
			
			if($this->baseClassName == 'Raduser') {
				$Radusergroup->save(array('username' => $this->baseClass->field('username'), 'groupname' => $ClassToAdd->field('groupname'), 'priority' => $priority));
			} else if($this->baseClassName == 'Radgroup') {
				$Radusergroup->save(array('groupname' => $this->baseClass->field('groupname'), 'username' => $ClassToAdd->field('username'), 'priority' => $Radusergroup->find('count', array('conditions' => array('username' => $ClassToAdd->field('username')))) + 1));
			}

			++$priority;
            }
        }
    }

    public function deleteUsersOrGroups($id, $idToDelete)
    {
        $success = true;
        if(!empty($idToDelete)){
            $this->baseClass->id = $id;
            $Radusergroup = new Radusergroup();
            if($this->baseClassName == 'Raduser')
                $ClassToAdd = new Radgroup();
            else if($this->baseClassName == 'Radgroup')
                $ClassToAdd = new Raduser();

            foreach($idToDelete as $did){
                $ClassToAdd->id = $did;
                if($this->baseClassName == 'Raduser')
                    $success = $success && $Radusergroup->deleteAll(array(
                        'Radusergroup.groupname' => $ClassToAdd->field('groupname'),
                        'Radusergroup.username' => $this->baseClass->field('username')
                    ), false);
                else if($this->baseClassName == 'Radgroup')
                    $success = $success && $Radusergroup->deleteAll(array(
                        'Radusergroup.username' => $ClassToAdd->field('username'), 
                        'Radusergroup.groupname' => $this->baseClass->field('groupname')
                    ), false);
            }
        }
        return $success;
    }

    public function deleteAllUsersOrGroups($id)
    {
	$success = true;
	$this->baseClass->id = $id;
	$Radusergroup = new Radusergroup();
	
	if($this->baseClassName == 'Raduser')
		$ClassToAdd = new Radgroup();
	else if($this->baseClassName == 'Radgroup')
		$ClassToAdd = new Raduser();

	if($this->baseClassName == 'Raduser')
		$success = $Radusergroup->deleteAll(array('Radusergroup.username' => $this->baseClass->field('username')), false);
	else if($this->baseClassName == 'Radgroup')
		$success = $Radusergroup->deleteAll(array('Radusergroup.groupname' => $this->baseClass->field('groupname')), false);
	
	return $success;
    }

    public function getUserGroups($id, $order = array())
    {
	$this->baseClass->id = $id;
	$name = $this->baseClass->field($this->displayName);
	$Radusergroup = new Radusergroup();
	$findAllFunc = 'findAllBy' . ucfirst($this->displayName);
	$usersOrGroups = $Radusergroup->$findAllFunc($name, array(), $order);

	return $usersOrGroups;
    }

    public function delete($request, $id)
    {
        if ($request->is('get')) {
            throw new MethodNotAllowedException();
        }
        $this->baseClass->id = $id;

        // delete matching radchecks
        $rads = $this->getChecks($id)   ;
        foreach($rads as $r)
            $this->checkClass->delete($r[$this->checkClassName]['id']);

        // delete usergroup relations
        $username = $this->baseClass->field($this->displayName);
        $Radusergroup = new Radusergroup();
        $Radusergroup->deleteAll(array('Radusergroup.' . $this->displayName => $username));

        if ($this->baseClass->delete($id)) {
            return true;
        } else {
            return false;
        }

        // TODO: delete certificate on filesystem if necessary
    }

    public function update_radcheck_fields($id, $request, $additionalFields = array()){
        // common fields
        $fields = array(
            'Expiration' => $request->data[$this->baseClassName]['expiration_date'],
            'Simultaneous-Use' => $request->data[$this->baseClassName]['simultaneous_use']
            );
        $fields = array_merge($fields, $additionalFields);
        $rads = $this->getChecks($id);

        foreach($fields as $key=>$value){
            $found = false;
            foreach($rads as &$r){
                if($r[$this->checkClassName]['attribute'] == $key){
                    $found = true;
                    if($r[$this->checkClassName]['value'] != ""){
                        $r[$this->checkClassName]['value'] = $value;
                        $this->checkClass->save($r);
                        break;
                    }
                }
            }
            // FIXME: doesn't work for new check fields!
            if(!$found){
                $r = array($this->checkClassName => array(
                    'attribute' => $key,
                    'value' => $value,
                    'op' => ':=',
                    $this->displayName => $this->baseClass->field($this->displayName)));
                if($value != ""){
                    $this->checkClass->create();
                    $this->checkClass->save($r);
                }
            }
        }
    }

    public function restore_common_check_fields($id, &$request, $cisco=false)
    {
        // restore values from radchecks
        $rads = $this->getChecks($id);
        foreach($rads as $r){
            if($r[$this->checkClassName]['attribute'] == 'NAS-Port-Type' && $cisco){
                $request->data[$this->baseClassName]['nas-port-type'] = $r[$this->checkClassName]['value'];
            } else if($r[$this->checkClassName]['attribute'] == 'Expiration'){
                $request->data[$this->baseClassName]['expiration_date'] = $r[$this->checkClassName]['value'];
            } else if($r[$this->checkClassName]['attribute'] == 'Simultaneous-Use'){
                $request->data[$this->baseClassName]['simultaneous_use'] = $r[$this->checkClassName]['value'];
            }
        }
    }

    // FIXME
    public function login()
    {
        if ($request->is('post')) {
            if ($this->Auth->login()) {
                $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash(__('Invalid username or password, try again.'));
            }
        }
    }

    public function logout()
    {
        $this->redirect($this->Auth->logout());
    }

}

?>
