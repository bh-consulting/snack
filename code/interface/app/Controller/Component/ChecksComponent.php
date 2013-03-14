<?php

App::uses('Component', 'Controller');
App::import('Model', 'Radcheck');
App::import('Model', 'Raduser');
App::import('Model', 'Radgroupcheck');
App::import('Model', 'Radgroup');
App::import('Model', 'Radusergroup');
App::import('Model', 'Radreply');
App::import('Model', 'Radgroupreply');

class ChecksComponent extends Component {

    private $displayName;
    private $checkClass;
    private $checkClassName;
    private $baseClass;
    private $baseClassName;
    private $replyClass;
    private $replyClassName;

    public function __construct($collection, $params) {
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
    public function getChecks($id) {
        $this->baseClass->id = $id;

        // sorry for that...
        $findAllFunc = 'findAllBy' . ucfirst($this->displayName);
        return $this->checkClass->$findAllFunc(
            $this->baseClass->field($this->displayName)
        );
    }

    /**
     * Get the replies (radreply or radgroupreply) lines linked to this entity
     */
    public function getReplies($id) {
        $this->baseClass->id = $id;

        // sorry for that...
        $findAllFunc = 'findAllBy' . ucfirst($this->displayName);
        return $this->replyClass->$findAllFunc(
            $this->baseClass->field($this->displayName)
        );
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

        if($rad['is_loginpass'])
            return $types[1][$nice];
        if($rad['is_cert']  )
            return $types[3][$nice];
        if($rad['is_mac'])
            return $types[2][$nice];
        if($rad['is_cisco'])
            return $types[0][$nice];
        return "snack";
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
    public function createCheck($displayName, $attribute, $op, $value) {
        if($value != "" && $attribute != ""){
            $data = array(
                $this->displayName => $displayName,
                'attribute' => $attribute,
                'op' => $op,
                'value' => $value
            );

            $rad = new $this->checkClass;
            $rad->create();
            return $rad->save($data);
        }

        return true;
    }

    /**
     * Create a new reply (radreply or radgroupreply) 
     */
    public function createReply($displayName, $attribute, $op, $value){
        if(!empty($value) && !empty($attribute)){
            $reply = new $this->replyClass;

            // create additional replies if vlan id
            if($attribute == 'Tunnel-Private-Group-Id'){
                $replyToAdd = array(
                    $this->displayName => $displayName,
                    'attribute' => 'Tunnel-Type',
                    'op' => ':=',
                    'value' => 'VLAN',
                );

                $reply->create();
                if(!$reply->save($replyToAdd)){
                    throw new ReplyAddException(
                        $this->baseClassName,
                        $this->baseClass->id,
                        $displayName,
                        'Tunnel-Type := VLAN'
                    );
                }

                $replyToAdd = array(
                    $this->displayName => $displayName,
                    'attribute' => 'Tunnel-Medium-Type',
                    'op' => ':=',
                    'value' => 'IEEE-802',
                );
                $reply->create();
                if(!$reply->save($replyToAdd)){
                    throw new ReplyAddException(
                        $this->baseClassName,
                        $this->baseClass->id,
                        $displayName,
                        'Tunnel-Medium-Type := IEEE-802'
                    );
                }
            }

            $data = array(
                $this->displayName => $displayName,
                'attribute' => $attribute,
                'op' => $op,
                'value' => $value
            );

            $reply->create();
            if(!$reply->save($data)){
                throw new ReplyAddException(
                    $this->baseClassName,
                    $this->baseClass->id,
                    $displayName,
                    $attribute.$op.$value
                );
            }
        }
    }

    /**
     * Create a new user or group with all its settings: checks and replies 
     * config
     * @param array $request content of the request
     * @param array $checks checks lines to add 
     */
    public function add($request, $checks) {
        if ($request->is('post')) { 
            // add common checks values (expiration date and simultaneous uses)
            $name = $request->data[$this->baseClassName][$this->displayName];
            if(isset($request->data[$this->baseClassName]['expiration_date'])){
                $checks = array_merge($checks, array(
                    array(
                        $name,
                        'Expiration',
                        '==',
                        Utils::formatDate(
                            $request->data[$this->baseClassName]['expiration_date'],
                            'expiration'
                        )
                    ),
                ));
            }
            if(isset($request->data[$this->baseClassName]['simultaneous_use'])){
                $checks = array_merge($checks, array(
                    array(
                        $name,
                        'Simultaneous-Use',
                        '==',
                        $request->data[$this->baseClassName]['simultaneous_use']
                    )
                ));
            }

            $this->baseClass->create();
            if (!$this->baseClass->save($request->data)) {
                if (isset($request->data[$this->baseClassName]['is_mac'])
                    && isset($request->data[$this->baseClassName]['mac'])
                    && $request->data[$this->baseClassName]['is_mac']
                ) {
                    $name = $request->data[$this->baseClassName]['mac'];
                }

                throw new AddException($this->baseClassName, $name);
            }

            // Add users or groups linked to this group or user
            if (isset($request->data[$this->baseClassName]['groups'])) {
                $result = $this->addUsersOrGroups(
                    $this->baseClass->id,
                    $request->data[$this->baseClassName]['groups']
                );

                if (!is_null($result)) {
                    throw new UserGroupAddException($name, $result);
                }
            } elseif (isset($request->data[$this->baseClassName]['users'])) {
                $result = $this->addUsersOrGroups(
                    $this->baseClass->id,
                    $request->data[$this->baseClassName]['users']
                );

                if (!is_null($result)) {
                    throw new UserGroupAddException($result, $name);
                }
            }

            // Create all checks
            foreach ($checks as $rc) {
                if (!$this->createCheck($rc[0], $rc[1], $rc[2], $rc[3])) {
                    throw new CheckAddException(
                        $this->baseClassName,
                        $this->baseClass->id,
                        $name,
                        $rc[1].$rc[2].$rc[3]
                    );
                }
            }

            // Add common replies values
            $replies = array();
            if(isset($request->data[$this->baseClassName]['tunnel-private-group-id'])){
                $replies[]= array(
                    $name,
                    'Tunnel-Private-Group-Id',
                    ':=',
                    $request->data[$this->baseClassName]['tunnel-private-group-id']
                );
            }
            if(isset($request->data[$this->baseClassName]['session-timeout'])){
                $checks[]= array(
                    $name,
                    'Session-Timeout',
                    ':=',
                    $request->data[$this->baseClassName]['session-timeout']
                );
            }
            
            foreach($replies as $reply){
                $this->createReply($reply[0], $reply[1],$reply[2], $reply[3]);
            }
        }
    }

    /**
     * Link user(s) to a group, or group(s) to a user
     */
    public function addUsersOrGroups($id, $idToAdd) {
        if (!empty($idToAdd)) {
            $this->baseClass->id = $id;
            $Radusergroup = new Radusergroup();

            if ($this->baseClassName == 'Raduser') {
                $ClassToAdd = new Radgroup();
            } else if ($this->baseClassName == 'Radgroup') {
                $ClassToAdd = new Raduser();
            }

            $priority = 1;
            foreach ($idToAdd as $aid) {
                $ClassToAdd->id = $aid;
                $Radusergroup->create();

                if($this->baseClassName == 'Raduser') {
                    $result = $Radusergroup->save(
                        array(
                            'username' => $this->baseClass->field('username'),
                            'groupname' => $ClassToAdd->field('groupname'),
                            'priority' => $priority,
                        )
                    );

                    if (!$result) {
                        return $ClassToAdd->field('groupname');
                    }
                } else if ($this->baseClassName == 'Radgroup') {
                    $result = $Radusergroup->save(
                        array(
                            'groupname' => $this->baseClass->field('groupname'),
                            'username' => $ClassToAdd->field('username'),
                            'priority' => $Radusergroup->find(
                                'count',
                                array(
                                    'conditions' => array(
                                        'username' => $ClassToAdd->field('username')
                                    )
                                )
                            ) + 1,
                        )
                    );

                    if (!$result) {
                        return $ClassToAdd->field('username');
                    }
                }

                ++$priority;
            }
        }

        return null;
    }

    /**
     * Delete the user(s) of a group, or group(s) of a user 
     * @param  int $id         id of the base class
     * @param  int $idToDelete id of the class to delete
     * @return [type]
     */
    public function deleteUsersOrGroups($id, $idToDelete) {
        if (!empty($idToDelete)) {
            $this->baseClass->id = $id;

            $Radusergroup = new Radusergroup();
            if ($this->baseClassName == 'Raduser') {
                $ClassToAdd = new Radgroup();
            } else if($this->baseClassName == 'Radgroup') {
                $ClassToAdd = new Raduser();
            }

            foreach ($idToDelete as $did) {
                $ClassToAdd->id = $did;

                if ($this->baseClassName == 'Raduser') {
                    $result = $Radusergroup->deleteAll(array(
                        'Radusergroup.groupname' => $ClassToAdd->field('groupname'),
                        'Radusergroup.username' => $this->baseClass->field('username')
                    ), false);

                    if (!$result) {
                        return $ClassToAdd->field('groupname');
                    }
                } else if($this->baseClassName == 'Radgroup') {
                    $result = $Radusergroup->deleteAll(array(
                        'Radusergroup.username' => $ClassToAdd->field('username'), 
                        'Radusergroup.groupname' => $this->baseClass->field('groupname')
                    ), false);

                    if (!$result) {
                        return $ClassToAdd->field('username');
                    }
                }
            }
        }

        return null;
    }

    /**
     * Delete all the users of a group, or all groups of a user
     */
    public function deleteAllUsersOrGroups($id) {
        $this->baseClass->id = $id;
        $Radusergroup = new Radusergroup();

        $result = $Radusergroup->deleteAll(
            array('Radusergroup.' . $this->displayName
            => $this->baseClass->field($this->displayName))
        );

        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * Get the groups of a user given its id
     */
    public function getUserGroups($id, $order = array()) {
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
    public function delete($request, $id) {
        if ($request->is('get')) {
            throw new MethodNotAllowedException();
        }

        // Entity info
        $this->baseClass->id = $id;
        $name = $this->baseClass->field($this->displayName);

        // Delete matching radchecks
        $rads = $this->getChecks($id);
        foreach ($rads as $r) {
            if (!$this->checkClass->delete($r[$this->checkClassName]['id'])) {
                throw new CheckRemoveException(
                    $this->baseClassName,
                    $id,
                    $name,
                    $r[$this->checkClassName]['id']
                );
            }
        }

        // Delete matching radreplies
        $rads = $this->getReplies($id);
        foreach ($rads as $r) {
            if (!$this->replyClass->delete($r[$this->replyClassName]['id'])) {
                throw new ReplyRemoveException(
                    $this->baseClassName,
                    $id,
                    $name,
                    $r[$this->replyClassName]['id']
                );
            }
        }

        // Delete user-group relations
        $this->deleteAllUsersOrGroups($id);

        if (!$this->baseClass->delete($id)) {
            throw new DeleteException($this->baseClassName, $id, $name);
        }
    }

    /**
     * Update the check fields of a user or a group
     */
    public function updateRadcheckFields($id, $request, $additionalFields = array()){
        // common fields
        $fields = array(
            'Expiration' => Utils::formatDate(
                $request->data[$this->baseClassName]['expiration_date'],
                'expiration'
            ),
            'Simultaneous-Use' => $request->data[$this->baseClassName]['simultaneous_use']
        );
        $fields = array_merge($fields, $additionalFields);
        $rads = $this->getChecks($id);

        foreach($fields as $key=>$value){
            $found = false;
            foreach($rads as &$r){
                if($r[$this->checkClassName]['attribute'] == $key){
                    $found = true;
                    // value is set
                    if(!empty($value)){
                        // value modified
                        if($value != $r[$this->checkClassName]['value']){
                            $oldValue = $r[$this->checkClassName]['value'];
                            $r[$this->checkClassName]['value'] = $value;

                            // check if that was a cisco user that was deleted
                            // TODO: test!!
                            if($key == 'NAS-Port-Type'
                                && preg_match('/[05]/', $oldValue)
                                && $value == '15'
                                && !isset($request->data[$this->baseClassName]['is_loginpass'])
                            ){
                                $this->checkClass->deleteAll(array(

                                    $this->checkClassName . '.' . $this->displayName => $r[$this->checkClassName][$this->displayName],
                                    $this->checkClassName . '.attribute' => 'Cleartext-Password',
                                ));
                            }

                            $this->checkClass->save($r);
                        
                        }
                        break;
                    // blank field => value deleted
                    } else {
                        // delete radcheck if not a password field
                        if($key != 'Cleartext-Password'){
                            $this->checkClass->delete($r[$this->checkClassName]['id']);
                        }
                    }
                }
            }
            if(!$found && !empty($value)){
                if (!$this->createCheck(
                    $this->baseClass->field($this->displayName),
                    $key,
                    ':=',//TODO: ceci n'est pas vrai pour tous les attributs (==, =~..)
                    $value
                )) {
                    throw new CheckAddException(
                        $this->baseClassName,
                        $this->baseClass->id,
                        $this->baseClass->field($this->displayName),
                        $key.':='.$value
                    );
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
            'Session-Timeout' => $request->data[$this->baseClassName]['session-timeout'],
        );
        $rads = $this->getReplies($id);

        foreach($fields as $key=>$value){
            $found = false;
            foreach($rads as &$r){
                if($r[$this->replyClassName]['attribute'] == $key){
                    $found = true;
                    // value is set
                    if(!empty($value)){
                        // new value
                        if($value != $r[$this->replyClassName]['value']){
                            // update radreply
                            $r[$this->replyClassName]['value'] = $value;
                            $this->replyClass->save($r);
                        }
                        break;
                    // blank value => attribute deleted
                    } else {
                        // delete radreply
                        $this->replyClass->delete($r[$this->replyClassName]['id']);
                        if($key == 'Tunnel-Private-Group-Id'){
                            $this->replyClass->deleteAll(array(
                                $this->replyClassName . '.' . $this->displayName => $r[$this->replyClassName][$this->displayName],
                                $this->replyClassName . '.attribute' => array('Tunnel-Type', 'Tunnel-Medium-Type'),
                            ));
                        }
                        break;
                    }
                }
            }
            if(!$found){
                $this->createReply(
                    $this->baseClass->field($this->displayName),
                    $key,
                    ':=',//TODO: ceci n'est pas vrai pour tous les attributs (==, =~..)
                    $value
                );
            }
        }
    }

    /**
     * Restore the check fields of a user or a group
     */
    public function restoreCommonCheckFields($id, &$request, $cisco=false)
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

    /**
     * Restore the reply fields of a user or a group
     */
    public function restoreCommonReplyFields($id, &$request)
    {
        // restore values from radreply
        $rads = $this->getReplies($id);
        foreach($rads as $r){
            if($r[$this->replyClassName]['attribute'] == 'Tunnel-Private-Group-Id'){
                $request->data[$this->baseClassName]['tunnel-private-group-id'] = $r[$this->replyClassName]['value'];
            } else if($r[$this->replyClassName]['attribute'] == 'Session-Timeout'){
                $request->data[$this->baseClassName]['session-timeout'] = $r[$this->replyClassName]['value'];
            }
        }
    }

    /**
     * Restore the MAC or Cisco properties for active users
     * @param  [type] $id      user id
     * @param  [type] $request request where to values
     */
    public function restoreCommonCiscoMacFields($id, &$request) {
        if($request->data[$this->baseClassName]['is_cisco']){
            $request->data[$this->baseClassName]['cisco'] = 1;
            $request->data[$this->baseClassName]['was_cisco'] = 1;
        }

        $rads = $this->getChecks($id);
        foreach($rads as $r){
            switch($r[$this->checkClassName]['attribute']){
                case 'Calling-Station-Id':
                    $request->data[$this->baseClassName]['calling-station-id'] = Utils::formatMac($r[$this->checkClassName]['value']);
                    break;

                case 'NAS-Port-Type':
                    $nasPortType = $r[$this->checkClassName]['value'];

                    if($nasPortType == '0|5|15'){
                        $request->data[$this->baseClassName]['nas-port-type'] = 10;
                    } else if(preg_match('/.*0.*/', $r[$this->checkClassName]['value'])){
                        $request->data[$this->baseClassName]['nas-port-type'] = 0;
                    } else if(preg_match('/.*5.*/', $r[$this->checkClassName]['value'])){
                        $request->data[$this->baseClassName]['nas-port-type'] = 5;
                    }
                    break;
            }
        }
    }

}

?>
