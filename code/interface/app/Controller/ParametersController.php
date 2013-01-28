<?php
Configure::load('parameters');

class ParametersController extends AppController {
    public $helpers = array('Html', 'Form');
    public $uses = array('Parameter');

    public function index() {
        $this->Parameter->read();

        foreach ($this->Parameter->data['Parameter'] as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function edit() {
        if ($this->request->is('post')) {
            Utils::cleanPath($this->request->data['Parameter']['scriptsPath']);
            Utils::cleanPath($this->request->data['Parameter']['certsPath']);

            $this->Parameter->set($this->request->data);

            if ($this->Parameter->save()) {
                $this->Session->setFlash(
                    __('Parameters have been updated.'),
                    'flash_success'
                );
                
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('Unable to update parameters.'),
                    'flash_error'
                );
            }
        } else {
            $this->request->data = $this->Parameter->read();
        }
    }
}

?>
