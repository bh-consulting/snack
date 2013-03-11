<?php

App::uses('AppController', 'Controller');
App::import('Model', 'Radcheck');
App::import('Model', 'Radgroup');
App::import('Model', 'Radusergroup');

/**
 * Controller to handle user management: create, update, remove users.
 */
class RadusersController extends AppController {

    public $helpers = array('Html', 'Form', 'JqueryEngine', 'Csv');
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
        'Session',
        'RequestHandler'
    );

    public function login() {
        if($this->request->is('post')){
            if($this->checkAuthentication(
                $this->request->data['Raduser']['username'],
                $this->request->data['Raduser']['passwd']
            )){
                $this->Auth->login($this->request->data['Raduser']);
                Utils::userlog(__('logged in'));
                return $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash(__('Username or password is incorrect, or user is not authorized to access Snack interface.'), 'default', array(), 'auth');
            }
        }
    }

    public function logout() {
        Utils::userlog(__('logged out'));
        $this->redirect($this->Auth->logout());
    }

    private function checkAuthentication($username, $passwd) {
        $user = $this->Raduser->findByUsername($this->request->data['Raduser']['username']);
        if(isset($user) && !empty($user)){
            $role = $this->Raduser->getRole($user['Raduser']['id']);
            if($role != 'user'){
                $this->request->data['Raduser']['role'] = $role;
                $checks = $this->Checks->getChecks($user['Raduser']['id']);
                foreach ($checks as $check) {
                    if($check['Radcheck']['attribute'] == 'Cleartext-Password'){
                        if($check['Radcheck']['value'] == $passwd){
                            return true;
                        }
                    }
                }
            } else {
                return false;
            }
        }
        return false;
    }

    public function isAuthorized($user) {
        
        // All registered user can view users
        if(in_array($this->action, array(
            'index', 'view_mac', 'view_cert', 'view_loginpass', 'export', 
        ))){
            return true;
        }
        if($user['role'] === 'admin' && in_array($this->action, array(
            'view_cert', 'view_loginpass', 'view_mac',
            'add_cert', 'add_loginpass', 'add_mac',
            'edit_cert', 'edit_loginpass', 'edit_mac',
        ))){
            return true;
        }

        return parent::isAuthorized($user);
    }

    public function beforeFilter() {
        $this->Auth->allow('login', 'logout');
        parent::beforeFilter();
    }
    
    /**
     * Display users list.
     * Manage multiple delete/export actions.
     */
    public function index() {
        // Multiple delete/export
        if ($this->request->is('post')) {
            if(isset($this->request->data['action'])){
                switch ($this->request->data['action']) {
                    case "delete":
                        $this->multipleDelete(
                            $this->request->data['MultiSelection']['users']
                        );
                        break;
                    case "export":
                        $this->multipleExport(
                            $this->request->data['MultiSelection']['users']
                        );
                        break;
                }
            }
        }

        $radusers = $this->paginate('Raduser');

        if($radusers != null){
            foreach ($radusers as &$r) {
                $r['Raduser']['ntype'] = $this->Checks->getType($r['Raduser'], true);
                $r['Raduser']['type'] = $this->Checks->getType($r['Raduser'], false);

                if( $r['Raduser']['type'] == "mac" ) {
                    $r['Raduser']['username'] = Utils::formatMAC(
                        $r['Raduser']['username']
                    );
                }
            }
        }

        $this->set('radusers', $radusers);
    }

    /**
     * Delete severals users.
     *
     * @param ids - array of user ID.
     */
    private function multipleDelete($ids=array()) {
        if (isset($ids) && is_array($ids)) {
            try {
                foreach ($ids as $userId) {
                    $this->Checks->delete($this->request, $userId);
                    Utils::userlog(__('deleted user %s', $userId));
                }

                $this->Session->setFlash(
                    __('Users have been deleted.'),
                    'flash_success'
                );
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );
                Utils::userlog(__('error while deleting users'), 'error');
            }
        } else {
            $this->Session->setFlash(
                __('Please, select at least one user !'),
                'flash_warning'
            );
        }
    }

    public function export($userIds) {
        $usersData = array();
        $user = new Raduser();
        foreach (explode(',', $userIds) as $userId) {
            if (preg_match('#^[0-9]+$#', $userId)) {
                $user->id = $userId;
                $user->read();
                $checks = $this->Checks->getChecks($userId);
                $replies = $this->Checks->getReplies($userId);
                $usersData[] = array_merge(
                    array('type' => 'Raduser'),
                    $user->data['Raduser']
                );

                foreach ($checks as $check) {
                    $usersData[] = array_merge(
                        array('type' => 'Radcheck'),
                        $check['Radcheck']
                    );
                }

                foreach ($replies as $reply) {
                    $usersData[] = array_merge(
                        array('type' => 'Radreply'),
                        $reply['Radreply']
                    );
                }
            }
        }

        $this->set('usersData', $usersData);
        $this->set('filename', 'users_' . date('d-m-Y'));
    }

    /**
     * Export severals users.
     *
     * @param ids - array of user ID.
     */
    private function multipleExport($ids = array()) {
        if (isset($ids) && is_array($ids)) {
            try {
                $this->redirect(array('action' => 'export', implode(',', $ids) . '.csv'));

                $this->Session->setFlash(
                    __('Users have been exported.'),
                    'flash_success'
                );
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );
            }
        } else {
            $this->Session->setFlash(
                __('Please, select at least one user !'),
                'flash_warning'
            );
        }
    }

    /**
     * Display a user and its attributes, depending on its type.
     *
     * @param  $id - user id
     * @param  $type - type of the user to display
     */
    public function view($id = null, $type = array()){
        $views = $this->Checks->view($id);

        $views['base']['Raduser']['ntype'] =
            $this->Checks->getType($views['base']['Raduser'], true);
        $views['base']['Raduser']['type'] =
            $this->Checks->getType($views['base']['Raduser'], false);

        $this->set('raduser', $views['base']);
        $this->set('radchecks', $views['checks']);
        $this->set('radgroups', $views['groups']);

        $attributes = $type;

        // Raduser
        if( $views['base']['Raduser']['type'] == 'mac'
            && strlen( $views['base']['Raduser']['username'] ) == 12
        ) {
            $attributes['MAC address'] = Utils::formatMAC(
                $views['base']['Raduser']['username']
            );

        } else {
            $attributes['Username'] = $views['base']['Raduser']['username'];
        }
        $attributes['Comment'] = $views['base']['Raduser']['comment'];
        $attributes['Role'] = $views['base']['Raduser']['role'];
        $attributes['Certificate path'] = Configure::read('Parameters.certsPath')
                    . '/users/' . $views['base']['Raduser']['username'] . '_';
        $attributes['Cisco'] = $views['base']['Raduser']['is_cisco'] 
            ? _('Yes') : _('No');

        // Radchecks
        foreach($views['checks'] as $check){
            $attributes[ $check['Radcheck']['attribute'] ] =
                $check['Radcheck']['value'];
            if($check['Radcheck']['attribute'] == 'Calling-Station-Id'){
                $attributes['MAC address'] = Utils::formatMAC(
                    $check['Radcheck']['value']);
            }
        }

        // Radgroups
        $groups = array();
        foreach($views['groups'] as $group){
            $groups[] = $group['Radusergroup']['groupname'];
        }

        $attributes['Groups'] = $groups;

        $this->set('attributes', $attributes);
    }

    /**
     * View a user with certificate.
     * @param  $id - user id
     */
    public function view_cert($id = null) {
        $showedAttr = array(
            'Authentication type',
            'Username',
            'Comment',
            'Certificate path',
            'EAP-Type',
            'Expiration',
            'Simultaneous-Use', 
            'Groups',
            'Cisco',
            'MAC address',
            'Role',
        );
        $raduser = $this->Raduser->findById($id);
        if($raduser['Raduser']['is_cisco']){
            $showedAttr[]= 'NAS-Port-Type';
        }
        $this->set('showedAttr', $showedAttr);
        $this->view($id, array( 'Authentication type' => 'Certificate' ));
    }

    /**
     * View a user with login/password.
     * @param  $id - user id
     */
    public function view_loginpass($id = null) {
        $showedAttr = array(
            'Authentication type',
            'Username',
            'Comment',
            'Expiration',
            'Simultaneous-Use',
            'Groups',
            'Cisco',
            'MAC address',
            'Role',
        );

        $raduser = $this->Raduser->findById($id);
        if($raduser['Raduser']['is_cisco']){
            $showedAttr[]= 'NAS-Port-Type';
        }

        $this->set('showedAttr', $showedAttr);
        $this->view($id, array( 'Authentication type' => 'Login / Password' ));
    }

    /**
     * View a user with mac address.
     * @param  $id - user id
     */
    public function view_mac($id = null) {
        $this->set(
            'showedAttr',
            array(
                'Authentication type',
                'MAC address',
                'Comment',
                'Expiration',
                'Simultaneous-Use',
                'Groups',
                'Role',
            )
        );
        $this->view($id, array( 'Authentication type' => 'MAC address' ));
    }

    /**
     * View a user of the interface
     * @param  $id - user id
     */
    public function view_snack($id = null) {
        $this->set(
            'showedAttr',
            array(
                'Role',
                'Comment',
            )
        );
        $this->view($id, array( 'Authentication type' => 'None' ));
    }

    /**
     * Display user add form.
     *
     * @param $success - determine if user was well added.
     * @param $cert - certificate path if user added has a certificate.
     */
    private function add($success) {
        if($this->request->is('post')){
            if ($success) {
                if (isset($this->request->data['Raduser']['is_cert'])
                    && $this->request->data['Raduser']['is_cert'] == 1
                ) {
                    $certs = Utils::getUserCertsPath(
                        $this->request->data['Raduser']['username']
                    );
                    $this->Session->setFlash(__(
                        'New user added. His certificates are %s and %s.',
                        $certs['public'],
                        $certs['key'],
                        'flash_success'
                    ));
                } else {
                    $this->Session->setFlash(
                        __('New user added.'),
                        'flash_success'
                    );
                }

                Utils::userlog(__('added user %s', $this->Raduser->id));
                $this->redirect(array('action' => 'index'));
            }
        }

        // Radgroup
        $groups = new Radgroup();
        $this->set(
            'groups',
            $groups->find('list', array('fields' => array('groupname')))
        );
    }

    /**
     * Add check lines if cisco checkbox is checked or MAC address typed.
     * @param $checks - array of radchecks lines
     */
    private function setCommonCiscoMacFields(&$checks=array()) {
        $username = $this->request->data['Raduser']['username'];

        // retrieve nas-port-type check
        $nasPortTypeIndex = -1;
        for($i = 0; $i < count($checks); $i++){
            if($checks[$i][1] == 'NAS-Port-Type'){
                $nasPortTypeIndex = $i;
                break;
            }
        }

        // add radchecks for cisco user
        if(isset($this->request->data['Raduser']['cisco']) && $this->request->data['Raduser']['cisco'] == 1){
            $this->request->data['Raduser']['is_cisco'] = 1;

            $nasPortType = $this->request->data['Raduser']['nas-port-type'];

            if($nasPortType == 10){
                $nasPortTypeRegexp = '0|5|15';
            } else {
                $nasPortTypeRegexp = $nasPortType . '|15';
            }

            $checks[$nasPortTypeIndex]= array(
                $username,
                'NAS-Port-Type',
                '=~',
                $nasPortTypeRegexp,
            );

            if(isset($this->request->data['Raduser']['is_mac'])
                || isset($this->request->data['Raduser']['is_cert'])){
                    $checks[]= array(
                        $username,
                        'Cleartext-Password',
                        ':=',
                        $this->request->data['Raduser']['passwd'],
                    );
                }
        } else {
            $checks[$nasPortTypeIndex] = array($username, 'NAS-Port-Type', '=~', '15');
            $this->request->data['Raduser']['is_cisco'] = 0;
        }

        // add radchecks for mac auth
        if(isset($this->request->data['Raduser']['calling-station-id'])){
            if(!empty($this->request->data['Raduser']['calling-station-id'])) {
                $mac = Utils::cleanMAC($this->request->data['Raduser']['calling-station-id']);
                $checks[]= array(
                    $username,
                    'Calling-Station-Id',
                    '==',
                    $mac,
                );
                $this->request->data['Raduser']['is_mac'] = 1;
            } else {
                $checks[]= array(
                    $username,
                    'Calling-Station-Id',
                    '==',
                    "",
                );
                $this->request->data['Raduser']['is_mac'] = 0;
            }
        }

        return $checks;
    }

    public function add_loginpass(){
        $success = false;

        if ($this->request->is('post')) {
            try {
                $username = $this->request->data['Raduser']['username'];

                $this->request->data['Raduser']['is_loginpass'] = 1;

                if (isset($this->request->data['Raduser']['ttls'])
                    && $this->request->data['Raduser']['ttls'] == 1) {
                    $eapType = 'EAP-TTLS';
                } else {
                    $eapType = 'MD5-CHALLENGE';
                }

                $rads = array(
                    array(
                        $username,
                        'NAS-Port-Type',
                        '==',
                        '15',
                    ),
                    array(
                        $username,
                        'Cleartext-Password',
                        ':=',
                        $this->request->data['Raduser']['passwd'],
                    ),
                    array(
                        $username,
                        'EAP-Type',
                        ':=',
                        $eapType,
                    )
                );
                $this->setCommonCiscoMacFields($rads);
                $this->Checks->add($this->request, $rads);

                $success = true;
            } catch(UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );
                Utils::userlog(__('error while adding a loginpass user'), 'error');
                $success = false;
            }
        }

        $this->add($success);
    }


    public function add_cert(){
        $success = false;

        if ($this->request->is('post')) {
            try {
                $username = $this->request->data['Raduser']['username'];
                $this->request->data['Raduser']['is_cert'] = 1;

                // Create certificate
                $this->createCertificate(-1, $username);

                $rads = array(
                    array(
                        $username,
                        'NAS-Port-Type',
                        '==',
                        '15'
                    ),
                    array(
                        $username,
                        'EAP-Type',
                        ':=',
                        'TLS'
                    )
                );
                $this->setCommonCiscoMacFields($rads);
                $this->Checks->add($this->request, $rads);

                $success = true;
            } catch(UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );
                Utils::userlog(__('error while adding a cert user'), 'error');

                $success = false;
            }
        }

        $this->add($success);
    }

    public function add_mac(){
        $success = false;

        if ($this->request->is('post')) {
            try {
                $this->request->data['Raduser']['mac'] =
                    Utils::cleanMAC($this->request->data['Raduser']['mac']);

                $username = $this->request->data['Raduser']['mac'];
                $this->request->data['Raduser']['is_mac'] = 1;
                $this->request->data['Raduser']['username'] =
                    $this->request->data['Raduser']['mac'];
                $rads = array(
                    array(
                        $username,
                        'NAS-Port-Type',
                        '==',
                        '15',
                    ),
                    array(
                        $username,
                        'Cleartext-Password',
                        ':=',
                        $this->request->data['Raduser']['mac'],
                    ),
                    array(
                        $username,
                        'EAP-Type',
                        ':=',
                        'MD5-CHALLENGE',
                    )
                );
                $this->Checks->add($this->request, $rads);

                $success = true;
            } catch(UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );

                Utils::userlog(__('error while adding a MAC user'), 'error');
                $success = false;
            }
        }

        $this->add($success);
    }

    /**
     * Controller method to add a Snack user
     */
    public function add_snack() {
        if($this->request->is('post')){
            $found = false;
            $Radcheck = new Radcheck;

            // add the admin rights to an existing user
            if(isset($this->request->data['Raduser']['existing_user'])
                && !empty($this->request->data['Raduser']['existing_user'])
            ){
                $user = $this->Raduser->findById($this->request->data['Raduser']['existing_user']);
                $user['Raduser']['role'] = $this->request->data['Raduser']['role'];
                $this->request->data['Raduser']['username'] = $user['Raduser']['username'];

                // change or add the password to the user
                if(isset($this->request->data['Raduser']['passwd'])){
                    $checks = $this->Checks->getChecks($this->request->data['Raduser']['existing_user']);
                    foreach ($checks as $check) {
                        if($check['Radcheck']['attribute'] == 'Cleartext-Password'){
                            $check['Radcheck']['value'] = $this->request->data['Raduser']['passwd'];
                            $found = true;
                            $Radcheck->save($check);
                            break;
                        }
                    }
                }

                $success = $this->Raduser->save($user);

            // create a new user (only admin)
            } else {
                $this->Raduser->create();
                $success = $this->Raduser->save($this->request->data);
            }

            // the user does not exist in radcheck, create it
            if(!$found){
                $Radcheck->create();
                $Radcheck->save(array(
                    'username' => $this->request->data['Raduser']['username'],
                    'attribute' => 'Cleartext-Password',
                    'op' => ':=',
                    'value' => $this->request->data['Raduser']['passwd']
                ));
            }

            if($success){
                $this->Session->setFlash(__(
                    'The admin user %s was added', $this->request->data['Raduser']['username']),
                    'flash_success'
                );
                Utils::userlog(__('added admin user %s', $this->request->data['Raduser']['username']), 'error');
                $this->redirect(array('action' => 'index'));

            } else {
                $this->Session->setFlash(
                    'Unable to add the admin user',
                    'flash_error'
                );
                Utils::userlog(__('error while adding an admin user'), 'error');
            }
        }

        $users = $this->Raduser->find('all', array('conditions' => array('Raduser.role !=' => 'superadmin')));
        $values = array();
        foreach ($users as $u) {
            $values[$u['Raduser']['id']]= $u['Raduser']['username'];
        }
        $this->set('users', $values);
    }

    private function edit($success) {
        if ($this->request->is('post')) {
            if ($success) {
                $this->Session->setFlash(
                    __('User has been updated.'),
                    'flash_success');

                Utils::userlog(__('edited user %s', $this->Raduser->id));
                $this->redirect(array('action' => 'index'));
            }
        }

        // Radgroup
        $groups = new Radgroup();
        $this->set(
            'groups',
            $groups->find('list', array('fields' => array('id', 'groupname')))
        );
        $this->restoreGroups($this->Raduser->id);

        // Radcheck
        $this->Checks->restoreCommonCheckFields(
            $this->Raduser->id,
            $this->request,
            $this->Raduser->is_cisco
        );

        // Radreply
        $this->Checks->restoreCommonReplyFields(
            $this->Raduser->id,
            $this->request
        );

        // MAC or Cisco properties for active users
        if($this->request->data['Raduser']['is_cisco']
            || $this->request->data['Raduser']['is_mac'])
        {
            $this->Checks->restoreCommonCiscoMacFields(
                $this->Raduser->id,
                $this->request
            );
        }
    }

    public function edit_loginpass($id = null) {
        $this->Raduser->id = $id;
        $success = false;

        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();
        } else {
            try {
                $this->request->data['Raduser']['is_loginpass'] = 1;

                $checksCiscoMac = $this->setCommonCiscoMacFields();

                if(!$this->Raduser->save($this->request->data)){
                    throw new EditException('Raduser', $id, $this->request->data['Raduser']['username']);
                }

                // update radchecks fields
                $checkClassFields = array(
                    'Cleartext-Password' =>
                    $this->request->data['Raduser']['passwd'],
                );
                foreach ($checksCiscoMac as $c) {
                    $checkClassFields[$c[1]] = $c[3];
                }
                $this->Checks->updateRadcheckFields(
                    $id,
                    $this->request,
                    $checkClassFields
                );

                // update radreply fields
                $this->Checks->updateRadreplyFields($id, $this->request);

                // update group list
                $this->updateGroups($this->Raduser->id, $this->request);

                $success = true;
            } catch(UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );
                Utils::userlog(__('error while editing loginpass user %s', $this->Raduser->id), 'error');
                $success = false;
            }
        }

        $this->edit($success);
    }

    public function edit_mac($id = null) {
        $this->Raduser->id = $id;
        $success = false;

        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();
            $this->request->data['Raduser']['username'] =
                Utils::formatMAC(
                    $this->request->data['Raduser']['username']
                );
        } else {
            try {
                $this->request->data['Raduser']['is_mac'] = 1;
                $this->request->data['Raduser']['username'] =
                    Utils::cleanMAC($this->request->data['Raduser']['username']);
                if(!$this->Raduser->save($this->request->data)){
                    throw new EditException('Raduser', $id, $this->request->data['Raduser']['username']);
                }

                $this->updateGroups($this->Raduser->id, $this->request);
                $this->Checks->updateRadcheckFields($id, $this->request);
                $this->Checks->updateRadreplyFields($id, $this->request);

                $success = true;
            } catch(UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );
                Utils::userlog(__('error while editing MAC user %s', $this->Raduser->id), 'error');
                $success = false;
            }
        }

        $this->edit($success);
    }

    public function edit_cert($id = null) {
        $this->Raduser->id = $id;
        $success = false;

        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();
        } else {
            try {
                $this->request->data['Raduser']['is_cert'] = 1;
                $checksCiscoMac = $this->setCommonCiscoMacFields();
                if(!$this->Raduser->save($this->request->data)){
                    throw new EditException('Raduser', $id, $this->request->data['Raduser']['username']);
                }

                // update radchecks fields
                $checkClassFields = array();
                foreach ($checksCiscoMac as $c) {
                    $checkClassFields[$c[1]] = $c[3];
                }
                $this->Checks->updateRadcheckFields($id, $this->request, $checkClassFields);

                $this->Checks->updateRadreplyFields($id, $this->request);

                $this->updateGroups($this->Raduser->id, $this->request);

                // If user asks for a new certificate
                if ($this->request->data['Raduser']['cert_gen'] == 1) {
                    $this->renewCertificate(
                        $id,
                        $this->Raduser->field('username')
                    );
                }

                $success = true;
            } catch(UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );
                Utils::userlog(__('error while editing cert user %s', $this->Raduser->id), 'error');
                $success = false;
            }
        }

        $this->edit($success);
    }

    /**
     * Controller method to edit a Snack user
     */
    public function edit_snack($id=null) {
        $this->Raduser->id = $id;
        $success = false;

        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();
        } else {
            try {
                if(!$this->Raduser->save($this->request->data)){
                    throw new EditException('Raduser', $id, $this->request->data['Raduser']['username']);
                }

                // TODO: update password

            } catch(UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );
                Utils::userlog(__('error while editing Snack user %s', $this->Raduser->id), 'error');
                $success = false;
            }
            $success = true;
        }

        if ($success) {
            $this->Session->setFlash(
                __('User has been updated.'),
                'flash_success');

            Utils::userlog(__('edited user %s', $this->Raduser->id));
            $this->redirect(array('action' => 'index'));
        }
        $this->request->data = $this->Raduser->read();
    }

    public function delete ($id = null) {
        try {
            $this->Raduser->id = $id;

            // Revoke and remove certificate for user
            if ($this->Raduser->field('is_cert') == 1) {
                //TODO: gérer les exceptions
                $this->removeCertificate(
                    $id,
                    $this->Raduser->field('username')
                );
            }

            $this->Checks->delete($this->request, $id);
            Utils::userlog(__('deleted user %s', $id));

            $this->Session->setFlash(
                __('The user with id #%d has been deleted.', $id),
                'flash_success'
            );
        } catch(UserGroupException $e) {
            $this->Session->setFlash(
                $e->getMessage(),
                'flash_error'
            );
            Utils::userlog(__('error while deleting user %s', $id), 'error');
        }

        $this->redirect(array('action' => 'index'));
    }

    /**
     * Restore the list of groups of a user (on edit page)
     * @param  [type] $id user id
     */
    private function restoreGroups($id) {
        $groupsRecords = $this->Checks->getUserGroups(
            $id, array( 'priority' => 'asc')
        );
        $groups = array();

        foreach ($groupsRecords as $group) {
            $groups[]= $group['Radusergroup']['groupname'];
        }

        $this->set('selectedGroups', $groups);
    }

    /**
     * Update the groups of a user
     * @param  [type] $id      user id
     * @param  [type] $request request where to save the groups
     * @return [type]          [description]
     */
    private function updateGroups($id, $request) {
        if (!$this->Checks->deleteAllUsersOrGroups($id)) {
            throw new UserGroupCleanGroupException(
                $request->data['Raduser']['username']
            );
        }

        $result = $this->Checks->addUsersOrGroups(
            $id,
            $request->data['Raduser']['groups']
        );
        
        if (!is_null($result)) {
            throw new UserGroupAddException(
                $request->data['Raduser']['username'],
                $result
            );
        }
    }

    /*
     * Generate a certificate.
     * @param username - Identify the user in the certificate (Common Name)
     * 
     * @return 0 if certificate was generated, error code otherwise.
     */
    public function createCertificate($userID, $username) {
        $command = Configure::read('Parameters.scriptsPath')
            . '/createCertificate '
            . '"' . Configure::read('Parameters.certsPath') . '" '
            . '"' . $username. '" '
            . '"' . Configure::read('Parameters.countryName') . '" '
            . '"' . Configure::read('Parameters.stateOrProvinceName') . '" '
            . '"' . Configure::read('Parameters.localityName') . '" '
            . '"' . Configure::read('Parameters.organizationName') . '" ';

        // Create new certificate
        $result = Utils::shell($command);
        Utils::userlog(__('created certificate for user %s', $userID));

        switch ($result['code']) {
        case 1:
            throw new RSAKeyException($userID, $username);
        case 2:
            throw new CertificateException($userID, $username);
        case 3:
            throw new CertificateSignException($userID, $username);
        case 4:
            throw new CRLException($userID, $username);
        }
    }

    /*
     * Delete and revoke a certificate.
     * @param username - Identify the user in the certificate (Common Name)
     * 
     * @return 0 if certificate was removed, error code otherwise.
     */
    public function removeCertificate($userID, $username) {
        $certs = Utils::getUserCertsPath($username);

        if (file_exists($certs['public']) && file_exists($certs['key'])) {
            $command = Configure::read('Parameters.scriptsPath')
                . '/revokeClient '
                . '"' . Configure::read('Parameters.certsPath') . '" '
                . '"' . $username. '" ';

            // Revoke
            $result = Utils::shell($command);
            Utils::userlog(__('deleted certificate for user %s', $userID));

            if ($result['code']) {
                switch ($result['code']) {
                case 4:
                    throw new CRLException($userID, $username);
                case 5:
                    throw new RevokeException($userID, $username);
                }
            }

            // Delete
            if (!unlink($certs['public']) || !unlink($certs['key'])) {
                throw new CertificateRemoveException($userID, $username);
            }
        } else {
            throw new CertificateNotFoundException($userID, $username);
        }
    }

    /*
     * Generate a new certificate and delete the previous.
     * @param username - Identify the user in the certificate (Common Name)
     * 
     * @return 0 if certificate was generated, error code otherwise.
     */
    public function renewCertificate($userID, $username) {
        $this->removeCertificate($userID, $username);
        $this->createCertificate($userID, $username);
    }
}
?>
