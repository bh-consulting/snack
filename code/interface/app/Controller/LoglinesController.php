<?php

App::uses('Sanitize', 'Utility');

class LoglinesController extends AppController {
    public $helpers = array('Html', 'Form');
    public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));
    public $components = array(
        'Filters' => array('model' => 'Logline'),
    );

    public function isAuthorized($user) {
        
        if($user['role'] === 'admin' && $this->action === 'index'){
            return true;
        }

        return parent::isAuthorized($user);
    }

    public function index() {
        $this->Filters->addSliderConstraint(array(
            'fields' => 'level', 
            'input' => 'level',
            'default' => 'info',
            'items' => $this->Logline->levels,
        ));

        $this->Filters->addDatesConstraint(array(
            'fields' => 'datetime', 
            'from' => 'datefrom',
            'to' => 'dateto',
        ));

        $this->Filters->addStringConstraint(array(
            'fields' => 'msg',
            'input' => 'text',
        ));

        $this->Filters->paginate();
    }

    public function deleteAll($program) {
        if($this->request->is('get')){
            throw new MethodNotAllowedException();
        }

        if (in_array($program, array('freeradius', 'interface'))) {
            if ($this->Logline->deleteAll(array('program' => $program))) {
                $this->Session->setFlash(
                    __('All logs for %s have been deleted.', $program),
                    'flash_success'
                );
                Utils::userlog(__('delete all logs for %s', $program));
            } else {
                $this->Session->setFlash(
                    __('Unable to delete all logs for %s.', $program),
                    'flash_error'
                );
                Utils::userlog(__('error while deleting logs for %s', $program));
            }
        }

        $this->redirect(array('action' => 'index'));
    }
}
?>
