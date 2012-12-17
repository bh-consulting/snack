<?php

App::import('Model', 'Radcheck');
App::import('Model', 'Radgroup');
App::import('Model', 'Radusergroup');
class RadusersController extends AppController
{
    public $helpers = array('Html', 'Form');
    public $components = array(
        'Checks' => array(
            'displayName' => 'username',
            'baseClass' => 'Raduser',
            'checkClass' => 'Radcheck'
            ),
        'Session');

    public function index(){
        $this->set('radusers', $this->Checks->index());
    }

    public function view($id = null){
        $views = $this->Checks->view($id);
        $this->set('raduser', $views['base']);
        $this->set('radchecks', $views['checks']);
    }

    public function view_cisco($id = null) {
        $this->view($id);
    }

    public function view_cert($id = null) {
        $this->view($id);
    }

    public function view_loginpass($id = null) {
        $this->view($id);
    }

    public function view_mac($id = null) {
        $this->view($id);
    }

    public function add($success){
        if($this->request->is('post')){
            if($success){
                if(array_key_exists('cert_path', $this->request->data['Raduser']))
                    $this->Session->setFlash('New user added. Certificate in ' . $this->request->data['Raduser']['cert_path']);
                else
                    $this->Session->setFlash('New user added.');

                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->flash('Unable to add user.', 'error');
            }
        }
        $groups = new Radgroup();
        $this->set('groups', $groups->find('list', array('fields' => array('groupname'))));
    }

    public function add_cisco(){
        $success = false;
        if ($this->request->is('post')) {
            $name = $this->request->data['Raduser']['username'];
            $this->request->data['Raduser']['is_cisco'] = 1;
            $checks = array(
                array($name,
                    'NAS-Port-Type',
                    '==',
                    $this->request->data['Raduser']['nas-port-type']
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
                )
            );

            $success = $this->Checks->add($this->request, $checks);

            // TODO: add a cisco user with $this->request->data['Raduser']['username']/ $this->request->data['Raduser']['password']
        }
        $this->add($success);
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
                )
            );
            $success = $this->Checks->add($this->request, $rads);
        }
        $this->add($success);
    }

    public function add_mac(){
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

    public function add_cert(){
        $success = false;
        if ($this->request->is('post')) {

            $name = $this->request->data['Raduser']['username'
          ];
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
                    'EAP-TTLS'
                )
            );
            $success = $this->Checks->add($this->request, $rads);

            // TODO: generate a certificate
        }
        $this->add($success);
    }

    public function edit_cisco($id = null){
        $this->Raduser->id = $id;
        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();

            // restore values from radchecks
            $this->Checks->restore_common_check_fields($id, $this->request, $cisco=true);

            $result = $this->request;
        } else {

            if ($this->Raduser->save($this->request->data)) {

                // update radchecks fields
                $checkClassFields = array(
                    'NAS-Port-Type' => $this->request->data['Raduser']['nas-port-type'],
                    'Cleartext-Password' => $this->request->data['Raduser']['password']);
                $this->Checks->update_radcheck_fields($id, $this->request, $checkClassFields);
                $this->update_groups($this->Raduser->id, $this->request);

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
            $this->Checks->restore_common_check_fields($id, $this->request);
            $result = $this->request;
        } else {

            if ($this->Raduser->save($this->request->data)) {
                // update radchecks fields
                $checkClassFields = array('Cleartext-Password' => $this->request->data['Raduser']['password']);
                $this->Checks->update_radcheck_fields($id, $this->request, $checkClassFields);

                $this->update_groups($this->Raduser->id, $this->request);

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
            $this->Checks->restore_common_check_fields($id, $this->request);
            $result = $this->request;
        } else {

            if ($this->Raduser->save($this->request->data)) {
                $this->update_groups($this->Raduser->id, $this->request);
                $this->Checks->update_radcheck_fields($id, $this->request);
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
            $this->Checks->restore_common_check_fields($id, $this->request);
            $result = $this->request;
        } else {

            if ($this->Raduser->save($this->request->data)) {
                $this->update_groups($this->Raduser->id, $this->request);
                $newCert = ($this->request->data['Raduser']['cert_gen'] == 1);

                $this->Checks->update_radcheck_fields($id, $this->request);
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
                $this->Session->setFlash('User has been updated.');
                //$this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to update user.', 'flash_error');
            }
        } else{
            $this->request = $result;
        }

        $groups = new Radgroup();
        $this->set('groups', $groups->find('list', array('fields' => array('groupname'))));
        $this->restore_groups($this->Raduser->id);
    }

    public function delete($id = null){

        $success = $this->Checks->delete($this->request, $id);
        if($success){
            $this->Session->setFlash('The user with id:' . $id . ' has been deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Unable to delete user with id:' . $id . ' has been deleted.', 'flash_error');
        }
    }

    public function getUserGroups($id)
    {
        $this->Raduser->id = $id;
        $username = $this->Raduser->field('username');
        $Radusergroup = new Radusergroup();
        $groups = $Radusergroup->findAllByUsername($username);
        return $groups;
    }



    public function restore_groups($id)
    {
        $Radgroup = new Radgroup();
        $groups = $this->getUserGroups($id);
        if(!empty($groups)){
            $groupsId = array();
            foreach ($groups as $usergroup) {
                $g = $Radgroup->findByGroupname($usergroup['Radusergroup']['groupname']);
                $groupsId[]= $g['Radgroup']['id'];
            }
            $this->set('groups_selected', $groupsId);
        }
    }

    public function update_groups($id, $request){
        $Radgroup = new Radgroup();
        $groups = $this->getUserGroups($id);
        $groupsToAdd = array();
        $groupsToDelete = array();

        print_r($request->data['Raduser']['groups']);
        // remove deleted groups
        foreach($groups as $group){
            $found = false;
            $radgroup = $Radgroup->findByGroupname($group['Radusergroup']['groupname']);
            foreach($request->data['Raduser']['groups'] as $requestGroup){
                if($radgroup['Radgroup']['id'] == $requestGroup)
                    $found = true;
            }

            if(!$found){
                $groupsToDelete[]= $radgroup['Radgroup']['id'];
            }
        }
        $this->Checks->deleteGroups($id, $groupsToDelete);

        // add new groups
        foreach($request->data['Raduser']['groups'] as $requestGroup){
            $found = false;
            foreach($groups as $group){
                $radgroup = $Radgroup->findByGroupname($group['Radusergroup']['groupname']);
                if($radgroup['Radgroup']['id'] == $requestGroup)
                    $found = true;
            }

            if(!$found){
                echo 'add ' . $requestGroup;
                $groupsToAdd[]= $requestGroup;
            }
        }
        $this->Checks->addGroups($id, $groupsToAdd);

    }
}

?>
