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
        'Filters',
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
        if ($this->request->is('post')) {
            if ($this->checkAuthentication(
                $this->request->data['Raduser']['username'],
                $this->request->data['Raduser']['passwd']
            )) {
                $this->Auth->login($this->request->data['Raduser']);
                Utils::userlog(__('logged in'));

                $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash(
                    __('Username or password is incorrect,'
                    . ' or user is not authorized to access Snack interface.'),
                    'default',
                    array(),
                    'auth'
                );
            }
        }
    }

    public function logout() {
        Utils::userlog(__('logged out'));
        $this->redirect($this->Auth->logout());
    }

    private function checkAuthentication($username, $passwd) {
        $user = $this->Raduser
            ->findByUsername($this->request->data['Raduser']['username']);
        if (isset($user) && !empty($user)) {
            $role = $this->Raduser->getRole($user['Raduser']['id']);
            if ($role != 'user') {
                $this->request->data['Raduser']['role'] = $role;
                $checks = $this->Checks->getChecks($user['Raduser']['id']);
                foreach ($checks as $check) {
                    if ($check['Radcheck']['attribute'] == 'Cleartext-Password') {
                        if ($check['Radcheck']['value'] == $passwd) {
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
        if (in_array($this->action, array(
            'index', 'view_mac', 'view_cert', 'view_loginpass', 'export', 
        ))) {
            return true;
        }

        if ($user['role'] === 'admin' && in_array($this->action, array(
            'view_cert', 'view_loginpass', 'view_mac',
            'add_cert', 'add_loginpass', 'add_mac',
            'edit_cert', 'edit_loginpass', 'edit_mac',
        ))) {
            return true;
        }

        return parent::isAuthorized($user);
    }

    public function beforeFilter() {
        $this->Auth->allow('login', 'logout');
        parent::beforeFilter();
    }

    public function beforeValidateForFilters() {
        unset($this->Raduser->validate['username']['notEmpty']['required']);
    }

    public function getRegexExpiration($args = array()) {
        if (!empty($args['input'])) {
            $data = &$this->request->data['Raduser'][$args['input']];

            if (isset($data[0]) && $data[0] == 'expired') {
                return "(username IN (SELECT username from radcheck "
                    . "where attribute='Expiration' "
                    . "and STR_TO_DATE(value, '%d %b %Y %T') < NOW()))";
            } else {
                return '(1=1)';
            }
        }
    }

    /**
     * Display users list.
     * Manage multiple delete/export actions.
     */
    public function index() {
        // Multiple delete/export
        if ($this->request->is('post')) {
            if (isset($this->request->data['action'])) {
                switch ($this->request->data['action']) {
                case "delete":
                    $this->multipleDelete(
                        isset($this->request->data['MultiSelection']) ?
                        $this->request->data['MultiSelection']['users'] : 0
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

        // Filters
        $this->Filters->addStringConstraint(array(
            'fields' => array(
                'id',
                'username',
                'comment',
            ),
            'input' => 'text',
            'ahead' => array('username'),
        ));

        $this->Filters->addBooleanConstraint(array(
            'fields' => array('is_loginpass', 'is_phone', 'is_mac', 'is_cisco', 'is_cert'),
            'input' => 'authtype',
            'items' => array(
                'is_cisco' => __('Cisco'),
                'is_mac' => __('MAC'),
		'is_phone' => __('Phone'),
                'is_loginpass' => __('Login/Pwd'),
		'is_phone' => __('Phone'),
                'is_cert' => __('Certificate'),
            ),
        ));

        $this->Filters->addSelectConstraint(array(
            'fields' => array('role'),
            'items' => array(
                'user' => $this->Raduser->roles['user'],
                'tech' => $this->Raduser->roles['tech'],
                'admin' => $this->Raduser->roles['admin'],
                'root' => $this->Raduser->roles['root'],
            ),
            'input' => 'rolefilter',
            'title' => false,
        ));

        $this->Filters->addComplexConstraint(array(
            'select' => array(
                'items' => array(
                    'expired' => '<i class="icon-warning-sign icon-red"></i> '
                    . __('Expired'),
                ),
                'input' => 'expired',
                'title' => false,
            ),
            'callback' => array(
                'getRegexExpiration',
                array(
                    'input' => 'expired',
                ),
            )
        ));

        $radusers = $this->Filters->paginate();

        if ($radusers != null) {
            $userList = array();
            foreach ($radusers as &$user) {
                if (!empty($user['Raduser']['username'])) {
                    $userList[] = $user['Raduser']['username'];
                }

                $user['Raduser']['ntype'] = $this->Checks->getType(
                    $user['Raduser'],
                    true
                );

                $user['Raduser']['type'] = $this->Checks->getType(
                    $user['Raduser'],
                    false
                );
            }

            if (!empty($userList)) {
                $radcheck = new Radcheck();
                $expirations = $radcheck->find(
                    'list',
                    array(
                        'fields' => array('username', 'value'),
                        'conditions' => array(
                            'username REGEXP' => '^(' . implode($userList, '|') . ')$',
                            'attribute' => 'Expiration',
                        )
                    )
                );
                foreach ($radusers as &$user) {
		    if (isset($expirations[$user['Raduser']['username']])
                        && Utils::formatDate(
			    array(
                                $expirations[$user['Raduser']['username']],
                                date('d M Y H:i:s')
                            ),
                            'dursign') >= 0
                    ) {
                        $user['Raduser']['expiration'] = Utils::formatDate(
                            $expirations[$user['Raduser']['username']],
                            'display'
                        );
                    } else {
                        $user['Raduser']['expiration'] = -1;
                    }
                }
            }
	    foreach ($radusers as &$user) {		    
		if ($user['Raduser']['type'] == "mac") {
                    $user['Raduser']['username'] = Utils::formatMAC(
                        $user['Raduser']['username']
                    );
                }
	   }
        }

        $this->set('roles', $this->Raduser->roles);
        $this->set('radusers', $radusers);
    }

    /**
     * Delete severals users.
     *
     * @param ids - array of user ID.
     */
    private function multipleDelete($ids = array()) {
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
                __('Please, select at least one user!'),
                'flash_warning'
            );
        }
    }

    public function import() {
        if ($this->request->isPost()) {
            $handle = fopen($_FILES['data']['tmp_name']['importCsv']['file'], "r");
            $results = array();

            while (($fields = fgetcsv($handle)) != false) {
                switch ($fields[0]) {
                case 'Raduser':
                    if (count($fields) >= 7) {
                        $user = new Raduser();
                        $user->set('username', $fields[1]);
                        $user->set('role', $fields[2]);
                        $user->set('is_cisco', $fields[3]);
                        $user->set('is_loginpass', $fields[4]);
                        $user->set('is_cert', $fields[5]);
                        $user->set('is_mac', $fields[6]);

                        if (isset($fields[7])) {
                            $user->set('comment', $fields[7]);
                        }

                        if ($user->save()) {
                             $results[] = __('%s was added', $fields[1]);
                        } else {
                             $results[] = __('ERROR: %s was not added', $fields[1]);
                        }
                    }
                    break;
                case 'Radcheck':
                    if (count($fields) == 4) {
                        $check = new Radcheck();
                        $check->set('username', $fields[1]);
                        $check->set('attribute', $fields[2]);
                        $check->set('op', $fields[3]);
                        $check->set('value', $fields[4]);

                        if ($check->save()) {
                             $results[] = __('%s (%s) was added', $fields[1], $fields[2]);
                        } else {
                             $results[] = __('ERROR: %s (%s) was not added', $fields[1], $fields[2]);
                        }
                    }
                    break;
                case 'Radreply':
                    if (count($fields) == 4) {
                        $reply = new Radcheck();
                        $reply->set('username', $fields[1]);
                        $reply->set('attribute', $fields[2]);
                        $reply->set('op', $fields[3]);
                        $reply->set('value', $fields[4]);

                        if ($reply->save()) {
                             $results[] = __('%s (%s) was added', $fields[1], $fields[2]);
                        } else {
                             $results[] = __('ERROR: %s (%s) was not added', $fields[1], $fields[2]);
                        }
                    }
                    break;
                case 'Radusergroup':
                    if (count($fields) >= 2) {
                        $usergroup = new Radusergroup();
                        $usergroup->set('username', $fields[1]);
                        $usergroup->set('groupname', $fields[2]);

                        if (isset($fields[3])) {
                            $usergroup->set('priority', $fields[3]);
                        } else {
                            $usergroup->set('priority', 1);
                        }

                        if ($usergroup->save()) {
                             $results[] = __('%s was added to %s', $fields[1], $fields[2]);
                        } else {
                             $results[] = __('ERROR: %s was not added to %s', $fields[1], $fields[2]);
                        }
                    }
                    break;
                }
            }

            $this->set('results', $results);
        } else {
            $this->redirect(array('controller' => 'Radusers', 'action' => 'index'));
        }
    }

    public function exportAll() {
       $ids =  $this->Raduser->find('list', array('fields' => 'id'));

       $this->redirect(array('controller' => 'Radusers', 'action' => 'export', implode($ids, ',') . '.csv'));
    }

    public function export($userIds) {
        $usersData = array();
        foreach (explode(',', $userIds) as $userId) {
            if (preg_match('#^[0-9]+$#', $userId)) {
                $user = $this->Raduser->read(null, $userId);

                if ($user['Raduser']) {
                    $checks = $this->Checks->getChecks($userId);
                    $replies = $this->Checks->getReplies($userId);
                    $usergroups = $this->Checks->getUserGroups($userId);

                    $usersData[] = array(
                        'Raduser',
                        $user['Raduser']['username'],
                        !empty($user['Raduser']['role']) ? $user['Raduser']['role'] : 'user',
                        isset($user['Raduser']['is_cisco']) && $user['Raduser']['is_cisco'],
                        isset($user['Raduser']['is_loginpass']) && $user['Raduser']['is_loginpass'],
                        isset($user['Raduser']['is_cert']) && $user['Raduser']['is_cert'],
                        isset($user['Raduser']['is_mac']) && $user['Raduser']['is_mac'],
                        $user['Raduser']['comment'],
                    );

                    foreach ($checks as $check) {
                        $usersData[] = array(
                            'Radcheck',
                            $check['Radcheck']['username'],
                            $check['Radcheck']['attribute'],
                            $check['Radcheck']['op'],
                            $check['Radcheck']['value'],
                        );
                    }

                    foreach ($replies as $reply) {
                        $usersData[] = array(
                            'Radreply',
                            $reply['Radreply']['username'],
                            $reply['Radreply']['attribute'],
                            $reply['Radreply']['op'],
                            $reply['Radreply']['value'],
                        );
                    }

                    foreach ($usergroups as $usergroup) {
                        $usersData[] = array(
                            'Radusergroup',
                            $usergroup['Radusergroup']['username'],
                            $usergroup['Radusergroup']['groupname'],
                            $usergroup['Radusergroup']['priority'],
                        );
                    }
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
                __('Please, select at least one user!'),
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
        $username = $views['base']['Raduser']['username'];

        // Raduser
        if( $views['base']['Raduser']['type'] == 'mac'
            && strlen($username) == 12
        ) {
            $attributes['MAC address'] = Utils::formatMAC($username);

        } else {
            $attributes['Username'] = $username;
        }
        $attributes['Comment'] = $views['base']['Raduser']['comment'];
        $attributes['Role'] = $this->Raduser->roles[$views['base']['Raduser']['role']];
        $attributes['User certificate path'] = Utils::getUserCertsPath($username);
        $attributes['Server certificate path'] = Utils::getServerCertPath();
        $attributes['Cisco'] = $views['base']['Raduser']['is_cisco'] 
            ? __('Yes') : __('No');

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
            'User certificate path',
            'Server certificate path',
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
            'EAP-Type',
            'Server certificate path',
            'Role',
        );

        $raduser = $this->Raduser->findById($id);
        if($raduser['Raduser']['is_cisco']){
            $showedAttr[]= 'NAS-Port-Type';
        }

        $this->set('showedAttr', $showedAttr);
        $this->view($id, array( 'Authentication type' => 'Login/Pwd' ));
    }

    /**
     * View a user with login/password.
     * @param  $id - user id
     */
    public function view_phone($id = null) {
        $showedAttr = array(
            'Authentication type',
            'Username',
            'Comment',
            'Expiration',
            'Simultaneous-Use',
            'Groups',
            'Cisco',
            'MAC address',
            'EAP-Type',
            'Server certificate path',
        );

        $raduser = $this->Raduser->findById($id);

        $this->set('showedAttr', $showedAttr);
        $this->view($id, array( 'Authentication type' => 'Login/Pwd' ));
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
     */
    private function add($success) {
        if($this->request->is('post')){
            if ($success) {

                if (isset($this->request->data['Raduser']['is_cert'])
                    && $this->request->data['Raduser']['is_cert'] == 1
                ) {
                    $username = $this->request->data['Raduser']['username'];
                    $cert = Utils::getUserCertsPath($username);

                    $this->Session->setFlash(
                        __('New user added. His certificate is in '),
                        'flash_success_link',
                        array(
                            'title' => $cert,
                            'url' => array(
                                'controller' => 'certs',
                                'action' => 'get_cert/' . $username,
                            ),
                            'style' => array(
                                'class' => '',
                                'escape' => false,
                                'style' => '',
                            ),
                        )
                    );

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
        $this->set('roles', $this->Raduser->roles);
    }

    /**
     * Add check lines if cisco checkbox is checked or MAC address typed.
     * @param $checks - array of radchecks lines
     */
    private function setCommonCiscoMacFields(&$checks=array()) {
        if (isset($this->request->data['Raduser']['username'])) {
            $username = $this->request->data['Raduser']['username'];
        } else if (isset($this->request->data['Raduser']['id'])) {
            $this->Raduser->id = $this->request->data['Raduser']['id'];
            $username = $this->Raduser->field('username');
        }

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

            if($nasPortType == 'both'){
                $nasPortTypeRegexp = 'Async|Virtual|Ethernet';
            } else {
                $nasPortTypeRegexp = $nasPortType . '|Ethernet';
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
            $checks[$nasPortTypeIndex] = array($username, 'NAS-Port-Type', '=~', 'Ethernet');
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
                        '=~',
                        'Ethernet|Wireless-802.11',
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

    public function add_phone(){
        $success = false;

        if ($this->request->is('post')) {
            try {
                $username = $this->request->data['Raduser']['username'];

                $this->request->data['Raduser']['is_phone'] = 1;

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
                        '=~',
                        'Ethernet|Wireless-802.11',
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
                $this->createCertificate(
                    -1,
                    $username,
                    array(
                        $this->request->data['Raduser']['country'],
                        $this->request->data['Raduser']['province'],
                        $this->request->data['Raduser']['locality'],
                        $this->request->data['Raduser']['organization'],
                    )
                );

                $rads = array(
                    array(
                        $username,
                        'NAS-Port-Type',
                        '=~',
                        'Ethernet|Wireless-802.11',
                    ),
                    array(
                        $username,
                        'EAP-Type',
                        ':=',
                        'EAP-TLS'
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
                $username =
                    Utils::cleanMAC($this->request->data['Raduser']['mac']);

                $this->request->data['Raduser']['username'] = $username;
                $this->request->data['Raduser']['role'] = 'user';

                $this->request->data['Raduser']['is_mac'] = 1;
                $rads = array(
                    array(
                        $username,
                        'NAS-Port-Type',
                        '=~',
                        'Ethernet|Wireless-802.11',
                    ),
                    array(
                        $username,
                        'Cleartext-Password',
                        ':=',
                        $username,
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
            $username = '';
            $Radcheck = new Radcheck();

            // add the admin rights to an existing user
            if(isset($this->request->data['Raduser']['existing_user'])
                && !empty($this->request->data['Raduser']['existing_user'])
            ){
                $user = $this->Raduser->findById($this->request->data['Raduser']['existing_user']);

                if ($user) {
                    $user['Raduser']['role'] = $this->request->data['Raduser']['role'];
                    $username = $user['Raduser']['username'];

                    // change or add the password to the user
                    if (isset($this->request->data['Raduser']['passwd'])) {
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

                    $success = $this->Raduser->save($user, true, array('role'));
                } else {
                    $success = false;
                }
            // create a new user (only admin)
            } else {
                $this->Raduser->create();
                $success = $this->Raduser->save($this->request->data);
            }

            // the user does not exist in radcheck, create it
            if(!$found){
                $Radcheck->create();
                $Radcheck->save(array(
                    'username' => $username,
                    'attribute' => 'Cleartext-Password',
                    'op' => ':=',
                    'value' => $this->request->data['Raduser']['passwd']
                ));
            }

            if($success){
                $this->Session->setFlash(__(
                    'The SNACK user %s was added', $this->request->data['Raduser']['username']),
                'flash_success'
            );
                Utils::userlog(__('added admin user %s', $this->request->data['Raduser']['username']), 'error');
                $this->redirect(array('action' => 'index'));

            } else {
                $this->Session->setFlash(
                    'Unable to add the SNACK user',
                    'flash_error'
                );
                Utils::userlog(__('error while adding a SNACK user'), 'error');
            }
        }

        $users = $this->Raduser->find(
            'all',
            array(
                'fields' => array(
                    '*',
                    '(SELECT attribute from radcheck where username=Raduser.username and attribute=\'Cleartext-Password\' limit 1) as pwd',
                ),
                'conditions' => array(
                    'Raduser.role' => 'user',
                ),
            )
        );

        $values = array();
        foreach ($users as $u) {
            if ($this->Checks->getType($u['Raduser'], false) != 'mac') {
                $values[]= array(
                    'name' => $u['Raduser']['username'],
                    'value' => $u['Raduser']['id'],
                    'data-pwd' => is_null($u[0]['pwd']),
                );
            }
        }
        $this->set('users', $values);
        $this->set('roles', $this->Raduser->roles);
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

        if ($this->Raduser->field('is_mac')) {
            $this->set(
                'username',
                Utils::formatMAC($this->Raduser->field('username'))
            );
        } else {
            $this->set('username', $this->Raduser->field('username'));
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

        if (isset($this->request->data['Raduser']['expiration_date'])) {
            $this->request->data['Raduser']['expiration_date'] = Utils::formatDate(
                $this->request->data['Raduser']['expiration_date'],
                'syst'
            );
        }

        // Radreply
        $this->Checks->restoreCommonReplyFields(
            $this->Raduser->id,
            $this->request
        );

        // MAC or Cisco properties for active users
        if((isset($this->request->data['Raduser']['is_cisco'])
            && $this->request->data['Raduser']['is_cisco'])
            || (isset($this->request->data['Raduser']['is_mac'])
            && $this->request->data['Raduser']['is_mac'])
        ) {
            $this->Checks->restoreCommonCiscoMacFields(
                $this->Raduser->id,
                $this->request
            );
        }

        $this->set('roles', $this->Raduser->roles);
    }

    public function edit_loginpass($id = null) {
        $this->Raduser->id = $id;
        $success = false;

	foreach ($this->Checks->getChecks($id) AS $check) {
	    if($check['Radcheck']['attribute'] == 'EAP-Type')
		$ttls = ($check['Radcheck']['value'] == 'EAP-TTLS') ? 1 : 0;
	}

        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();
	    $this->request->data['Raduser']['ttls'] = $ttls;
        } else {
            try {
                $this->request->data['Raduser']['is_loginpass'] = 1;

                $checksCiscoMac = $this->setCommonCiscoMacFields();

                if (!$this->Raduser->save($this->request->data)){
                    throw new EditException(
                        'Raduser',
                        $id,
                        $this->Raduser->field('username')
                    );
                }

                // update radchecks fields
                $checkClassFields = array(
                    'Cleartext-Password' =>
                    $this->request->data['Raduser']['passwd'],
                );

                foreach ($checksCiscoMac as $c) {
                    $checkClassFields[$c[1]] = $c[3];
                }

                if (isset($this->request->data['Raduser']['ttls'])
                    && $this->request->data['Raduser']['ttls'] == 1) {
			$checkClassFields['EAP-Type'] = 'EAP-TTLS';
                    } else {
			$checkClassFields['EAP-Type'] = 'MD5-CHALLENGE';
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

    public function edit_phone($id = null) {
        $this->Raduser->id = $id;
        $success = false;

        foreach ($this->Checks->getChecks($id) AS $check) {
            if($check['Radcheck']['attribute'] == 'EAP-Type')
                $ttls = ($check['Radcheck']['value'] == 'EAP-TTLS') ? 1 : 0;
        }

        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();
            $this->request->data['Raduser']['ttls'] = $ttls;
        } else {
            try {
                $this->request->data['Raduser']['is_phone'] = 1;

                $checksCiscoMac = $this->setCommonCiscoMacFields();

                if (!$this->Raduser->save($this->request->data)){
                    throw new EditException(
                        'Raduser',
                        $id,
                        $this->Raduser->field('username')
                    );
                }

                // update radchecks fields
                $checkClassFields = array(
                    'Cleartext-Password' =>
                    $this->request->data['Raduser']['passwd'],
                );

                foreach ($checksCiscoMac as $c) {
                    $checkClassFields[$c[1]] = $c[3];
                }

                if (isset($this->request->data['Raduser']['ttls'])
                    && $this->request->data['Raduser']['ttls'] == 1) {
                        $checkClassFields['EAP-Type'] = 'EAP-TTLS';
                    } else {
                        $checkClassFields['EAP-Type'] = 'MD5-CHALLENGE';
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
                Utils::userlog(__('error while editing phone user %s', $this->Raduser->id), 'error');
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

                if(!$this->Raduser->save($this->request->data)){
                    throw new EditException(
                        'Raduser',
                        $id,
                        $this->Raduser->field('username')
                    );
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
            $this->request->data['Raduser']['was_user'] = $this->request->data['Raduser']['role'] === 'user';
        } else {
            try {
                $this->request->data['Raduser']['is_cert'] = 1;

                $checksCiscoMac = $this->setCommonCiscoMacFields();

                if(!$this->Raduser->save($this->request->data)){
                    throw new EditException(
                        'Raduser',
                        $id,
                        $this->Raduser->field('username')
                    );
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
                    throw new EditException(
                        'Raduser',
                        $id,
                        $this->Raduser->field('username')
                    );
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
        $this->set('username', $this->Raduser->field('username'));
        $this->set('roles', $this->Raduser->roles);
    }

    public function delete ($id = null) {
        $alreadyFlashed = false;
        try {
            if($this->request->is('get')){
                throw new MethodNotAllowedException();
            }

            $id = is_null($id) ? $this->request->data['Raduser']['id'] : $id;
            $this->Raduser->id = $id;

            // Revoke and remove certificate for user
            if ($this->Raduser->field('is_cert') == 1) {
                //TODO: gérer les exceptions
                try {
                    $this->removeCertificate(
                        $id,
                        $this->Raduser->field('username')
                    );
                } catch(CertificateNotFoundException $e){
                    $this->Session->setFlash(
                        $e->getMessage(),
                        'flash_warning'
                    );
                    Utils::userlog(__('warning: while deleting user %s, certificate files not found!', $id), 'error');
                    $alreadyFlashed = true;
                }
            }

            $this->Checks->delete($this->request, $id);
            Utils::userlog(__('deleted user %s', $id));

            if(! $alreadyFlashed) {
                $this->Session->setFlash(
                    __('The user has been deleted.'),
                    'flash_success'
                );
            }
        } catch(Exception $e) {
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
    public function createCertificate($userID, $username, $params=array()) {
        if (!empty($params) && count($params) == 4 && is_array($params)) {
            $command = Configure::read('Parameters.scriptsPath')
                . '/createCertificate '
                . '"' . Configure::read('Parameters.certsPath') . '" '
                . '"' . $username. '" '
                . '"' . Configure::read('Parameters.countryName') . '" '
                . '"' . Configure::read('Parameters.stateOrProvinceName') . '" '
                . '"' . Configure::read('Parameters.localityName') . '" '
                . '"' . Configure::read('Parameters.organizationName') . '" ';
        } else {
            $command = Configure::read('Parameters.scriptsPath')
                . '/createCertificate '
                . '"' . Configure::read('Parameters.certsPath') . '" '
                . '"' . $username. '" '
                . '"' . $params[0] . '" '
                . '"' . $params[1] . '" '
                . '"' . $params[2] . '" '
                . '"' . $params[3] . '" ';
        }

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
        $cert = Utils::getUserCertsPath($username);

        if (file_exists($cert)) {
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
            if (!unlink($cert)) {
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
