<?php

App::import('Model', 'Radcheck');
App::import('Model', 'Radgroup');
App::import('Model', 'Radusergroup');

/**
 * Controller to handle user management: add, update, remove users.
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

    /**
     * Display users list.
     * Manage multiple delete/export actions.
     */
    public function index() {
        // Multiple delete/export
        if ($this->request->is('post')) {
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

        $radusers = $this->paginate('Raduser');

        foreach ($radusers as &$r) {
            $r['Raduser']['ntype'] = $this->Checks->getType($r['Raduser'], true);
            $r['Raduser']['type'] = $this->Checks->getType($r['Raduser'], false);

            if( $r['Raduser']['type'] == "mac" ) {
                $r['Raduser']['username'] = Utils::formatMAC(
                    $r['Raduser']['username']
                );
            }
        }

        $this->set('radusers', $radusers);

        // FIXME: should not be here, DRY
        $this->set('sortIcons', array(
            'asc' => 'icon-chevron-down',
            'desc' => 'icon-chevron-up')
        );
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
        $attributes['Certificate path'] =
            $views['base']['Raduser']['cert_path'];
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
            )
        );
        $this->view($id, array( 'Authentication type' => 'MAC address' ));
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
                        'New user added. His certificates were %s and %s.',
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
    private function addCommonCiscoMacFields(&$checks) {
        $username = $this->request->data['Raduser']['username'];

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
            // TODO: delete next line if it's not usefull.
            // $this->Raduser->data['Raduser']['is_cisco'] = 1;

            $nasPortType = $this->request->data['Raduser']['nas-port-type'];

            //TODO: a priori quand on va changer de both vers console/vty, on va ajouter un type de port mais pas supprimer l'autre...
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
        }

        // add radchecks for mac auth
        if(isset($this->request->data['Raduser']['mac_active'])) {
            $mac = $this->request->data['Raduser']['mac_active'];
            $mac = str_replace(':', '', $mac);
            $mac = str_replace('-', '', $mac);
            $checks[]= array(
                $username,
                'Calling-Station-Id',
                '==',
                $mac,
            );
            $this->request->data['Raduser']['is_mac'] = 1;
        }
    }

    public function add_loginpass(){
        $success = false;

        if ($this->request->is('post')) {
            try {
                $username = $this->request->data['Raduser']['username'];

                $this->request->data['Raduser']['is_loginpass'] = 1;

                if ($this->request->data['Raduser']['ttls'] == 1) {
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
                $this->addCommonCiscoMacFields($rads);
                $this->Checks->add($this->request, $rads);

                $success = true;
            } catch(UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );

                $success = false;
            }
        }

        $this->add($success);
    }


    // TODO: delete if mac active is not supported
    // public function add_mac_active() {
    //     $success = false;

    //     if ($this->request->is('post')) {
    //         try {
    //             $this->request->data['Raduser']['mac'] = 
    //                 Utils::cleanMAC($this->request->data['Raduser']['mac']);

    //             $username = $this->request->data['Raduser']['username'];
    //             $this->request->data['Raduser']['is_mac'] = 1;
    //             $rads = array(
    //                 array(
    //                     $username,
    //                     'NAS-Port-Type',
    //                     '==',
    //                     '15'
    //                 ),
    //                 // FIXME to test
    //                 // array($username,
    //                 //     'Cleartext-Password',
    //                 //     ':=',
    //                 //     $this->request->data['Raduser']['mac']
    //                 // ),
    //                 // array($username,
    //                 //     'EAP-Type',
    //                 //     ':=',
    //                 //     'MD5-CHALLENGE'
    //                 // ),
    //                 array(
    //                     $username,
    //                     'Calling-Station-Id',
    //                     '==',
    //                     $this->request->data['Raduser']['mac'],
    //                 ),
    //             );

    //             $this->addCommonCiscoMacFields($rads);
    //             $this->Checks->add($this->request, $rads);

    //             $success = true;
    //         } catch(UserGroupException $e) {
    //             $this->Session->setFlash(
    //                 $e->getMessage(),
    //                 'flash_error'
    //             );

    //             $success = false;
    //         }
    //     }

    //     $this->add($success);
    // }

    public function add_cert(){
        $success = false;

        if ($this->request->is('post')) {
            try {
                $username = $this->request->data['Raduser']['username'];
                $this->request->data['Raduser']['is_cert'] = 1;

                // Create certificate
                $this->createCertificate(-1, $username);

                $this->request->data['Raduser']['cert_path'] = 
                    Configure::read('Parameters.certsPath')
                    . '/users/' . $username . '_';

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
                $this->addCommonCiscoMacFields($rads);
                $this->Checks->add($this->request, $rads);

                $success = true;
            } catch(UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );

                $success = false;
            }
        }

        $this->add($success);
    }

    public function add_mac_passive(){
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

                $success = false;
            }
        }

        $this->add($success);
    }

    private function edit($success) {
        if ($this->request->is('post')) {
            if ($success) {
                $this->Session->setFlash(
                    __('User has been updated.'),
                    'flash_success');

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
    }

    // public function edit_cisco($id = null) {
    //     $this->Raduser->id = $id;
    //     $success = false;

    //     if ($this->request->is('get')) {
    //         $this->request->data = $this->Raduser->read();
    //     } else {
    //         try {
    //             $this->Raduser->save($this->request->data);

    //             // update radchecks fields
    //             $checkClassFields = array(
    //                 'NAS-Port-Type' => 
    //                 $this->request->data['Raduser']['nas-port-type'],
    //                 'Cleartext-Password' =>
    //                 $this->request->data['Raduser']['passwd']
    //             );
    //             $this->Checks->updateRadcheckFields(
    //                 $id,
    //                 $this->request,
    //                 $checkClassFields
    //             );
    //             $this->updateGroups($this->Raduser->id, $this->request);

    //             $success = true;
    //         } catch(UserGroupException $e) {
    //             $this->Session->setFlash(
    //                 $e->getMessage(),
    //                 'flash_error'
    //             );
    //         }
    //     }

    //     $this->edit($success);
    // }

    public function edit_loginpass($id = null) {
        $this->Raduser->id = $id;
        $success = false;

        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();
        } else {
            try {
                $this->Raduser->save($this->request->data);

                // update radchecks fields
                $checkClassFields = array(
                    'Cleartext-Password' =>
                    $this->request->data['Raduser']['passwd']
                );
                $this->Checks->updateRadcheckFields(
                    $id,
                    $this->request,
                    $checkClassFields
                );

                $this->updateGroups($this->Raduser->id, $this->request);

                $success = true;
            } catch(UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );
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
                $this->Raduser->save($this->request->data);

                $this->updateGroups($this->Raduser->id, $this->request);
                $this->Checks->updateRadcheckFields($id, $this->request);
                $this->Checks->updateRadreplyFields($id, $this->request);

                $success = true;
            } catch(UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );
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
                $this->Raduser->save($this->request->data);

                $this->updateGroups($this->Raduser->id, $this->request);
                $this->Checks->updateRadcheckFields($id, $this->request);

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
            }
        }

        $this->edit($success);
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

            $this->Session->setFlash(
                __('The user with id #%d has been deleted.', $id),
                'flash_success'
            );
        } catch(UserGroupException $e) {
            $this->Session->setFlash(
                $e->getMessage(),
                'flash_error'
            );
        }

        $this->redirect(array('action' => 'index'));
    }

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
