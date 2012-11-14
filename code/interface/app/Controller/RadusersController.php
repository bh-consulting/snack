<?php

App::import('Model', 'Radcheck');
class RadusersController extends AppController
{
    public $helpers = array('Html', 'Form');

    public function index()
    {
        $this->set('radusers', $this->Raduser->find('all'));
    }

    public function view($id = null)
    {
        $this->Raduser->id = $id;
        $this->set('raduser', $this->Raduser->read());
    }

    public function create_radcheck($username, $attribute, $op, $value){
        $data = array(
            'username' => $username,
            'attribute' => $attribute,
            'op' => $op,
            'value' => $value
        );
        $rad = new Radcheck;
        $rad->create();
        $rad->save($data);
    }

    public function add($radchecks){
        if($this->request->is('post')){
                    
            $success = true;
            foreach($radchecks as $rc)
                $this->create_radcheck($rc[0], $rc[1], $rc[2], $rc[3]);

            $this->Raduser->create();
            $success = $success && $this->Raduser->save($this->request->data);

            if($success){
                $this->Session->setFlash('New user added.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add user.');
            }
        }
    }
    public function add_cisco()
    {
        if ($this->request->is('post')) {
            $username = $this->request->data['Raduser']['username'];
            $radchecks = array(
                array($username,
                    'NAS-Port-Type',
                    '==',
                    $this->request->data['Raduser']['nas-port-type']
                ),
                array($username,
                    'Cleartext-Password',
                    ':=',
                    $this->request->data['Raduser']['password']
                ),
                array($username,
                    'EAP-TYPE',
                    ':=',
                    'MD5-CHALLENGE'
                )
            );

            $this->add($radchecks);

            // add a cisco user with $this->request->data['username'] / $this->request->data['password']
        }
    }

    public function add_loginpass()
    {
        if ($this->request->is('post')) {

            $username = $this->request->data['Raduser']['username'];
            $radchecks = array(
                array($username,
                    'NAS-Port-Type',
                    '==',
                    '15'
                ),
                array($username,
                    'Cleartext-Password',
                    ':=',
                    $this->request->data['Raduser']['password']
                ),
                array($username,
                    'EAP-TYPE',
                    ':=',
                    'MD5-CHALLENGE'
                )
            );
            $this->add($radchecks);
        }
    }

    public function add_mac(){
        
        if ($this->request->is('post')) {

            $username = $this->request->data['Raduser']['mac'];
            $radchecks = array(
                array($username,
                    'NAS-Port-Type',
                    '==',
                    '15'
                ),
                array($username,
                    'Cleartext-Password',
                    ':=',
                    $this->request->data['Raduser']['mac']
                ),
                array($username,
                    'EAP-TYPE',
                    ':=',
                    'MD5-CHALLENGE'
                )
            );
            $this->add($radchecks);
        }
    }

    public function add_cert()
    {
        if ($this->request->is('post')) {
            $this->Raduser->create();
            $this->request->data['attribute'] = 'Cleartext-Password';
            $this->request->data['op'] = ':=';
            /* if ($this->Raduser->save($this->request->data)) {
                $this->Session->setFlash('New user added.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add user.');
            }
             */
        }
    }

    public function edit($id = null)
    {
        $this->Raduser->id = $id;
        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();
        } else {
            if ($this->Raduser->save($this->request->data)) {
                $this->Session->setFlash('User has been updated.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to update user.');
            }
        }
    }

    public function delete($id)
    {
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }
        if ($this->Raduser->delete($id)) {
            $this->Session->setFlash('The user with id:' . $id . ' has been deleted.');
            $this->redirect(array('action' => 'index'));
        }
    }

    public function login()
    {
        if ($this->request->is('post')) {
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
