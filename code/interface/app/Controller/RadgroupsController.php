<?php

App::import('Model', 'Radgroupcheck');
class RadgroupsController extends AppController
{
    public $helpers = array('Html', 'Form');
    public $components = array(
        'Checks' => array(
            'displayName' => 'groupname',
            'baseClass' => 'Radgroup',
            'checkClass' => 'Radgroupcheck'
            ),
        'Session');

    public function index(){
        $this->set('radgroups', $this->Checks->index());
    }

    public function view($id = null){
        $views = $this->Checks->view($id);
        $this->set('radgroup', $views['base']);
        $this->set('radgroupchecks', $views['checks']);
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
        $success = $this->Checks->add_cisco($this->request);
        $this->add($success);
    }

    public function add_loginpass(){
        $success = $this->Checks->add_loginpass($this->request);
        $this->add($success);
    }
    public function add_mac(){
        $success = $this->Checks->add_mac($this->request);
        $this->add($success);
    }

    public function add_cert(){
        $success = $this->Checks->add_cert($this->request);
        $this->add($success);
    }

    public function edit_cisco($id = null){
        $result = $this->Checks->edit_cisco($this->request, $id);
        $this->edit($result);
    }

    public function edit_loginpass($id = null){
        $result = $this->Checks->edit_loginpass($this->request, $id);
        $this->edit($result);
    }

    public function edit_mac($id = null){
        $result = $this->Checks->edit_mac($this->request, $id);
        $this->edit($result);
    }

    public function edit_cert($id = null){
        $result = $this->Checks->edit_cert($this->request, $id);
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
