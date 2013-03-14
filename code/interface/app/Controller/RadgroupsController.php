<?php

App::import('Model', 'Radgroupcheck');
App::import('Model', 'Raduser');

class RadgroupsController extends AppController {

    public $helpers = array('Html', 'Form', 'JqueryEngine');
    public $paginate = array('limit' => 10, 'order' => array('id' => 'asc'));
    public $components = array(
        'Filters',
        'Checks' => array(
            'displayName' => 'groupname',
            'baseClass' => 'Radgroup',
            'checkClass' => 'Radgroupcheck',
            'replyClass' => 'Radgroupreply'
        ),
        'Session');

    public function isAuthorized($user) {
        if($user['role'] === 'admin' && in_array($this->action, array(
            'index', 'view', 'add', 'edit',
        ))){
            return true;
        }

        return parent::isAuthorized($user);
    }

    public function beforeValidateForFilters() {
        unset($this->Radgroup->validate['groupname']['notEmpty']['required']);
    }
    
    public function getRegexExpiration($args = array()) {
        if (!empty($args['input'])) {
            $data = &$this->request->data['Radgroup'][$args['input']];

            if (isset($data[0]) && $data[0] == 'expired') {
                return "(groupname IN (SELECT groupname from radgroupcheck "
                    . "where attribute='Expiration' "
                    . "and STR_TO_DATE(value, '%d %b %Y %T') < NOW()))";
            } else {
                return '(1=1)';
            }
        }
    }

	public function index(){
        // Multiple delete/export
        if ($this->request->is('post')) {
            switch ($this->request->data['action']) {
            case "delete":
                $this->multipleDelete(
                    isset($this->request->data['MultiSelection']) ?
			$this->request->data['MultiSelection']['groups'] : 0
                );
                break;
            }
        }

        // Filters
        $this->Filters->addStringConstraint(array(
            'fields' => array(
                'id',
                'groupname',
                'comment',
            ),
            'input' => 'text',
            'ahead' => array('groupname'),
        ));

        $this->Filters->addComplexConstraint(array(
            'select' => array(
                'items' => array(
                    'expired' => '<i class="icon-warning-sign icon-red"></i> '
                    . __('Expired'),
                ),
                'input' => 'expired',
                'title' => false,
            ),
            'callback' => array(
                'getRegexExpiration',
                array(
                    'input' => 'expired',
                ),
            )
        ));

        $radusers = $this->Filters->paginate();
        $radgroups = $this->Filters->paginate();

        if ($radgroups != null) {
            foreach ($radgroups as &$group) {
                $radgc = new Radgroupcheck();
                $groupcheck = $radgc->find(
                    'first',
                    array(
                        'fields' => 'value',
                        'conditions' => array(
                            'groupname' => $group['Radgroup']['groupname'],
                            'attribute' => 'Expiration'
                        )
                    )
                );

                if (!empty($groupcheck)
                    && Utils::formatDate(
                        array($groupcheck['Radgroupcheck']['value'], date('d M Y H:i:s')),
                        'dursign'
                    ) >= 0 
                ) {
                    $group['Radgroup']['expiration'] = Utils::formatDate(
                        $groupcheck['Radgroupcheck']['value'],
                        'display'
                    );
                } else {
                    $group['Radgroup']['expiration'] = -1;
                }
            }
        }

        $this->set('radgroups', $radgroups);
    }

    /**
     * Delete severals groups.
     *
     * @param ids - array of group ID.
     */
    private function multipleDelete($ids=array()) {
        if (isset($ids) && is_array($ids)) {
            try {
                foreach ($ids as $userId) {
                    $this->Checks->delete($this->request, $userId);
                    Utils::userlog(__('deleted group %s', $userId));
                }

                $this->Session->setFlash(
                    __('Groups have been deleted.'),
                    'flash_success'
                );
            } catch (UserGroupException $e) {
                $this->Session->setFlash(
                    $e->getMessage(),
                    'flash_error'
                );
                Utils::userlog(__('error while deleting group'), 'error');
            }
        } else {
            $this->Session->setFlash(
                __('Please, select at least one group!'),
                'flash_warning'
            );
        }
    }

    public function view($id = null){
        $views = $this->Checks->view($id);
        $this->set('radgroup', $views['base']);
        $this->set('radgroupchecks', $views['checks']);
	
    	$attributes = array();

    	// Radgroup
    	$attributes['Groupname'] = $views['base']['Radgroup']['groupname'];
    	$attributes['Comment'] = $views['base']['Radgroup']['comment'];

    	// Radchecks
    	foreach($views['checks'] as $check){
    		$attributes[ $check['Radgroupcheck']['attribute'] ] = $check['Radgroupcheck']['value'];
    	}

    	// Radusergroup
    	$users = array();
    	foreach($views['groups'] as $user){
    		$users[] = $user['Radusergroup']['username'];
    	}

    	$attributes['Users'] = $users;

    	$this->set('attributes', $attributes);
        $this->set(
            'showedAttr',
            array(
                'Groupname',
                'Comment',
                'NAS-Port-Type',
                'Expiration',
                'Simultaneous-Use',
                'Users'
            )
        );
    }

