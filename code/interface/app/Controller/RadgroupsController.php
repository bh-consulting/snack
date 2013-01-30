<?php

App::import('Model', 'Radgroupcheck');
App::import('Model', 'Raduser');

class RadgroupsController extends AppController
{
    public $helpers = array('Html', 'Form', 'JqueryEngine');
    public $paginate = array('limit' => 10, 'order' => array('Radgroup.id' => 'asc'));
    public $components = array(
        'Checks' => array(
            'displayName' => 'groupname',
            'baseClass' => 'Radgroup',
            'checkClass' => 'Radgroupcheck',
            'replyClass' => 'Radgroupreply'
            ),
        'Session');

	public function index(){
        // Multiple delete/export
        if ($this->request->is('post')) {
            switch ($this->request->data['action']) {
            case "delete":
                $this->multipleDelete(
                    $this->request->data['MultiSelection']['groups']
                );
                break;
            }
        }

		$this->set('radgroups', $this->paginate('Radgroup'));

		// FIXME: should not be here, DRY
		$this->set('sortIcons', array('asc' => 'icon-chevron-down', 'desc' => 'icon-chevron-up'));
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
            }
        } else {
            $this->Session->setFlash(
                __('Please, select at least one group !'),
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
    	$attributes['Certificate path'] = $views['base']['Radgroup']['cert_path'];

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

                $this->redirect(array('action' => 'index'));
            } catch (UserGroupException $uge) {
                $this->Session->setFlash(
                    $uge->getMessage(),
                    'flash_error'
                );
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

                $this->redirect(array('action' => 'index'));
            } catch (UserGroupException $uge) {
                $this->Session->setFlash(
                    $uge->getMessage(),
                    'flash_error'
                );
            }
        }

        $Raduser = new Raduser();
        $this->set(
            'users',
            $Raduser->find('list', array('fields' => array('username')))
        );
        $this->restoreUsers($this->Radgroup->id);
        $this->Checks->restoreCommonCheckFields($id, $this->request);
    }

	public function delete ($id = null) {
        try {
            $this->Checks->delete($this->request, $id);

			$this->Session->setFlash(
				__('The group with id #') . $id . __(' has been deleted.'),
				'flash_success'
			);
		} catch (UserGroupException $uge) {
			$this->Session->setFlash(
				$uge->getMessage(),
				'flash_error'
			);
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
