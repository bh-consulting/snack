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
            'checkClass' => 'Radgroupcheck'
            ),
        'Session');

    public function index(){
        $this->set('radgroups', $this->paginate('Radgroup'));
        // FIXME: should not be here, DRY
        $this->set('sortIcons', array('asc' => 'icon-chevron-down', 'desc' => 'icon-chevron-up'));
    }

    public function view($id = null){
        $views = $this->Checks->view($id);
        $this->set('radgroup', $views['base']);
        $this->set('radgroupchecks', $views['checks']);
    }

    public function add(){
        if($this->request->is('post')){
            $success = $this->Checks->add($this->request, array());

            if($success){
                $this->Session->setFlash('New group added.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add group.', 'flash_error');
            }
        }
        $Raduser = new Raduser();
        $this->set('users', $Raduser->find('list', array('fields' => array('username'))));
    }

    public function edit($id = null){
        if ($this->request->is('get')) {
            $this->Radgroup->id = $id;
            $this->request->data = $this->Radgroup->read();

        } else {
            if ($this->Radgroup->save($this->request->data)) {
                $this->Checks->update_radcheck_fields($id, $this->request);
                $this->updateUsers($id, $this->request);
                $this->Session->setFlash('Group has been updated.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to update group.', 'flash_error');
            }
        }

        $Raduser = new Raduser();
        $this->set('users', $Raduser->find('list', array('fields' => array('username'))));
        $this->restoreUsers($this->Radgroup->id);
        $this->Checks->restore_common_check_fields($id, $this->request);
    }

    public function delete($id = null)
    {
	$success = $this->Checks->delete($this->request, $id);

	if($success){
		$this->Session->setFlash('The user with id #' . $id . ' has been deleted.');
		$this->redirect(array('action' => 'index'));
	} else {
		$this->Session->setFlash('Unable to delete user with id #' . $id . '.', 'flash_error');
	}
    }

    public function restoreUsers($id)
    {
	$usersRecords = $this->Checks->getUserGroups($id);
	$users = array();

	if( !empty($usersRecords) ){
		foreach($usersRecords as $user) {
			$users[]= $user['Radusergroup']['username'];
		}
	}
	$this->set('selectedUsers', $users);
    }

    public function updateUsers($id, $request){
        $Raduser = new Raduser();
        $users = $this->Checks->getUserGroups($id);
        $usersToAdd = array();
        $usersToDelete = array();

        // remove deleted users
        foreach($users as $user){
            $found = false;
            $raduser = $Raduser->findByUsername($user['Radusergroup']['username']);
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
