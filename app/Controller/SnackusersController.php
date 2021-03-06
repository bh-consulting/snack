<?php

App::uses('AppController', 'Controller');

/**
 * Controller to handle  snack user management: create, update, remove users.
 */
class SnackusersController extends AppController {
	public $helpers = array('Html', 'Form', 'JqueryEngine', 'Csv');
    public $components = array(
        'Session',
        'RequestHandler'
    );
	public $uses = array('Snackuser');

	public function login() {
        if ($this->request->is('post')) {
            if ($this->checkAuthentication(
                    $this->request->data['Snackuser']['username'], $this->request->data['Snackuser']['passwd']
                )) {
                $this->Auth->login($this->request->data['Raduser']);
                $this->Session->write('Auth.User.username', $this->request->data['Snackuser']['username']);
                //$this->Session->write('Auth.username', $this->request->data['Snackuser']['username']);
                Utils::userlog(__('logged in'));

                $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash(
                    __('Username or password is incorrect,'
                       . ' or user is not authorized to access Snack interface.'), 'default', array(), 'auth'
                );
            }
        }
        if (Configure::read('Parameters.role')=="slave") {
            $this->Session->setFlash(
                    __('Warning this is a SLAVE node : all changement will be not saved'), 'default', array(), 'role'
            );
        }
    }

    public function logout() {
        Utils::userlog(__('logged out'));
        $this->redirect($this->Auth->logout());
    }

    public function beforeFilter() {
        $this->Auth->allow('login', 'logout');
        parent::beforeFilter();
    }
    
    public function isAuthorized($user) {
        if (in_array($this->action, array(
                    'login', 'logout', 'changepwd',
                ))) {
            return true;
        }
        return parent::isAuthorized($user);
    }

    private function checkAuthentication($username, $passwd) {
        $user = $this->Snackuser->findByUsername($username);
        if (isset($user) && !empty($user)) {
            $role = $this->Snackuser->getRole($user['Snackuser']['id']);
            if ($role != 'user') {
                $this->request->data['Raduser']['role'] = $role;
                if ($user['Snackuser']['password'] == Security::hash($passwd, 'sha1', true)) {
                	return true;
                }
            } else {
                return false;
            }
        }

        return false;
    }

    public function index() {
    	$this->Snackuser->recursive = 0;
        $this->set('snackusers', $this->paginate());
    }
    
    public function add() {
    	if ($this->request->is('post')) {
            $this->Snackuser->create();
            if ($this->Snackuser->save($this->request->data)) {
                $this->Session->setFlash(
                    __('New user added.'), 'flash_success'
                );
                Utils::userlog(__('added user %s', $this->Snackuser->id));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Error.'), 'flash_error');
            }
        }
        $this->set('roles', $this->Snackuser->roles);
    }

    public function edit($id = null) {
        $this->Snackuser->id = $id;   
    	if ($this->request->is('get')) {
            $this->request->data = $this->Snackuser->read();
        } else {
            $user = $this->Snackuser->read();
            if (($this->request->data['Snackuser']['password'] == '' && $this->request->data['Snackuser']['confirm_password'] == '')) {
                unset($this->request->data['Snackuser']['password']);
                unset($this->request->data['Snackuser']['confirm_password']);
            }
            if($this->Snackuser->save($this->request->data, array('validate' => false))){
                Utils::userlog(__('edited SNACK user %s', $id));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('Unable to update SNACK user.'),
                    'flash_error'
                );
                Utils::userlog(__('error while editing SNACK user %s', $id), 'error');
            }
        }
        $this->set('roles', $this->Snackuser->roles);
    }

    public function delete($id = null) {
        if($this->request->is('get')){
            throw new MethodNotAllowedException();
        }

        $id = is_null($id) ? $this->request->data['Snackuser']['id'] : $id;

        if($this->Snackuser->delete($id)){
            $this->Session->setFlash(
                __('The SNACK user has been deleted.'),
                'flash_success'
            );
            Utils::userlog(__('deleted SNACK user %s', $id));
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(
                __('Unable to delete SNACK user.'));
            Utils::userlog(__('error while deleting SNACK user %s', $id), 'error');
        }
    }

    public function changepwd($username) {
        if ($this->request->is('post')) {
            $id = $this->Snackuser->find('first', array(
            'conditions' => array('Snackuser.username' => $username),
        ))['Snackuser']['id'];
            $this->Snackuser->id = $id;
            if ($this->Snackuser->save($this->request->data)) {
                $this->Session->setFlash(
                    __('Password saved.'), 'flash_success'
                );
                Utils::userlog(__('password of user %s saved', $this->Snackuser->id));
                return $this->redirect($this->referer());
            } else {
                $this->Session->setFlash(__('Error.'), 'flash_error');
            }
        }
    }

}