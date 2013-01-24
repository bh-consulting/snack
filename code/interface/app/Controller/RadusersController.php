<?php

App::import('Model', 'Radcheck');
App::import('Model', 'Radgroup');
App::import('Model', 'Radusergroup');

class RadusersController extends AppController {
    public $helpers = array('Html', 'Form', 'JqueryEngine');
    public $paginate = array(
    	'limit' => 10,
    	'order' => array('Raduser.id' => 'asc')
        );
    public $components = array(
    	'Checks' => array(
    	    'displayName' => 'username',
    	    'baseClass' => 'Raduser',
    	    'checkClass' => 'Radcheck',
    	    'replyClass' => 'Radreply',
    	),
	'Session');

    public function index() {
    	if ($this->request->is('post')) {
    	    if (isset($this->request->data['MultiSelection']['users']) &&
        		is_array($this->request->data['MultiSelection']['users'])
    	    ) {
        		$success = false;
        		foreach( $this->request->data['MultiSelection']['users'] as $userId ) {
        		    switch( $this->request->data['action'] ) {
        		    case "delete":
            			$success = $this->Checks->delete($this->request, $userId);
            			break;
        		    case "export":
            			//TODO: export CSV
            			break;
        		    }

        		    if($success){
            			switch( $this->request->data['action'] ) {
            			case "delete":
            			    $this->Session->setFlash(
                				__('Users have been deleted.'),
                				'flash_success'
            			    );
            			    break;
            			case "export":
            			    $this->Session->setFlash(
                				__('Users have been exported.'),
                				'flash_success'
            			    );
            			    break;
            			}
        		    } else {
        			switch( $this->request->data['action'] ) {
            			case "delete":
            			    $this->Session->setFlash(
                				__('Unable to delete users.'),
                				'flash_error'
            			    );
            			    break;
            			case "export":
            			    $this->Session->setFlash(
                				__('Unable to export users.'),
                				'flash_error'
            			    );
            			    break;
            			}
        		    }
        		}
    	    } else {
    		$this->Session->setFlash(__('Please, select at least one user !'), 'flash_warning');
    	    }
    	}

    	$radusers = $this->paginate('Raduser');

    	foreach ($radusers as &$r) {
    	    $r['Raduser']['ntype'] = $this->Checks->getType($r['Raduser'], true);
    	    $r['Raduser']['type'] = $this->Checks->getType($r['Raduser'], false);

    	    if( $r['Raduser']['type'] == "mac" ) {
        		$r['Raduser']['username'] = $this->Checks->formatMAC( $r['Raduser']['username'] );
    	    }
    	}

    	$this->set('radusers', $radusers);
    	// FIXME: should not be here, DRY
    	$this->set('sortIcons', array('asc' => 'icon-chevron-down', 'desc' => 'icon-chevron-up'));
    }

    public function view($id = null, $type = array()){
    	$views = $this->Checks->view($id);

    	$views['base']['Raduser']['ntype'] = $this->Checks->getType($views['base']['Raduser'], true);
    	$views['base']['Raduser']['type'] = $this->Checks->getType($views['base']['Raduser'], false);

    	$this->set('raduser', $views['base']);
    	$this->set('radchecks', $views['checks']);
    	$this->set('radgroups', $views['groups']);

    	$attributes = $type;

    	// Raduser
    	if( $views['base']['Raduser']['type'] == "mac" && strlen( $views['base']['Raduser']['username'] ) == 12 ) {
    	    $attributes['MAC address'] = $this->Checks->formatMAC( $views['base']['Raduser']['username'] );
    	} else {
    	    $attributes['Username'] = $views['base']['Raduser']['username'];
    	}
    	$attributes['Comment'] = $views['base']['Raduser']['comment'];
    	$attributes['Certificate path'] = $views['base']['Raduser']['cert_path'];

    	// Radchecks
    	foreach($views['checks'] as $check){
    	    $attributes[ $check['Radcheck']['attribute'] ] = $check['Radcheck']['value'];
    	}

    	// Radgroups
    	$groups = array();
    	foreach($views['groups'] as $group){
    	    $groups[] = $group['Radusergroup']['groupname'];
    	}

    	$attributes['Groups'] = $groups;

    	$this->set('attributes', $attributes);
    }

