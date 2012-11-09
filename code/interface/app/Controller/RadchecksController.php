<?php

class RadchecksController extends AppController
{
    public $helpers = array('Html', 'Form');

    public function index()
    {
        $this->set('radchecks', $this->Radcheck->find('all'));
    }

    public function view($id = null)
    {
        $this->Radcheck->id = $id;
        $this->set('radcheck', $this->Radcheck->read());
    }

    public function add()
    {
        if ($this->request->is('post')) {
            $this->Radcheck->create();
            if ($this->Radcheck->save($this->request->data)) {
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
            // add a cisco user with $this->request->data['username'] / $this->request->data['password']
            $this->Radcheck->create();
            $this->request->data['attribute'] = 'Cleartext-Password';
            $this->request->data['op'] = ':=';
            print_r($this->request->data);
            if ($this->Radcheck->save($this->request->data)) {
                $this->Session->setFlash('New user added.');
                //$this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add user.');
            }
        }
    }

    public function add_loginpass()
    {
        if ($this->request->is('post')) {
            $this->Radcheck->create();
            $this->request->data['attribute'] = 'Cleartext-Password';
            $this->request->data['op'] = ':=';
            if ($this->Radcheck->save($this->request->data)) {
                $this->Session->setFlash('New user added.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add user.');
            }
        }
    }

    public function add_cert()
    {
        if ($this->request->is('post')) {
            $this->Radcheck->create();
            $this->request->data['attribute'] = 'Cleartext-Password';
            $this->request->data['op'] = ':=';
            /* if ($this->Radcheck->save($this->request->data)) {
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
        $this->Radcheck->id = $id;
        if ($this->request->is('get')) {
            $this->request->data = $this->Radcheck->read();
        } else {
            if ($this->Radcheck->save($this->request->data)) {
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
        if ($this->Radcheck->delete($id)) {
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
