<?php
/**
 * Created by JetBrains PhpStorm.
 * User: julien
 * Date: 10/10/12
 * Time: 16:50
 * To change this template use File | Settings | File Templates.
 */
class RadchecksController extends AppController
{
    public $helpers = array('Html', 'Form');

    public function index(){
        $this->set('radchecks', $this->Radcheck->find('all'));
    }

    public function view($id = null){
        $this->Radcheck->id = $id;
        $this->set('radcheck', $this->Radcheck->read());
    }

    public function add(){
        if($this->request->is('post')){
            $this->Radcheck->create();
            if($this->Radcheck->save($this->request->data)){
                $this->Session->setFlash('New user added.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add user.');
            }
        }
    }

    public function edit($id = null){
        $this->Radcheck->id = $id;
        if($this->request->is('get')){
            $this->request->data = $this->Radcheck->read();
        } else {
            if($this->Radcheck->save($this->request->data)){
                $this->Session->setFlash('User has been updated.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to update user.');
            }
        }
    }

    public function delete($id){
        if($this->request->is('get')){
            throw new MethodNotAllowedException();
        }
        if($this->Radcheck->delete($id)){
            $this->Session->setFlash('The user with id:' . $id . ' has been deleted.');
            $this->redirect(array('action' => 'index'));
        }
    }

}
