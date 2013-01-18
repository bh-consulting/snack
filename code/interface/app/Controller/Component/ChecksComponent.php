<?php

App::uses('Component', 'Controller');
App::import('Model', 'Radcheck');
App::import('Model', 'Raduser');
App::import('Model', 'Radgroupcheck');
App::import('Model', 'Radgroup');
App::import('Model', 'Radusergroup');
App::import('Model', 'Radreply');
App::import('Model', 'Radgroupreply');

class ChecksComponent extends Component
{

	private $displayName;
	private $checkClass;
	private $checkClassName;
	private $baseClass;
	private $baseClassName;
    private $replyClass;
    private $replyClassName;

	public function __construct($collection, $params)
    {
		$this->displayName = $params['displayName'];
		$this->checkClass = new $params['checkClass'];
		$this->checkClassName = $params['checkClass'];
		$this->baseClass = new $params['baseClass'];
		$this->baseClassName = $params['baseClass'];
        $this->replyClass = new $params['replyClass'];
        $this->replyClassName = $params['replyClass'];
	}

    /**
    * Get the checks (radcheck or radgroupcheck) lines linked to this entity
    */
    public function getChecks($id)
    {
        $this->baseClass->id = $id;

        // sorry for that...
        $findAllFunc = 'findAllBy' . ucfirst($this->displayName);
        return $this->checkClass->$findAllFunc($this->baseClass->field($this->displayName));
    }

    /**
    * Get the replies (radreply or radgroupreply) lines linked to this entity
    */
    public function getReplies($id)
    {
        $this->baseClass->id = $id;

        // sorry for that...
        $findAllFunc = 'findAllBy' . ucfirst($this->displayName);
        return $this->replyClass->$findAllFunc($this->baseClass->field($this->displayName));
    }

    /**
    * Get the type of user, in a simple to read or nice to read way
    */
    public function getType($rad, $nice = false) {
        $types = array(
            array('cisco', __('Cisco')),
            array('loginpass', __('Login / Password')),
            array('mac', __('MAC')),
            array('cert', __('Certificate'))
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

    /**
    * Reformat the mac addr for a nice display
    */
    public function formatMAC( $mac, $delimiter = ':' )
    {
	    return ( strlen( $mac ) == 12 ) ? substr($mac, 0, 2) . $delimiter . substr($mac, 2, 2) . $delimiter . substr($mac, 4, 2) . $delimiter . substr($mac, 6, 2) . $delimiter . substr($mac, 8, 2) . $delimiter . substr($mac, 10, 2) : $mac;
    }

    /**
    * Get the index page: list of all entities (users or groups) with its type if user
    */
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

    /**
    * Get the details of an entity to display it in a view page
    */
    public function view($id = null) {
        $this->baseClass->id = $id;
        return array(
        	'base' => $this->baseClass->read(), 
    		'checks' => $this->getChecks($id),
    		'groups' => $this->getUserGroups($id)
        );
    }

    /**
    * Create a new check (radcheck or radgroupcheck) 
    */
    public function createCheck($displayName, $attribute, $op, $value){
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

    /**
    * Create a new reply (radreply or radgroupreply) 
    */
    public function createReply($displayName, $attribute, $op, $value){
        if($value != "" && $attribute != ""){
            $data = array(
                $this->displayName => $displayName,
                'attribute' => $attribute,
                'op' => $op,
                'value' => $value
            );
            $reply = new $this->replyClass;
            $reply->create();
            return $reply->save($data);
        } else {
            return true;
        }
    }

    /**
    * Create a new user or group with all its settings: checks and replies config
    */
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
                $success = $success && $this->createCheck($rc[0], $rc[1], $rc[2], $rc[3]);

            // add common replies values
            $replies = array();
            if(isset($request->data[$this->baseClassName]['tunnel-private-group-id'])){
                $replies[]= array($name,
                                'Tunnel-Type',
                                '=',
                                'VLAN'
                );
                $replies[]= array($name,
                                'Tunnel-Medium-Type',
                                '=',
                                'IEEE-802'
                );
            }
            $replies[]= array(
                $name,
                'Tunnel-Private-Group-Id',
                '=',
                $request->data[$this->baseClassName]['tunnel-private-group-id']
            );
            $replies[]= array(
                $name,
                'Reply-Message',
                '=',
                $request->data[$this->baseClassName]['reply-message']
            );
            $replies[]= array(
                $name,
                'Exec-Program-Wait',
                '=',
                $request->data[$this->baseClassName]['exec-program-wait']
            );
            $replies[]= array(
                $name,
                'Session-Timeout',
                '=',
                $request->data[$this->baseClassName]['session-timeout']
            );
            foreach($replies as $reply){
                $success = $success && $this->createReply($reply[0], $reply[1], $reply[2], $reply[3]);
            }

            return $success;
        }
    }

    /**
    * Link user(s) to a group, or group(s) to a user
    */
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

    /**
    * Delete the user(s) of a group, or group(s) of a user
    */
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

    /**
    * Delete all the users of a group, or all groups of a user
    */
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

    /**
    * Get the groups of a user given its id
    */
    public function getUserGroups($id, $order = array())
    {
    	$this->baseClass->id = $id;
    	$name = $this->baseClass->field($this->displayName);
    	$Radusergroup = new Radusergroup();
    	$findAllFunc = 'findAllBy' . ucfirst($this->displayName);
    	$usersOrGroups = $Radusergroup->$findAllFunc($name, array(), $order);

    	return $usersOrGroups;
    }

    /**
    * Delete a user or a group
    */
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

    /**
    * Update the check fields of a user or a group
    */
    public function updateRadcheckFields($id, $request, $additionalFields = array()){
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

    /**
    * Update the reply fields of a user or a group
    */
    public function updateRadreplyFields($id, $request){
        // common fields
        $fields = array(
            'Tunnel-Private-Group-Id' => $request->data[$this->baseClassName]['tunnel-private-group-id'],
            'Reply-Message' => $request->data[$this->baseClassName]['reply-message'],
            'Exec-Program-Wait' => $request->data[$this->baseClassName]['exec-program-wait'],
            'Session-Timeout' => $request->data[$this->baseClassName]['session-timeout'],
            );
        $rads = $this->getReplies($id);

        foreach($fields as $key=>$value){
            $found = false;
            foreach($rads as &$r){
                if($r[$this->replyClassName]['attribute'] == $key){
                    $found = true;
                    if($r[$this->replyClassName]['value'] != ""){
                        $r[$this->replyClassName]['value'] = $value;
                        $this->replyClass->save($r);
                        break;
                    }
                }
            }
            // FIXME: doesn't work for new check fields!
            if(!$found){
                $r = array($this->replyClassName => array(
                    'attribute' => $key,
                    'value' => $value,
                    'op' => ':=',
                    $this->displayName => $this->baseClass->field($this->displayName)));
                if($value != ""){
                    $this->replyClass->create();
                    $this->replyClass->save($r);
                }
            }
        }
    }

    /**
    * Restore the check fields of a user or a group
    */
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
