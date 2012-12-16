<?php

class RadacctsController extends AppController
{
    public $helpers = array('Html', 'Form');

		public $paginate = array(	'order' => array( 'acctuniqueid' => 'asc' ) );

    public function index()
    {
        $this->set('radaccts', $this->paginate( 'Radacct', array(), array(	'acctuniqueid'	,
																																						'username'			,
																																						'acctstarttime'	,
																																						'acctstoptime'	,
																																						'nasipaddress'	,
																																						'nasportid'			) ) );
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
