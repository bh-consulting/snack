<?php

class NasController extends AppController
{
    public $helpers = array('Html', 'Form');
    public $uses = array('Nas');

    public function index()
    {
        $this->set('nas', $this->Nas->find('all'));
    }

    public function view($id = null)
    {
        $this->Nas->id = $id;
        $this->set('nas', $this->Nas->read());
    }

    // method to display a warning field to restart the server after Nas changes
    public function alert_restart_server(){
        // TODO: add a link to the restart button in the message
        $this->Session->setFlash('You HAVE to restart the Radius server to apply NAS changes!', 'default', array(), 'warning');
    }

    public function add(){
        if($this->request->is('post')){
            $this->Nas->create();
            
            // init with default values
            $this->request->data['Nas']['type'] = 'other';
            $this->request->data['Nas']['ports'] = 1812;

            if ($this->Nas->save($this->request->data)) {
                $this->alert_restart_server();
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add NAS.', 'flash_error');
            }
        }
    }

    public function delete($id)
    {
        if($this->request->is('get')){
            throw new MethodNotAllowedException();
        }

        if($this->Nas->delete($id)){
            $this->Session->setFlash('The NAS with id:' . $id . ' has been deleted.');
            $this->redirect(array('action' => 'index'));
        }
    }


}

?>