    public function add(){
        if($this->request->is('post')){
            try {
                $this->Checks->add($this->request, array());

                $this->Session->setFlash(
                    __('New group added.'),
                    'flash_success'
                );
                Utils::userlog(__('added group %s', $this->Radgroup->id));

                $this->redirect(array('action' => 'index'));
            } catch (UserGroupException $uge) {
                $this->Session->setFlash(
                    $uge->getMessage(),
                    'flash_error'
                );
                Utils::userlog(__('error while adding group'), 'error');
            }
        }

        $Raduser = new Raduser();
        $this->set(
            'users',
            $Raduser->find('list', array('fields' => array('username')))
        );
    }

    public function edit($id = null){
        if ($this->request->is('get')) {
            $this->Radgroup->id = $id;
            $this->request->data = $this->Radgroup->read();
        } else {
            try {
                $this->Radgroup->save($this->request->data);
                $this->Checks->updateRadcheckFields($id, $this->request);
                $this->Checks->updateRadreplyFields($id, $this->request);
                $this->updateUsers($id, $this->request);

                $this->Session->setFlash(
                    __('Group has been updated.'),
                    'flash_success'
                );
                Utils::userlog(__('edited group %s', $this->Radgroup->id));

                $this->redirect(array('action' => 'index'));
            } catch (UserGroupException $uge) {
                $this->Session->setFlash(
                    $uge->getMessage(),
                    'flash_error'
                );
                Utils::userlog(__('error while editing group %s', $this->Radgroup->id), 'error');
            }
        }

        $Raduser = new Raduser();
        $this->set(
            'users',
            $Raduser->find('list', array('fields' => array('username')))
        );
        $this->restoreUsers($this->Radgroup->id);
        $this->Checks->restoreCommonCheckFields($id, $this->request);

        if (isset($this->request->data['Radgroup']['expiration_date'])) {
            $this->request->data['Radgroup']['expiration_date'] = Utils::formatDate(
                $this->request->data['Radgroup']['expiration_date'],
                'syst'
            );
        }

        $this->Checks->restoreCommonReplyFields($id, $this->request);
    }

    public function delete ($id = null) {
	try {
	    if($this->request->is('get')){
		throw new MethodNotAllowedException();
	    }

	    $id = is_null($id) ? $this->request->data['Radgroup']['id'] : $id;

	    $this->Checks->delete($this->request, $id);

	    $this->Session->setFlash(
		    __('The group has been deleted.'),
		    'flash_success'
		    );

	    Utils::userlog(__('deleted group %s', $id));

	} catch (UserGroupException $uge) {
	    $this->Session->setFlash(
		    $uge->getMessage(),
		    'flash_error'
		    );

	    Utils::userlog(__('error while deleting group %s', $this->Radgroup->id), 'error');
	}

	$this->redirect(array('action' => 'index'));
    }

    public function restoreUsers($id) {
    	$usersRecords = $this->Checks->getUserGroups($id);
    	$users = array();

    	if( !empty($usersRecords) ){
    		foreach($usersRecords as $user) {
    			$users[]= $user['Radusergroup']['username'];
    		}
    	}
    	$this->set('selectedUsers', $users);
    }

    public function updateUsers($id, $request) {
        $Raduser = new Raduser();
        $users = $this->Checks->getUserGroups($id);
        $usersToAdd = array();
        $usersToDelete = array();

        // remove deleted users
        foreach($users as $user){
            $found = false;
            $raduser = $Raduser->findByUsername(
                $user['Radusergroup']['username']
            );
            if(!empty($request->data['Radgroup']['users'])){
                foreach($request->data['Radgroup']['users'] as $requestUser){
                    if($raduser['Raduser']['id'] == $requestUser)
                        $found = true;
                }
            }

            if(!$found){
                $usersToDelete[]= $raduser['Raduser']['id'];
            }
        }

        $this->Checks->deleteUsersOrGroups($id, $usersToDelete);

        // add new users
        if(!empty($request->data['Radgroup']['users'])){
            foreach($request->data['Radgroup']['users'] as $requestUser){
                $found = false;
                foreach($users as $user){
                    $raduser = $Raduser->findByUsername($user['Radusergroup']['username']);
                    if($raduser['Raduser']['id'] == $requestUser)
                        $found = true;
                }

                if(!$found){
                    $usersToAdd[]= $requestUser;
                }
            }
        }
        $this->Checks->addUsersOrGroups($id, $usersToAdd);
    }
}

?>