    public function view_cisco($id = null) {
    	$this->set('showedAttr', array( 'Authentication type', 'Username', 'Comment', 'NAS-Port-Type', 'Expiration', 'Simultaneous-Use', 'Groups' ));
    	$this->view($id, array( 'Authentication type' => 'Cisco' ));
    }

    public function view_cert($id = null) {
    	$this->set('showedAttr', array( 'Authentication type', 'Username', 'Comment', 'Certificate path', 'EAP-Type', 'Expiration', 'Simultaneous-Use', 'Groups' ));
    	$this->view($id, array( 'Authentication type' => 'Certificate' ));
    }

    public function view_loginpass($id = null) {
    	$this->set('showedAttr', array( 'Authentication type', 'Username', 'Comment', 'Expiration', 'Simultaneous-Use', 'Groups' ));
    	$this->view($id, array( 'Authentication type' => 'Login / Password' ));
    }

    public function view_mac($id = null) {
    	$this->set('showedAttr', array( 'Authentication type', 'MAC address', 'Comment', 'Expiration', 'Simultaneous-Use', 'Groups' ));
    	$this->view($id, array( 'Authentication type' => 'MAC address' ));
    }

    public function add($success){
    	if($this->request->is('post')){
    	    if($success){
    		if(array_key_exists('cert_path', $this->request->data['Raduser']))
    		    $this->Session->setFlash(__('New user added. Certificate in ') . $this->request->data['Raduser']['cert_path'], 'flash_success');
    		else
    		    $this->Session->setFlash(__('New user added.'), 'flash_success');

    		$this->redirect(array('action' => 'index'));
    	    } else {
    		$this->Session->setFlash(__('Unable to add user.'), 'flash_error');
    	    }
    	}
    	$groups = new Radgroup();
    	$this->set('groups', $groups->find('list', array('fields' => array('groupname'))));
    }

    /**
     * Add check lines if cisco checkbox is checked or MAC address typed
     * @param array $checks array of radchecks lines
     */
    private function addCommonCiscoMacFields(&$checks){
        $name = $this->request->data['Raduser']['username'];
        $mac = $this->request->data['Raduser']['mac_active'];

        // add radchecks for cisco user
        if($this->request->data['Raduser']['cisco'] == 1){

            // retrieve nas-port-type check
            $nasPortTypeIndex = -1;
            for($i = 0; $i < count($checks); $i++){
                if($checks[$i][1] == 'NAS-Port-Type'){
                    $nasPortTypeIndex = $i;
                    break;
                }
            }

            $this->request->data['Raduser']['is_cisco'] = 1;
            $nasPortType = $this->request->data['Raduser']['nas-port-type'];

            if($nasPortType == 10){
                $nasPortTypeRegexp = '0|5|15';
            } else {
                $nasPortTypeRegexp = $nasPortType . '|15';
            }

            $checks[$nasPortTypeIndex]= array(
                $name,
                'NAS-Port-Type',
                '=~',
                $nasPortTypeRegexp,
            );

            if(isset($this->request->data['Raduser']['is_mac'])){
                $checks[]= array(
                    $name,
                    'Cleartext-Password',
                    ':=',
                    $this->request->data['Raduser']['password'],
                );
            }
        }

        // add radchecks for mac auth
        if(isset($mac)) {
            $mac = str_replace(':', '', $mac);
            $mac = str_replace('-', '', $mac);
            $checks[]= array(
                $name,
                'Calling-Station-Id',
                '==',
                $mac,
            );
        }
    }

