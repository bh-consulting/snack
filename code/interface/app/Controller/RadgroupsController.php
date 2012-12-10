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

    public function add(){
        if($this->request->is('post')){
            $success = $this->Checks->add($this->request, array());

            if($success){
                $this->Session->setFlash('New group added.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add group.', 'flash_error');
            }
        }
    }

    public function edit($id = null){
        if ($this->request->is('get')) {
            $this->Radgroup->id = $id;
            $this->request->data = $this->Radgroup->read();
            $this->Checks->restore_common_check_fields($id, $this->request);
        } else {
            if ($this->Radgroup->save($this->request->data)) {
                $this->Checks->update_radcheck_fields($id, $this->request);
                $this->Session->setFlash('Group has been updated.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to update group.', 'flash_error');
            }
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
