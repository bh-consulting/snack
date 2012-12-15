<?php

class RadacctsController extends AppController
{
    public $helpers = array('Html', 'Form');

    public function index()
    {
        $this->set('radaccts', $this->Radacct->find('all'));
    }

    public function view($id = null)
    {
        $this->Radacct->id = $id;
        $this->set('radacct', $this->Radacct->read());
    }

    public function delete($id)
    {
        if($this->request->is('get')){
            throw new MethodNotAllowedException();
        }

        if($this->Radacct->delete($id)){
            $this->Radacct->setFlash('The Session with id:' . $id . ' has been deleted.');
            $this->redirect(array('action' => 'index'));
        }
    }


}

?>
