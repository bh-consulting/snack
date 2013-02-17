<?php

class RadacctsController extends AppController {
    public $helpers = array('Html', 'Form');
    public $paginate = array(
        'limit' => 10,
        'order' => array('acctuniqueid' => 'asc')
    );
    public $components = array(
        'Filters' => array('model' => 'Radacct')
    );

    public function index() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['MultiSelection']['sessions'])
                && is_array($this->request->data['MultiSelection']['sessions'])
            ) {
                $success = true;
                foreach( $this->request->data['MultiSelection']['sessions']
                    as $sessionId ) {
                    switch( $this->request->data['action'] ) {
                    case "delete":
                        $success = $success 
                            && $this->Radacct->delete($sessionId);
                        if ($success) {
                            Utils::userlog(
                                __('deleted session %s',
                                $sessionId));
                        } else {
                            Utils::userlog(
                                __(
                                    'error while deleting session %s',
                                    $sessionId
                                ),
                                'error'
                            );
                        }
                        break;
                    }
                }

                if ($success) {
                    switch( $this->request->data['action'] ) {
                    case "delete":
                        $this->Session->setFlash(
                            __('Sessions have been deleted.'),
                            'flash_success'
                        );
                        Utils::userlog(
                            __('deleted session %s', $sessionId)
                        );
                        break;
                    }
                } else {
                    switch( $this->request->data['action'] ) {
                    case "delete":
                        $this->Session->setFlash(
                            __('Unable to delete sessions.'),
                            'flash_error'
                        );
                        break;
                    }
                }
            } else {
                $this->Session->setFlash(
                    __('Please, select at least one session !'),
                    'flash_warning'
                );
            }
        }

        $this->Filters->addDatesConstraint(array(
            'fields' => array('acctstarttime', 'acctstoptime'), 
            'from' => 'datefrom',
            'to' => 'dateto',
        ));

        $this->Filters->addStringConstraint(array(
            'fields' => array(
                'acctuniqueid',
                'username',
                'callingstationid',
                'nasipaddress',
            ),
            'input' => 'text',
            'ahead' => array('username','callingstationid', 'nasipaddress'),
        ));

        $this->Filters->addSelectConstraint(array(
            'fields' => array('nasporttype'),
            'data' => array('nasporttype'),
            'input' => 'porttype',
            'title' => __('Select a port type...'),
        ));

        $this->Filters->paginate();
    }

    public function view($id = null) {
        $this->Radacct->id = $id;
        $this->set('radacct', $this->Radacct->read());
    }

    public function delete() {
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        $id = $this->request->data['Radacct']['id'];
        $uniqueId = $this->Radacct->field(
            'acctuniqueid',
            array('radacctid' => $id)
        );

        if ($this->Radacct->delete($id)) {
            $this->Session->setFlash(
                __('The Session with id #%s has beed deleted', $uniqueId),
                'flash_success'
                );
		    Utils::userlog(__('deleted session %s', $uniqueId));
            $this->redirect(array('action' => 'index'));
		} else {
		    $this->Session->setFlash(
				__('Unable to delete session #%s', $uniqueId),
				'flash_error'
			);
            Utils::userlog(
                __('error while deleting session %s', $uniqueId),
                'error'
            );
		}
    }
}

?>
