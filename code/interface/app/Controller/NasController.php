<?php

class NasController extends AppController
{
    public $helpers = array('Html', 'Form');

    public function index()
    {
        $this->loadModel('Nas');
        $this->set('nas', $this->Nas->find('all'));
    }

    public function view($id = null)
    {
        $this->Nas->id = $id;
        $this->set('nas', $this->Nas->read());
    }

}