    public function add_loginpass(){
    	$success = false;
    	if ($this->request->is('post')) {

            $name = $this->request->data['Raduser']['username'];
            $this->request->data['Raduser']['is_loginpass'] = 1;
            $rads = array(
                array($name,
                    'NAS-Port-Type',
                    '==',
                    '15'
                ),
                array($name,
                    'Cleartext-Password',
                    ':=',
                    $this->request->data['Raduser']['password']
                ),
                array($name,
                    'EAP-Type',
                    ':=',
                    'MD5-CHALLENGE'
                    // TODO ou 'EAP-TTLS si checkbox cert serveur cochee
                )
            );
            $this->addCommonCiscoMacFields($rads);
            $success = $this->Checks->add($this->request, $rads);
        }
        $this->add($success);
    }


    public function add_mac_active(){
    	$success = false;
    	if ($this->request->is('post')) {

            $this->request->data['Raduser']['mac'] = str_replace(':', '', $this->request->data['Raduser']['mac']);
            $this->request->data['Raduser']['mac'] = str_replace('-', '', $this->request->data['Raduser']['mac']);
            $name = $this->request->data['Raduser']['username'];
            $this->request->data['Raduser']['is_mac'] = 1;
            $rads = array(
                array(
                    $name,
                    'NAS-Port-Type',
                    '==',
                    '15'
                ),
                // FIXME to test
                // array($name,
                //     'Cleartext-Password',
                //     ':=',
                //     $this->request->data['Raduser']['mac']
                // ),
                // array($name,
                //     'EAP-Type',
                //     ':=',
                //     'MD5-CHALLENGE'
                // ),
                array(
                    $name,
                    'Calling-Station-Id',
                    '==',
                    $this->request->data['Raduser']['mac'],
                ),
            );

            $this->addCommonCiscoMacFields($rads);
            $success = $this->Checks->add($this->request, $rads);
        }
        $this->add($success);
    }

    public function add_cert(){
    	$success = false;
    	if ($this->request->is('post')) {

            $name = $this->request->data['Raduser']['username'];
            $this->request->data['Raduser']['is_cert'] = 1;
            $this->request->data['Raduser']['cert_path'] = '/var/www/cert/newcerts/' . $name . '.pem';
            $rads = array(
                array($name,
                    'NAS-Port-Type',
                    '==',
                    '15'
                ),
                array($name,
                    'EAP-Type',
                    ':=',
                    'TLS'
                )
            );
            $this->addCommonCiscoMacFields($rads);
            $success = $this->Checks->add($this->request, $rads);

	    // TODO: generate a certificate
    	}
    	$this->add($success);
    }

    public function add_mac_passive(){
        $success = false;
        if ($this->request->is('post')) {

            $this->request->data['Raduser']['mac'] = str_replace(':', '', $this->request->data['Raduser']['mac']);
            $this->request->data['Raduser']['mac'] = str_replace('-', '', $this->request->data['Raduser']['mac']);
            $name = $this->request->data['Raduser']['mac'];
            $this->request->data['Raduser']['is_mac'] = 1;
            $this->request->data['Raduser']['username'] = $this->request->data['Raduser']['mac'];
            $rads = array(
                array($name,
                    'NAS-Port-Type',
                    '==',
                    '15'
                ),
                array($name,
                    'Cleartext-Password',
                    ':=',
                    $this->request->data['Raduser']['mac']
                ),
                array($name,
                    'EAP-Type',
                    ':=',
                    'MD5-CHALLENGE'
                )
            );
            $success = $this->Checks->add($this->request, $rads);
        }
        $this->add($success);
    }

