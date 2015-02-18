<?php

class RadacctsController extends AppController {
    public $helpers = array('Html', 'Form');
    public $paginate = array(
        'limit' => 10,
        'order' => array('acctstarttime' => 'desc'),
        'conditions' => array('Radacct.acctauthentic !=' => 'Local'),
        
    );
    public $components = array(
        'Filters',
        'Users',
        'Nas',
    );

    public function isAuthorized($user) {
        if ($user['role'] === 'admin'
            && in_array($this->action, array('index', 'view'))
        ){
            return true;
        }

        return parent::isAuthorized($user);
    }

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
                    __('Please, select at least one session!'),
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
                'nasportid',
            ),
            'input' => 'text',
            'ahead' => array('username','callingstationid', 'nasipaddress'),
        ));

        $this->Filters->addSelectConstraint(array(
            'fields' => array('nasporttype'),
            'data' => array('nasporttype'),
            'translate' => $this->Radacct->types,
            'input' => 'porttype',
            'title' => __('Select a port type...'),
        ));
	
        $this->Filters->addComplexConstraint(array(
            'select' => array(
                'items' => array(
                    'active' => __(' '),
                ),
                'input' => 'active',
                'title' => false,
            ),
            'callback' => array(
                'getActive',
                array(
                    'input' => 'active',
                ),
            )
        ));
        
        $sessions = $this->Filters->paginate();

        $users = array();
        $devices = array();

        foreach ($sessions as &$session) {
            $users[$session['Radacct']['radacctid']] =
                $this->Users->extendUsers($session['Radacct']['username']);

            $devices[$session['Radacct']['radacctid']] =
                $this->Nas->extendNasByIP($session['Radacct']['nasipaddress']);

            $session['Radacct']['duration'] = Utils::formatDate(
                array(
                    $session['Radacct']['acctstarttime'],
                    $session['Radacct']['acctstoptime'],
                ),
                'durdisplay'
            ); 

	    if(is_null($session['Radacct']['acctstoptime'])) {
		$session['Radacct']['durationsec'] = Utils::formatDate(
		    array(
			$session['Radacct']['acctstarttime'],
			$session['Radacct']['acctstoptime'],
		    ),
		    'durdisplaysec'
		); 
	    } else
		$session['Radacct']['durationsec'] = -1;

            if (isset($session['Radacct']['acctstarttime'])) {
                $session['Radacct']['acctstarttime'] = Utils::formatDate(
                    $session['Radacct']['acctstarttime'],
                    'display'
                );
            }

            if (isset($session['Radacct']['acctstoptime'])) {
                $session['Radacct']['acctstoptime'] = Utils::formatDate(
                    $session['Radacct']['acctstoptime'],
                    'display'
                );
            }
        }
        $this->set('users', $users);
        $this->set('devices', $devices);
	$this->set('types', $this->Radacct->types);
        $this->set('radaccts', $sessions);
    }

    public function view($id = null) {
        $this->Radacct->id = $id;
	$session = $this->Radacct->read();
        $session['Radacct']['duration'] = Utils::formatDate(
            array(
                $session['Radacct']['acctstarttime'],
                $session['Radacct']['acctstoptime'],
            ),
            'durdisplay'
        );

	if(is_null($session['Radacct']['acctstoptime'])) {
	    $session['Radacct']['durationsec'] = Utils::formatDate(
		array(
		    $session['Radacct']['acctstarttime'],
		    $session['Radacct']['acctstoptime'],
		),
		'durdisplaysec'
	    ); 
	} else
	    $session['Radacct']['durationsec'] = -1;

        $this->set('radacct', $session);
        $this->set('types', $this->Radacct->types);
    }

    public function delete($id = null) {
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        $id = is_null($id) ? $this->request->data['Radacct']['id'] : $id;

        $uniqueId = $this->Radacct->field(
            'acctuniqueid',
            array('radacctid' => $id)
        );

        if ($this->Radacct->delete($id)) {
            $this->Session->setFlash(
                __('The session has been deleted'),
                'flash_success'
            );
            Utils::userlog(__('deleted session %s', $uniqueId));
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(
                __('Unable to delete session.'),
                'flash_error'
            );
            Utils::userlog(
                __('error while deleting session %s', $uniqueId),
                'error'
            );
        }
    }
	
    public function getActive($args = array()) {
        if (!empty($args['input'])) {
	    //$data = &$this->request->data['Radacct'][$args['input']];
            $data = &$this->request->query['active'];
            if (isset($data[0]) && $data[0] == 'active') {
                return "(radacctid IN (SELECT radacctid from radacct "
                    . "where acctstoptime is NULL ))";
            } else {
                return '(1=1)';
            }
        }

    }
    
    public function get_sessions_ajax() {
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
                    __('Please, select at least one session!'),
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
                'nasportid',
            ),
            'input' => 'text',
            'ahead' => array('username','callingstationid', 'nasipaddress'),
        ));

        $this->Filters->addSelectConstraint(array(
            'fields' => array('nasporttype'),
            'data' => array('nasporttype'),
            'translate' => $this->Radacct->types,
            'input' => 'porttype',
            'title' => __('Select a port type...'),
        ));
	
        $this->Filters->addComplexConstraint(array(
            'select' => array(
                'items' => array(
                    'active' => __(' '),
                ),
                'input' => 'active',
                'title' => false,
            ),
            'callback' => array(
                'getActive',
                array(
                    'input' => 'active',
                ),
            )
        ));
        
        $sessions = $this->Filters->paginate();

        $users = array();
        $devices = array();

        foreach ($sessions as &$session) {
            $users[$session['Radacct']['radacctid']] =
                $this->Users->extendUsers($session['Radacct']['username']);

            $devices[$session['Radacct']['radacctid']] =
                $this->Nas->extendNasByIP($session['Radacct']['nasipaddress']);

            $session['Radacct']['duration'] = Utils::formatDate(
                array(
                    $session['Radacct']['acctstarttime'],
                    $session['Radacct']['acctstoptime'],
                ),
                'durdisplay'
            ); 

	    if(is_null($session['Radacct']['acctstoptime'])) {
		$session['Radacct']['durationsec'] = Utils::formatDate(
		    array(
			$session['Radacct']['acctstarttime'],
			$session['Radacct']['acctstoptime'],
		    ),
		    'durdisplaysec'
		); 
	    } else
		$session['Radacct']['durationsec'] = -1;

            if (isset($session['Radacct']['acctstarttime'])) {
                $session['Radacct']['acctstarttime'] = Utils::formatDate(
                    $session['Radacct']['acctstarttime'],
                    'display'
                );
            }

            if (isset($session['Radacct']['acctstoptime'])) {
                $session['Radacct']['acctstoptime'] = Utils::formatDate(
                    $session['Radacct']['acctstoptime'],
                    'display'
                );
            }
        }
        $this->set('users', $users);
        $this->set('devices', $devices);
	$this->set('types', $this->Radacct->types);
        $this->set('radaccts', $sessions);
        $this->layout = false;
    }
}

?>
