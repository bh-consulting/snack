<?php

App::import('Model', 'Radcheck');
App::import('Model', 'Radgroup');
App::import('Model', 'Radusergroup');
class RadusersController extends AppController
{
    public $helpers = array('Html', 'Form', 'JqueryEngine');
    public $paginate = array('limit' => 10, 'order' => array('Raduser.id' => 'asc'));
    public $components = array(
        'Checks' => array(
            'displayName' => 'username',
            'baseClass' => 'Raduser',
            'checkClass' => 'Radcheck'
            ),
        'Session');

    public function index(){
        $radusers = $this->paginate('Raduser');

        foreach ($radusers as &$r) {
            $r['Raduser']['ntype'] = $this->Checks->getType($r['Raduser'], true);
            $r['Raduser']['type'] = $this->Checks->getType($r['Raduser'], false);
        }
        $this->set('radusers', $radusers);
        // FIXME: should not be here, DRY
        $this->set('sortIcons', array('asc' => 'icon-chevron-down', 'desc' => 'icon-chevron-up'));
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
                    $this->Session->setFlash(__('New user added. Certificate in ') . $this->request->data['Raduser']['cert_path']);
                else
                    $this->Session->setFlash(__('New user added.'));

                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Unable to add user.'), 'flash_error');
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

            $result = $this->request;
        } else {

            if ($this->Raduser->save($this->request->data)) {

                // update radchecks fields
                $checkClassFields = array(
                    'NAS-Port-Type' => $this->request->data['Raduser']['nas-port-type'],
                    'Cleartext-Password' => $this->request->data['Raduser']['password']);
                $this->Checks->update_radcheck_fields($id, $this->request, $checkClassFields);
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
                $this->Checks->update_radcheck_fields($id, $this->request, $checkClassFields);

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
            $result = $this->request;
        } else {

            if ($this->Raduser->save($this->request->data)) {
                $this->updateGroups($this->Raduser->id, $this->request);
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
            $result = $this->request;
        } else {

            if ($this->Raduser->save($this->request->data)) {
                $this->updateGroups($this->Raduser->id, $this->request);
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
                $this->Session->setFlash(__('User has been updated.'));
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

    public function delete($id = null){
        $success = $this->Checks->delete($this->request, $id);

        if($success){
            $this->Session->setFlash(__('The user with id #') . $id . __(' has been deleted.'));
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('Unable to delete user with id #') . $id . '.', 'flash_error');
        }
    }

    public function restoreGroups($id)
    {
	$groupsRecords = $this->Checks->getUserGroups($id, array( 'priority' => 'asc'));
	$groups = array();

	foreach ($groupsRecords as $group)
	{
		$groups[]= $group['Radusergroup']['groupname'];
	}
	print_r($groups);
	$this->set('selectedGroups', $groups);
    }

    public function updateGroups($id, $request)
    {
	$this->Checks->deleteAllUsersOrGroups($id);
	$this->Checks->addUsersOrGroups($id, $request->data['Raduser']['groups']);
    }
}

?>