    public function edit_cisco($id = null){
	$this->Raduser->id = $id;
	if ($this->request->is('get')) {
	    $this->request->data = $this->Raduser->read();

	    $result = $this->request;
	} else {

	    if ($this->Raduser->save($this->request->data)) {

		// update radchecks fields
		$checkClassFields = array(
		    'NAS-Port-Type' => $this->request->data['Raduser']['nas-port-type'],
		    'Cleartext-Password' => $this->request->data['Raduser']['password']);
		$this->Checks->updateRadcheckFields($id, $this->request, $checkClassFields);
		$this->updateGroups($this->Raduser->id, $this->request);

		$result = true;

	    } else {
		$result = false;
	    }
	}
	$this->edit($result);
    }

    public function edit_loginpass($id = null){
	$this->Raduser->id = $id;
	if ($this->request->is('get')) {
	    $this->request->data = $this->Raduser->read();
	    $result = $this->request;
	} else {

	    if ($this->Raduser->save($this->request->data)) {
		// update radchecks fields
		$checkClassFields = array('Cleartext-Password' => $this->request->data['Raduser']['password']);
		$this->Checks->updateRadcheckFields($id, $this->request, $checkClassFields);

		$this->updateGroups($this->Raduser->id, $this->request);

		$result = true;
	    } else {
		$result = false;
	    }
	}
	$this->edit($result);
    }

    public function edit_mac($id = null){
	$this->Raduser->id = $id;
	if ($this->request->is('get')) {
	    $this->request->data = $this->Raduser->read();
	    $this->request->data['Raduser']['username'] = $this->Checks->formatMAC( $this->request->data['Raduser']['username'] );

	    $result = $this->request;
	} else {

	    if ($this->Raduser->save($this->request->data)) {
		$this->updateGroups($this->Raduser->id, $this->request);
		$this->Checks->updateRadcheckFields($id, $this->request);
		$result = true;
	    } else {
		$result = false;
	    }
	}
	$this->edit($result);
    }

    public function edit_cert($id = null){
	$this->Raduser->id = $id;
	if ($this->request->is('get')) {
	    $this->request->data = $this->Raduser->read();
	    $result = $this->request;
	} else {

	    if ($this->Raduser->save($this->request->data)) {
		$this->updateGroups($this->Raduser->id, $this->request);
		$newCert = ($this->request->data['Raduser']['cert_gen'] == 1);

		$this->Checks->updateRadcheckFields($id, $this->request);
		if($newCert){
		    // TODO: generate a new cert
		}
		$result = true;

	    } else {
		$result = false;
	    }
	}
	$this->edit($result);
    }

    public function edit($result){
	if($this->request->is('post')){
	    if($result){
		$this->Session->setFlash(__('User has been updated.'), 'flash_success');
		$this->redirect(array('action' => 'index'));
	    } else {
		$this->Session->setFlash(__('Unable to update user.'), 'flash_error');
	    }
	} else{
	    $this->request = $result;
	}

	$groups = new Radgroup();
	$this->set('groups', $groups->find('list', array('fields' => array('id', 'groupname'))));
	$this->restoreGroups($this->Raduser->id);
	$this->Checks->restore_common_check_fields($this->Raduser->id, $this->request);
    }

    public function delete ($id = null) {
	$success = $this->Checks->delete($this->request, $id);

	if ($success) {
	    $this->Session->setFlash(
		__('The user with id #') . $id . __(' has been deleted.'),
		'flash_success'
	    );
	} else {
	    $this->Session->setFlash(
		__('Unable to delete user with id #') . $id . '.',
		'flash_error'
	    );
	}

	$this->redirect(array('action' => 'index'));
    }

    public function restoreGroups($id)
    {
	$groupsRecords = $this->Checks->getUserGroups($id, array( 'priority' => 'asc'));
	$groups = array();

	foreach ($groupsRecords as $group)
	{
	    $groups[]= $group['Radusergroup']['groupname'];
	}

	$this->set('selectedGroups', $groups);
    }

    public function updateGroups($id, $request)
    {
	$this->Checks->deleteAllUsersOrGroups($id);
	$this->Checks->addUsersOrGroups($id, $request->data['Raduser']['groups']);
    }
}

?>
