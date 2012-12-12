<?php

App::import('Model', 'Radcheck');
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
                $this->Session->setFlash('Unable to add user.', 'flash_error');
            }
        }
    }

    public function add_cisco(){
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
            $this->add($success);

            // TODO: add a cisco user with $this->request->data['Raduser']['username']/ $this->request->data['Raduser']['password']
        }
    }

    public function add_loginpass(){
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
            $this->add($success);
        }
    }

    public function add_mac(){
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
            $this->add($success);
        }
    }

    public function add_cert(){
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
            $this->add($success);

            // TODO: generate a certificate
        }
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
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to update user.', 'flash_error');
            }
        } else {
            $this->request = $result;
        }
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
}

?>
