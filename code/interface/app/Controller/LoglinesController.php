<?php

App::uses('Sanitize', 'Utility');

class LoglinesController extends AppController {
    public $helpers = array('Html', 'Form');
    public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));
    public $components = array(
        'Filters' => array('model' => 'Logline')
    );

    public function index() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['MultiSelection']['logs'])
                && is_array($this->request->data['MultiSelection']['logs'])
            ) {
                $success = true;
                foreach( $this->request->data['MultiSelection']['logs'] 
                    as $logId ) {
                    switch( $this->request->data['action'] ) {
                    case "delete":
                        $success = $success && $this->Logline->delete($logId);
                        if ($success) {
						    Utils::userlog(__('deleted log line %s', $logId));
                        } else {
                            Utils::userlog(
                                __('error while deleting log line %s', $logId),
                                'error'
                            );
                        }
                        break;
                    }
                }

                if($success){
                    switch( $this->request->data['action'] ) {
                    case "delete":
                        $this->Session->setFlash(
                            __('Log lines have been deleted.'),
                            'flash_success'
                        );
                        break;
                    }
                } else {
                    switch( $this->request->data['action'] ) {
                    case "delete":
                        $this->Session->setFlash(
                            __('Unable to delete log lines.'),
                            'flash_error'
                        );
                        break;
                    }
                }
            } else {
                $this->Session->setFlash(
                    __('Please, select at least one log line !'),
                    'flash_warning'
                );
            }
        }

        $this->Filters->addSliderConstraint(array(
            'fields' => 'level', 
            'input' => 'level',
            'default' => 'info',
            'options' => $this->Logline->levels,
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
}
?>
