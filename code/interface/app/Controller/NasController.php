<?php

class NasController extends AppController
{
    public $helpers = array('Html', 'Form', 'JqueryEngine');
    public $paginate = array('limit' => 10, 'order' => array('Nas.id' => 'asc'));
    public $uses = array('Nas');

    public function index()
    {
        $this->set('nas', $this->paginate('Nas'));
        // FIXME: should not be here, DRY
        $this->set('sortIcons', array('asc' => 'icon-chevron-down', 'desc' => 'icon-chevron-up'));
    }

    public function view($id = null)
    {
        $this->Nas->id = $id;
        $nas = $this->Nas->read();

        $this->set('nas', $nas);

	$attributes = array();

	// Nas
	$attributes['IP address'] = $nas['Nas']['nasname'];
	$attributes['Short name'] = $nas['Nas']['shortname'];
	$attributes['Description'] = $nas['Nas']['description'];
	$attributes['Type'] = $nas['Nas']['type'];
	$attributes['Ports'] = $nas['Nas']['ports'];
	$attributes['Virtual server'] = $nas['Nas']['server'];
	$attributes['Community'] = $nas['Nas']['community'];

	$this->set('attributes', $attributes);
        $this->set('showedAttr', array( 'IP address', 'Short name', 'Description', 'Type', 'Ports', 'Virtual server', 'Community' ));
    }

    // method to display a warning field to restart the server after Nas changes
    public function alert_restart_server(){
        // TODO: add a link to the restart button in the message
        $this->Session->setFlash(__('You HAVE to restart the Radius server to apply NAS changes!'), 'flash_error');
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
                $this->Session->setFlash(__('Unable to add NAS.'), 'flash_error');
            }
        }
    }

    public function edit($id = null)
    {
        $this->Nas->id = $id;
        if($this->request->is('get')){
            $this->request->data = $this->Nas->read();
        } else {
            if($this->Nas->save($this->request->data)){
                $this->alert_restart_server();
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Unable to update NAS'), 'flash_error');
            }
        }
    }

    public function delete($id)
    {
        if($this->request->is('get')){
            throw new MethodNotAllowedException();
        }

        if($this->Nas->delete($id)){
            $this->Session->setFlash(__('The NAS with id:') . $id . ' ' . __('has been deleted') . '.');
            $this->redirect(array('action' => 'index'));
        }
    }
}

?>
