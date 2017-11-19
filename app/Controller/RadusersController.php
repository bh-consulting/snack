<?php

App::uses('AppController', 'Controller');
App::import('Model', 'Radcheck');
App::import('Model', 'Radgroup');
App::import('Model', 'Radusergroup');
App::import('Model', 'Radreply');

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
    public $uses = array('Radacct', 'Raduser');

    public function isAuthorized($user) {

        // All registered user can view users
        if (in_array($this->action, array(
                    'index', 'view_mac', 'view_cert', 'view_loginpass',
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

    public function beforeValidateForFilters() {
        unset($this->Raduser->validate['username']['notBlank']['required']);
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
    
    public function getOldUsers($args = array()) {
        if (!empty($args['input'])) {
            $data = &$this->request->data['Raduser'][$args['from']];
            if (isset($data) && $data != '') {
                return "(username NOT IN (SELECT username from radacct where acctstarttime>'$data'))";
            } else {
                return '(1=1)';
            }
        }
    }

    /**
     * method to display a warning field to restart the server after Nas changes
     */
    public function alert_restart_server(){
        $this->Session->setFlash(
            __('You HAVE to restart the Radius server to apply NAS changes!'),
            'flash_error_link',
            array(
                'title' => __('Restart Freeradius') . ' <i class="icon-refresh icon-white"></i>',
                'url' => array(
                    'controller' => 'systemDetails',
                    'action' => 'restart/freeradius',
                ),
                'style' => array(
                    'class' => 'btn btn-danger btn-mini',
                    'escape' => false,
                    'style' => 'margin-left: 15px;'
                ),
            )
        );
        Utils::userlog(__('restarted the Radius server'));
    }

    /**
     * Get Vlan for user
     */
    public function getVLAN($username) {
        $reply = new Radreply();
        $vlan = $reply->find('all', array(
            'conditions' => array('Radreply.username' => $username,
                'Radreply.attribute' => 'Tunnel-Private-Group-Id'),
            'fields' => array('Radreply.value'),
        ));
        if (count($vlan) > 0) {
            return array($vlan[0]['Radreply']['value'], "");
        } else {
            $radusergroup = new Radusergroup();
            /*$group = $radusergroup->find('all', array(
                'conditions' => array('Radusergroup.username' => $username,
                    )
             ))*/
            $group = $radusergroup->query('select * from radusergroup where radusergroup.priority=(select min(radusergroup.priority) from radusergroup where radusergroup.username="'.$username.'") and radusergroup.username="'.$username.'"');
            if (count($group) > 0) {
                //debug($group[0]['radusergroup']['groupname']);
                $groupname = $group[0]['radusergroup']['groupname'];
                $radgroupreply = new Radgroupreply();
                $vlan = $radgroupreply->find('all', array(
                    'conditions' => array('Radgroupreply.groupname' => $groupname,
                        'Radgroupreply.attribute' => 'Tunnel-Private-Group-Id'),
                    'fields' => array('Radgroupreply.value'),
                ));
                if (count($vlan) > 0) {
                    return array($vlan[0]['Radgroupreply']['value'], $groupname);
                }
                else {
                    return array("x", "");
                }
            }
            else {
                return array("x", "");
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
        } else {
            $path = Configure::read('Parameters.certsPath')."/cacert.cer";
            if (!file_exists($path)) {
                $this->redirect(array('controller' => 'Parameters', 'action' => 'install'));
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
            'fields' => array('is_loginpass', 'is_windowsad', 'is_phone', 'is_mac', 'is_cisco', 'is_cert'),
            'input' => 'authtype',
            'items' => array(
                'is_cisco' => __('Cisco'),
                'is_mac' => __('MAC'),
                'is_phone' => __('Phone'),
                'is_loginpass' => __('Login/Pwd'),
                'is_windowsad' => __('Login/Pwd with ActiveDirectory'),
                'is_phone' => __('Phone'),
                'is_cert' => __('Certificate'),
            ),
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
                'getOldUsers',
                array(
                    'input' => 'old',
                    'from' => 'datefrom',
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
                list($user['Raduser']['vlan'], $user['Raduser']['group']) = $this->getVLAN($user['Raduser']['username']);
                $user['Raduser']['ntype'] = $this->Checks->getType(
                        $user['Raduser'], true
                );

                $user['Raduser']['type'] = $this->Checks->getType(
                        $user['Raduser'], false
                );
            }

            if (!empty($userList)) {
                $radcheck = new Radcheck();
                $expirations = $radcheck->find(
                        'list', array(
                    'fields' => array('username', 'value'),
                    'conditions' => array(
                        'username REGEXP' => '^(' . implode($userList, '|') . ')$',
                        'attribute' => 'Expiration',
                    )
                        )
                );
                foreach ($radusers as &$user) {
                    if (isset($expirations[$user['Raduser']['username']]) && Utils::formatDate(
                                    array(
                                $expirations[$user['Raduser']['username']],
                                date('d M Y H:i:s')
                                    ), 'dursign') >= 0
                    ) {
                        $user['Raduser']['expiration'] = Utils::formatDate(
                                        $expirations[$user['Raduser']['username']], 'display'
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
                        __('Users have been deleted.'), 'flash_success'
                );
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                        $e->getMessage(), 'flash_error'
                );
                Utils::userlog(__('error while deleting users'), 'error');
            }
        } else {
            $this->Session->setFlash(
                    __('Please, select at least one user!'), 'flash_warning'
            );
        }
    }

    public function import() {
        if ($this->request->isPost()) {
            $handle = fopen($_FILES['data']['tmp_name']['importCsv']['file'], "r");
            $results = array();
            $listradusers = array();
            $col = array();
            $line=0;
            while (($fields = fgetcsv($handle)) != false) {
                if (count($fields) != 7) {
                    $this->Session->setFlash(
                        __('Error number of columns on line %s', $line+1), 'flash_error'
                    );
                    continue;
                }
                //debug($fields);
                $i=0;
                $raduser = array();
                foreach($fields as $field) {
                    $fieldlower = strtolower($field);
                    if ($line == 0) {
                        switch($fieldlower) {
                            case "username":
                            case "password":
                            case "comment":
                            case "vlan":
                            case "cleartext":
                            case "phone":
                            case "cisco_user":
                                $col[$i] = $fieldlower;
                                break;
                        }
                    } else {
                        if ($field != "") {
                            $raduser[$col[$i]]=$field;
                        }
                    }
                    $i++;
                }
                if (count($raduser) > 0) {
                    $listradusers[]=$raduser;
                }
                $line++;
            }

            foreach ($listradusers as $raduser) {
                $usersaved = false;
                $user = new Raduser();
                $user->set('username', $raduser['username']);
                $user->set('comment', $raduser['comment']);
                if ($raduser['phone'] == "1") {
                    $user->set('is_phone', '1');
                } elseif (Utils::isMAC($raduser['username'])) {
                    $user->set('is_mac', '1');
                } else {
                    $user->set('is_loginpass', '1');
                }
                if ($raduser['cisco_user'] == "1") {
                    $user->set('is_cisco', '1');
                }
                $user->set('role', 'user');
                if ($user->save()) {
                    $usersaved = true;
                    $results[] = __('<b><span class="text-success">SUCCESS</span></b> %s', $raduser['username']);
                } else {
                    $results[] = __('<b><span class="text-danger">ERROR</span></b> %s', $raduser['username']);
                }

                $results[] = "<ul>";
                if ($usersaved) {
                    $check = new Radcheck();
                    if ($raduser['cleartext'] == "1" || $raduser['phone'] == "1") {
                        $check->set('username', $raduser['username']);
                        $check->set('attribute', 'Cleartext-Password');
                        $check->set('op', ':=');
                        $check->set('value', $raduser['password']);
                        if ($check->save()) {
                            $results[] = __('<li><b><span class="text-success">SUCCESS</span></b> Cleartext-Password</li>');
                        } else {
                            $results[] = __('<li><b><span class="text-danger">ERROR</span></b> Cleartext-Password');
                        }
                        $check = new Radcheck();
                        $check->set('username', $raduser['username']);
                        $check->set('attribute', 'EAP-Type');
                        $check->set('op', ':=');
                        $check->set('value', 'MD5-CHALLENGE');
                        if ($check->save()) {
                            $results[] = __('<li><b><span class="text-success">SUCCESS</span></b> MD5-CHALLENGE</li>');
                        } else {
                            $results[] = __('<li><b><span class="text-danger">ERROR</span></b> MD5-CHALLENGE');
                        }
                    } else {
                        $check->set('username', $raduser['username']);
                        $check->set('attribute', 'NT-Password');
                        $check->set('op', ':=');
                        $check->set('value', Utils::NTLMHash($raduser['password']));
                        if ($check->save()) {
                            $results[] = __('<li><b><span class="text-success">SUCCESS</span></b> NT-Password</li>');
                        } else {
                            $results[] = __('<li><b><span class="text-danger">ERROR</span></b> NT-Password</li>');
                        }
                    }

                    $check = new Radcheck();
                    $check->set('username', $raduser['username']);
                    $check->set('attribute', 'NAS-Port-Type');
                    $check->set('op', '+=');
                    $check->set('value', 'Ethernet');
                    if ($check->save()) {
                        $results[] = __('<li><b><span class="text-success">SUCCESS</span></b> NAS-Port-Type Ethernet</li>');
                    } else {
                        $results[] = __('<li><b><span class="text-danger">ERROR</span></b> NAS-Port-Type Ethernet</li>');
                    }

                    $check = new Radcheck();
                    $check->set('username', $raduser['username']);
                    $check->set('attribute', 'NAS-Port-Type');
                    $check->set('op', '+=');
                    $check->set('value', 'Wireless-802.11');
                    if ($check->save()) {
                        $results[] = __('<li><b><span class="text-success">SUCCESS</span></b> NAS-Port-Type Wireless-802.11</li>');
                    } else {
                        $results[] = __('<li><b><span class="text-danger">ERROR</span></b> NAS-Port-Type Wireless-802.11</li>');
                    }

                    if (isset($raduser['vlan'])) {
                        $reply = new Radreply();
                        $reply->set('username', $raduser['username']);
                        $reply->set('attribute', 'Tunnel-Type');
                        $reply->set('op', ':=');
                        $reply->set('value', 'VLAN');
                        if ($reply->save()) {
                            $results[] = __('<li><b><span class="text-success">SUCCESS</span></b> Tunnel-Type VLAN</li>');
                        } else {
                            $results[] = __('<li><b><span class="text-danger">ERROR</span></b> Tunnel-Type VLAN</li>');
                        }

                        $reply = new Radreply();
                        $reply->set('username', $raduser['username']);
                        $reply->set('attribute', 'Tunnel-Private-Group-Id');
                        $reply->set('op', ':=');
                        $reply->set('value', $raduser['vlan']);
                        if ($reply->save()) {
                            $results[] = __('<li><b><span class="text-success">SUCCESS</span></b> VLAN %s</li>', $raduser['vlan']);
                        } else {
                            $results[] = __('<li><b><span class="text-danger">ERROR</span></b> VLAN %s</li>', $raduser['vlan']);
                        }
                    }

                    if ($raduser['cisco_user'] == "1") {
                        $reply = new Radreply();
                        $reply->set('username', $raduser['username']);
                        $reply->set('attribute', 'Cisco-AVPair');
                        $reply->set('op', '=');
                        $reply->set('value', 'device-traffic-class=voice');
                        if ($reply->save()) {
                            $results[] = __('<li><b><span class="text-success">SUCCESS</span></b> Voice phone attribute</li>');
                        } else {
                            $results[] = __('<li><b><span class="text-danger">ERROR</span></b> Voice phone attribute</li>');
                        }
                    }
                }
                $results[] = "</ul>";
            }
            $this->set('results', $results);   
        }
    }

    public function exportAll() {
        $ids = $this->Raduser->find('list', array('fields' => 'id'));

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
                        __('Users have been exported.'), 'flash_success'
                );
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                        $e->getMessage(), 'flash_error'
                );
            }
        } else {
            $this->Session->setFlash(
                    __('Please, select at least one user!'), 'flash_warning'
            );
        }
    }

    /**
     * Display a user and its attributes, depending on its type.
     *
     * @param  $id - user id
     * @param  $type - type of the user to display
     */
    public function view($id = null, $type = array()) {
        $views = $this->Checks->view($id);

        $views['base']['Raduser']['ntype'] = $this->Checks->getType($views['base']['Raduser'], true);
        $views['base']['Raduser']['type'] = $this->Checks->getType($views['base']['Raduser'], false);
        
        $this->set('raduser', $views['base']);
        $this->set('radchecks', $views['checks']);
        $this->set('radgroups', $views['groups']);

        $attributes = $type;
        $username = $views['base']['Raduser']['username'];

        // Raduser
        if ($views['base']['Raduser']['type'] == 'mac' && strlen($username) == 12
        ) {
            $attributes['MAC address'] = Utils::formatMAC($username);
        } else {
            $attributes['Username'] = $username;
        }
        $attributes['Comment'] = $views['base']['Raduser']['comment'];
        $attributes['Cisco'] = $views['base']['Raduser']['is_cisco'] ? __('Yes') : __('No');

        // Radchecks
        foreach ($views['checks'] as $check) {
            $attributes[$check['Radcheck']['attribute']] = $check['Radcheck']['value'];
            if ($check['Radcheck']['attribute'] == 'Calling-Station-Id') {
                $attributes['MAC address'] = Utils::formatMAC(
                                $check['Radcheck']['value']);
            }
            if ($check['Radcheck']['attribute'] == 'Cleartext-Password') {
                $attributes['Cleartext-Password'] = true;
            }
        }

        // Radgroups
        $groups = array();
        foreach ($views['groups'] as $group) {
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
            'Server certificate cer path',
            'Expiration',
            'Simultaneous-Use',
            'Groups',
            'Cisco',
            'MAC address',
        );
        $raduser = $this->Raduser->findById($id);
        if ($raduser['Raduser']['is_cisco']) {
            $showedAttr[] = 'NAS-Port-Type';
        }
        $this->set('showedAttr', $showedAttr);
        $this->view($id, array('Authentication type' => 'Certificate'));
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
            'Cleartext-Password',
            'MAC address',
            'EAP-Type',
        );

        $raduser = $this->Raduser->findById($id);
        if ($raduser['Raduser']['is_cisco']) {
            $showedAttr[] = 'NAS-Port-Type';
        }

        $this->set('showedAttr', $showedAttr);
        $this->view($id, array('Authentication type' => 'Login/Pwd'));
    }

    /**
     * View a user with windows ad.
     * @param  $id - user id
     */
    public function view_windowsad($id = null) {
        $this->set(
                'showedAttr', array(
            'Authentication type',
            'Username',
            'Comment',
            'Expiration',
            'Simultaneous-Use',
            'Groups',
                )
        );
        $this->view($id, array('Authentication type' => 'MAC address'));
    }

    /**
     * View a phone
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
            'Server certificate cer path',
        );

        $raduser = $this->Raduser->findById($id);

        $this->set('showedAttr', $showedAttr);
        $this->view($id, array('Authentication type' => 'Login/Pwd'));
    }

    /**
     * View a user with mac address.
     * @param  $id - user id
     */
    public function view_mac($id = null) {
        $this->set(
                'showedAttr', array(
            'Authentication type',
            'MAC address',
            'Comment',
            'Expiration',
            'Simultaneous-Use',
            'Groups',
                )
        );
        $this->view($id, array('Authentication type' => 'MAC address'));
    }

    /**
     * Display user add form.
     *
     * @param $success - determine if user was well added.
     */
    private function add($success) {
        if ($this->request->is('post')) {
            if ($success) {

                if (isset($this->request->data['Raduser']['is_cert']) && $this->request->data['Raduser']['is_cert'] == 1
                ) {
                    $username = $this->request->data['Raduser']['username'];
                    $cert = Utils::getUserCertsPath($username);

                    $this->Session->setFlash(
                            __('New user added. His certificate is in '), 'flash_success_link', array(
                        'title' => $cert,
                        'url' => array(
                            'controller' => 'certs',
                            'action' => 'get_cert_user/' . $username . '/p12',
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
                            __('New user added.'), 'flash_success'
                    );
                }
                Utils::userlog(__('added user %s', $this->Raduser->id));
                $this->redirect(array('action' => 'index'));
            }
        }

        // Radgroup
        $groups = new Radgroup();
        $this->set(
                'groups', $groups->find('list', array('fields' => array('groupname')))
        );
    }

    /**
     * Add check lines if cisco checkbox is checked or MAC address typed.
     * @param $checks - array of radchecks lines
     */
    private function setCommonCiscoMacFields(&$checks = array()) {
        if (isset($this->request->data['Raduser']['username'])) {
            $username = $this->request->data['Raduser']['username'];
        } else if (isset($this->request->data['Raduser']['id'])) {
            $this->Raduser->id = $this->request->data['Raduser']['id'];
            $username = $this->Raduser->field('username');
        }

        // retrieve nas-port-type check
        $nasPortTypeIndex = -1;
        for ($i = 0; $i < count($checks); $i++) {
            if ($checks[$i][1] == 'NAS-Port-Type') {
                $nasPortTypeIndex = $i;
                break;
            }
        }

        // add radchecks for cisco user
        if (isset($this->request->data['Raduser']['cisco']) && $this->request->data['Raduser']['cisco'] == 1) {
            $this->request->data['Raduser']['is_cisco'] = 1;

            $nasPortType = $this->request->data['Raduser']['nas-port-type'];

            if ($nasPortType == 'both') {
                $checks[$nasPortTypeIndex] = array(
                    array(
                        $username,
                        'NAS-Port-Type',
                        '+=',
                        'Async',
                    ),
                    array(
                        $username,
                        'NAS-Port-Type',
                        '+=',
                        'Virtual',
                    ),
                    array(
                        $username,
                        'NAS-Port-Type',
                        '+=',
                        'Ethernet',
                    ),
                    array(
                        $username,
                        'NAS-Port-Type',
                        '+=',
                        'Wireless-802.11',
                    )
                );
            } else {
                $checks[$nasPortTypeIndex] = array(
                    array(
                        $username,
                        'NAS-Port-Type',
                        '+=',
                        $nasPortType,
                    ),
                    array(
                        $username,
                        'NAS-Port-Type',
                        '+=',
                        'Ethernet',
                    ),
                    array(
                        $username,
                        'NAS-Port-Type',
                        '+=',
                        'Wireless-802.11',
                    )
                );
            }

            /*$checks[$nasPortTypeIndex] = array(
                $username,
                'NAS-Port-Type',
                '+=',
                $nasPortTypeRegexp,
            );*/

            if (isset($this->request->data['Raduser']['is_mac']) || isset($this->request->data['Raduser']['is_cert'])) {
                $checks[] = array(
                    $username,
                    'Cleartext-Password',
                    ':=',
                    $this->request->data['Raduser']['passwd'],
                );
            }
        } else {
            //$checks[$nasPortTypeIndex] = array($username, 'NAS-Port-Type', '=~', 'Ethernet|Wireless-802.11');
            $this->request->data['Raduser']['is_cisco'] = 0;
        }

        // add radchecks for mac auth
        if (isset($this->request->data['Raduser']['calling-station-id'])) {
            if (!empty($this->request->data['Raduser']['calling-station-id'])) {
                $mac = Utils::cleanMAC($this->request->data['Raduser']['calling-station-id']);
                $checks[] = array(
                    $username,
                    'Calling-Station-Id',
                    '==',
                    $mac,
                );
                $this->request->data['Raduser']['is_mac'] = 1;
            } else {
                $checks[] = array(
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

    public function add_loginpass() {
        $success = false;

        if ($this->request->is('post')) {
            try {
                $username = $this->request->data['Raduser']['username'];

                $this->request->data['Raduser']['is_loginpass'] = 1;

                /*if (isset($this->request->data['Raduser']['ttls']) && $this->request->data['Raduser']['ttls'] == 1) {
                    $eapType = 'EAP-TTLS';
                } else {
                    $eapType = 'MD5-CHALLENGE';
                }*/

                if (isset($this->request->data['Raduser']['cleartext']) && $this->request->data['Raduser']['cleartext'] == 1) {
                    $rads[] = array(
                        $username,
                        'Cleartext-Password',
                        ':=',
                        $this->request->data['Raduser']['passwd'],
                    );
                } else {
                    $rads[] = array(
                        $username,
                        'NT-Password',
                        ':=',
                        Utils::NTLMHash($this->request->data['Raduser']['passwd']),
                    );
                }
                $rads[] = array(
                    $username,
                    'NAS-Port-Type',
                    '+=',
                    'Ethernet',
                );
                $rads[] = array(
                    $username,
                    'NAS-Port-Type',
                    '+=',
                    'Wireless-802.11',
                );

                $this->setCommonCiscoMacFields($rads);
                $this->Checks->add($this->request, $rads);
                $success = true;
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                        $e->getMessage(), 'flash_error'
                );
                Utils::userlog(__('error while adding a loginpass user'), 'error');
                $success = false;
            }
        }

        $this->add($success);
    }

    public function add_phone() {
        $success = false;
        if ($this->request->is('post')) {
            try {
                if ($this->request->data['Raduser']['is_mac'] == 1) {
                    $username = Utils::cleanMAC($this->request->data['Raduser']['username']);
                    $this->request->data['Raduser']['passwd'] = $username;
                    $this->request->data['Raduser']['confirm_password'] = $username;
                } else {
                    $username = $this->request->data['Raduser']['username'];
                }
                $this->request->data['Raduser']['is_phone'] = 1;

                if (isset($this->request->data['Raduser']['ttls']) && $this->request->data['Raduser']['ttls'] == 1) {
                    $eapType = 'EAP-TTLS';
                } else {
                    $eapType = 'MD5-CHALLENGE';
                }

                $rads = array(
                    array(
                        $username,
                        'NAS-Port-Type',
                        '+=',
                        'Ethernet',
                    ),
                    array(
                        $username,
                        'NAS-Port-Type',
                        '+=',
                        'Wireless-802.11',
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
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                        $e->getMessage(), 'flash_error'
                );
                Utils::userlog(__('error while adding a loginpass user'), 'error');
                $success = false;
            }
        }

        $this->add($success);
    }

    public function add_cert() {
        $success = false;

        if ($this->request->is('post')) {
            try {
                $username = $this->request->data['Raduser']['username'];
                $password = $this->request->data['Raduser']['password'];
                $this->request->data['Raduser']['is_cert'] = 1;

                // Create certificate
                $this->createCertificate(
                        -1, $username, $password, array(
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
                        '+=',
                        'Ethernet',
                    ),
                    array(
                        $username,
                        'NAS-Port-Type',
                        '+=',
                        'Wireless-802.11',
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
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                        $e->getMessage(), 'flash_error'
                );
                Utils::userlog(__('error while adding a cert user'), 'error');

                $success = false;
            }
        }

        $this->add($success);
    }

    public function add_mac() {
        $success = false;

        if ($this->request->is('post')) {
            try {
                $username = Utils::cleanMAC($this->request->data['Raduser']['mac']);

                $this->request->data['Raduser']['username'] = $username;

                $this->request->data['Raduser']['is_mac'] = 1;
                $rads = array(
                    array(
                        $username,
                        'NAS-Port-Type',
                        '+=',
                        'Ethernet',
                    ),
                    array(
                        $username,
                        'NAS-Port-Type',
                        '+=',
                        'Wireless-802.11',
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
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                        $e->getMessage(), 'flash_error'
                );

                Utils::userlog(__('error while adding a MAC user'), 'error');
                $success = false;
            }
        }

        $this->add($success);
    }

    private function edit($success) {
        if ($success) {
            $this->Session->setFlash(
                    __('User has been updated.'), 'flash_success');

            Utils::userlog(__('edited user %s', $this->Raduser->id));
            //debug($this->url);
            $this->redirect(array('action' => 'index'));
            //$this->redirect($this->url);
        }

        if ($this->Raduser->field('is_mac')) {
            $this->set(
                    'username', Utils::formatMAC($this->Raduser->field('username'))
            );
        } else {
            $this->set('username', $this->Raduser->field('username'));
        }

        // Radgroup
        $groups = new Radgroup();
        $this->set(
                'groups', $groups->find('list', array('fields' => array('id', 'groupname')))
        );
        $this->restoreGroups($this->Raduser->id);

        // Radcheck
        $this->Checks->restoreCommonCheckFields(
                $this->Raduser->id, $this->request, $this->Raduser->is_cisco
        );

        if (isset($this->request->data['Raduser']['expiration_date'])) {
            $this->request->data['Raduser']['expiration_date'] = Utils::formatDate(
                            $this->request->data['Raduser']['expiration_date'], 'syst'
            );
        }

        // Radreply
        $this->Checks->restoreCommonReplyFields(
                $this->Raduser->id, $this->request
        );

        // MAC or Cisco properties for active users
        if ((isset($this->request->data['Raduser']['is_cisco']) && $this->request->data['Raduser']['is_cisco']) || (isset($this->request->data['Raduser']['is_mac']) && $this->request->data['Raduser']['is_mac'])
        ) {
            $this->Checks->restoreCommonCiscoMacFields(
                    $this->Raduser->id, $this->request
            );
        }

    }

    public function edit_loginpass($id = null) {
        $this->url = $this->referer();
        $this->Raduser->id = $id;
        $success = false;
        $cleartext=0;
        foreach ($this->Checks->getChecks($id) AS $check) { 
            if ($check['Radcheck']['attribute'] == 'Cleartext-Password') {
                $cleartext=1;
                break;
            }    
        }

        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();
            if (isset($cleartext)) {
                $this->request->data['Raduser']['cleartext'] = $cleartext;
            }
            //$this->request->data['Raduser']['ttls'] = $ttls;
        } else {
            try {
                $this->request->data['Raduser']['is_loginpass'] = 1;

                $checksCiscoMac = $this->setCommonCiscoMacFields();

                if (!$this->Raduser->save($this->request->data)) {
                    throw new EditException(
                    'Raduser', $id, $this->Raduser->field('username')
                    );
                }

                // update radchecks fields
                if ($this->request->data['Raduser']['passwd'] != "") {
                    if (isset($this->request->data['Raduser']['cleartext']) && $this->request->data['Raduser']['cleartext'] == 1) {
                        $checkClassFields = array(
                            'Cleartext-Password' =>
                            $this->request->data['Raduser']['passwd'],
                        );
                    } else {
                        $checkClassFields = array(
                            'NT-Password' =>
                            Utils::NTLMHash($this->request->data['Raduser']['passwd']),
                        );
                    }
                }

                foreach ($checksCiscoMac as $c) {
                    $checkClassFields[$c[1]] = $c[3];
                }

                /*if (isset($this->request->data['Raduser']['ttls']) && $this->request->data['Raduser']['ttls'] == 1) {
                    $checkClassFields['EAP-Type'] = 'EAP-TTLS';
                } else {
                    $checkClassFields['EAP-Type'] = 'MD5-CHALLENGE';
                }*/

                $this->Checks->updateRadcheckFields(
                        $id, $this->request, $checkClassFields
                );

                // update radreply fields
                $this->Checks->updateRadreplyFields($id, $this->request);

                // update group list
                $this->updateGroups($this->Raduser->id, $this->request);

                $success = true;
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                        $e->getMessage(), 'flash_error'
                );
                Utils::userlog(__('error while editing loginpass user %s', $this->Raduser->id), 'error');
                $success = false;
            }
        }

        $this->edit($success);
    }

    public function edit_windowsad($id = null) {
        $this->url = $this->referer();
        $this->Raduser->id = $id;
        $success = false;

        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();
            
            //$this->request->data['Raduser']['ttls'] = $ttls;
        } else {
            try {
                $this->request->data['Raduser']['is_windowsad'] = 1;

                if (!$this->Raduser->save($this->request->data)) {
                    throw new EditException(
                    'Raduser', $id, $this->Raduser->field('username')
                    );
                }

                $this->updateGroups($this->Raduser->id, $this->request);
                $this->Checks->updateRadcheckFields($id, $this->request);
                $this->Checks->updateRadreplyFields($id, $this->request);

                $success = true;
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                        $e->getMessage(), 'flash_error'
                );
                Utils::userlog(__('error while editing MAC user %s', $this->Raduser->id), 'error');
                $success = false;
            }
        }

        $this->edit($success);
    }

    public function edit_phone($id = null) {
        $this->Raduser->id = $id;
        $success = false;

        foreach ($this->Checks->getChecks($id) AS $check) {
            if ($check['Radcheck']['attribute'] == 'EAP-Type')
                $ttls = ($check['Radcheck']['value'] == 'EAP-TTLS') ? 1 : 0;
        }

        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();
            $this->request->data['Raduser']['ttls'] = $ttls;
        } else {
            try {
                $this->request->data['Raduser']['is_phone'] = 1;

                $checksCiscoMac = $this->setCommonCiscoMacFields();

                if (!$this->Raduser->save($this->request->data)) {
                    throw new EditException(
                    'Raduser', $id, $this->Raduser->field('username')
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

                if (isset($this->request->data['Raduser']['ttls']) && $this->request->data['Raduser']['ttls'] == 1) {
                    $checkClassFields['EAP-Type'] = 'EAP-TTLS';
                } else {
                    $checkClassFields['EAP-Type'] = 'MD5-CHALLENGE';
                }

                $this->Checks->updateRadcheckFields(
                        $id, $this->request, $checkClassFields
                );

                // update radreply fields
                $this->Checks->updateRadreplyFields($id, $this->request);

                // update group list
                $this->updateGroups($this->Raduser->id, $this->request);

                $success = true;
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                        $e->getMessage(), 'flash_error'
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
            $this->request->data['Raduser']['username'] = Utils::formatMAC(
                            $this->request->data['Raduser']['username']
            );
        } else {
            try {
                $this->request->data['Raduser']['is_mac'] = 1;

                if (!$this->Raduser->save($this->request->data)) {
                    throw new EditException(
                    'Raduser', $id, $this->Raduser->field('username')
                    );
                }

                $this->updateGroups($this->Raduser->id, $this->request);
                $this->Checks->updateRadcheckFields($id, $this->request);
                $this->Checks->updateRadreplyFields($id, $this->request);

                $success = true;
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                        $e->getMessage(), 'flash_error'
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
            //$this->request->data['Raduser']['was_user'] = $this->request->data['Raduser']['role'] === 'user';
        } else {
            try {
                $this->request->data['Raduser']['is_cert'] = 1;

                $checksCiscoMac = $this->setCommonCiscoMacFields();

                if (!$this->Raduser->save($this->request->data)) {
                    throw new EditException(
                    'Raduser', $id, $this->Raduser->field('username')
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
                            $id, $this->Raduser->field('username'), $password = $this->request->data['Raduser']['password']
                    );
                }

                $success = true;
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                        $e->getMessage(), 'flash_error'
                );
                Utils::userlog(__('error while editing cert user %s', $this->Raduser->id), 'error');
                $success = false;
            }
        }

        $this->edit($success);
    }

    public function delete($id = null) {
        $alreadyFlashed = false;
        try {
            if ($this->request->is('get')) {
                throw new MethodNotAllowedException();
            }
            $id = is_null($id) ? $this->request->data['RaduserDelete']['id'] : $id;
            $this->Raduser->id = $id;

            // Revoke and remove certificate for user
            if ($this->Raduser->field('is_cert') == 1) {
                //TODO: gérer les exceptions
                try {
                    $this->removeCertificate(
                            $id, $this->Raduser->field('username')
                    );
                    $this->alert_restart_server();
                    $alreadyFlashed = true;
                } catch (CertificateNotFoundException $e) {
                    $this->Session->setFlash(
                            $e->getMessage(), 'flash_warning'
                    );
                    Utils::userlog(__('warning: while deleting user %s, certificate files not found!', $id), 'error');
                    $alreadyFlashed = true;
                }
            }

            $this->Checks->delete($this->request, $id);
            Utils::userlog(__('deleted user %s', $id));
            if (!$alreadyFlashed) {
                $this->Session->setFlash(
                    __('The user has been deleted.'), 'flash_success'
                );
            }
        } catch (Exception $e) {
            $this->Session->setFlash(
                    $e->getMessage(), 'flash_error'
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
                $id, array('priority' => 'asc')
        );
        $groups = array();

        foreach ($groupsRecords as $group) {
            $groups[] = $group['Radusergroup']['groupname'];
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
                $id, $request->data['Raduser']['groups']
        );

        if (!is_null($result)) {
            throw new UserGroupAddException(
            $request->data['Raduser']['username'], $result
            );
        }
    }

    /*
     * Generate a certificate.
     * @param username - Identify the user in the certificate (Common Name)
     * 
     * @return 0 if certificate was generated, error code otherwise.
     */

    public function createCertificate($userID, $username, $password, $params = array()) {
        if (!empty($params) && count($params) == 4 && is_array($params)) {
            $command = Configure::read('Parameters.scriptsPath')
                    . '/createCertificate '
                    . '"' . Configure::read('Parameters.certsPath') . '" '
                    . '"' . $username . '" '
                    . '"' . $password . '" '
                    . '"' . $params[0] . '" '
                    . '"' . $params[1] . '" '
                    . '"' . $params[2] . '" '
                    . '"' . $params[3] . '" ';
        } else {
            $command = Configure::read('Parameters.scriptsPath')
                    . '/createCertificate '
                    . '"' . Configure::read('Parameters.certsPath') . '" '
                    . '"' . $username . '" '
                    . '"' . $password . '" '
                    . '"' . Configure::read('Parameters.countryName') . '" '
                    . '"' . Configure::read('Parameters.stateOrProvinceName') . '" '
                    . '"' . Configure::read('Parameters.localityName') . '" '
                    . '"' . Configure::read('Parameters.organizationName') . '" ';
            
        }

        // Create new certificate
        $result = Utils::shell($command);
        Utils::userlog($command);
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
                    . '"' . $username . '" ';

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

    public function renewCertificate($userID, $username, $password) {
        $this->removeCertificate($userID, $username);
        $this->createCertificate($userID, $username, $password);
    }

    /* 
    * List revoked certs
    */

    public function revoked_certs() {
        $this->set('results', $this->Raduser->getRevokedCerts());
    }

    /*
    * Get the template csv for import
    */
    public function downloadcsvtemplate() {
        $this->render('/Radusers/index');
        return $this->response->file(APP."/templates/users-template.csv", array('download' => true, 'name' => "users-template.csv"));
    }
}

?>
