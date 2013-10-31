<?php

App::import('Model', 'Radgroupcheck');
App::import('Model', 'Raduser');

class RadgroupsController extends AppController {

    public $helpers = array('Html', 'Form', 'JqueryEngine', 'Csv');
    public $paginate = array('limit' => 10, 'order' => array('id' => 'asc'));
    public $components = array(
        'Filters',
        'Checks' => array(
            'displayName' => 'groupname',
            'baseClass' => 'Radgroup',
            'checkClass' => 'Radgroupcheck',
            'replyClass' => 'Radgroupreply'
        ),
        'Session',
        'RequestHandler',
    );

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
            case "export":
                $this->multipleExport(
                    $this->request->data['MultiSelection']['groups']
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

        $radgroups = $this->Filters->paginate();

        if ($radgroups != null) {
            $groupList = array();
            foreach ($radgroups as &$group) {
                if (!empty($group['Radgroup']['groupname'])) {
                    $groupList[] = $group['Radgroup']['groupname'];
                }
            }

            if (!empty($groupList)) {
                $radgroupcheck = new Radgroupcheck();

                $expirations = $radgroupcheck->find(
                    'list',
                    array(
                        'fields' => array('groupname', 'value'),
                        'conditions' => array(
                            'groupname REGEXP' => '^(' . implode($groupList, '|') . ')$',
                            'attribute' => 'Expiration',
                        )
                    )
                );

                foreach ($radgroups as &$group) {
                    if (isset($expirations[$group['Radgroup']['groupname']])
                        && Utils::formatDate(
                            array(
                                $expirations[$group['Radgroup']['groupname']],
                                date('d M Y H:i:s')
                            ),
                            'dursign') >= 0
                    ) {
                        $group['Radgroup']['expiration'] = Utils::formatDate(
                            $expirations[$group['Radgroup']['groupname']],
                            'display'
                        );
                    } else {
                        $group['Radgroup']['expiration'] = -1;
                    }
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

    public function import() {
        if ($this->request->isPost()) {
            $handle = fopen($_FILES['data']['tmp_name']['importCsv']['file'], "r");
            $results = array();

            while (($fields = fgetcsv($handle)) != false) {
                switch ($fields[0]) {
                case 'Radgroup':
                    if (count($fields) >= 2) {
                        $group = new Radgroup();
                        $group->set('groupname', $fields[1]);

                        if (isset($fields[2])) {
                            $group->set('comment', $fields[2]);
                        }

                        if ($group->save()) {
                             $results[] = __('%s was added', $fields[1]);
                        } else {
                             $results[] = __('ERROR: %s was not added', $fields[1]);
                        }
                    }
                    break;
                case 'Radgroupcheck':
                    if (count($fields) == 4) {
                        $check = new Radgroupcheck();
                        $check->set('groupname', $fields[1]);
                        $check->set('attribute', $fields[2]);
                        $check->set('op', $fields[3]);
                        $check->set('value', $fields[4]);

                        if ($check->save()) {
                             $results[] = __('%s (%s) was added', $fields[1], $fields[2]);
                        } else {
                             $results[] = __('ERROR: %s (%s) was not added', $fields[1], $fields[2]);
                        }
                    }
                    break;
                case 'Radgroupreply':
                    if (count($fields) == 4) {
                        $reply = new Radgroupcheck();
                        $reply->set('groupname', $fields[1]);
                        $reply->set('attribute', $fields[2]);
                        $reply->set('op', $fields[3]);
                        $reply->set('value', $fields[4]);

                        if ($reply->save()) {
                             $results[] = __('%s (%s) was added', $fields[1], $fields[2]);
                        } else {
                             $results[] = __('ERROR: %s (%s) was not added', $fields[1], $fields[2]);
                        }
                    }
                    break;
                case 'Radusergroup':
                    if (count($fields) >= 2) {
                        $usergroup = new Radusergroup();
                        $usergroup->set('username', $fields[1]);
                        $usergroup->set('groupname', $fields[2]);

                        if (isset($fields[3])) {
                            $usergroup->set('priority', $fields[3]);
                        } else {
                            $usergroup->set('priority', 1);
                        }

                        if ($usergroup->save()) {
                             $results[] = __('%s was added to %s', $fields[1], $fields[2]);
                        } else {
                             $results[] = __('ERROR: %s was not added to %s', $fields[1], $fields[2]);
                        }
                    }
                    break;
                }
            }

            $this->set('results', $results);
        } else {
            $this->redirect(array('controller' => 'Radgroups', 'action' => 'index'));
        }
    }

    public function exportAll() {
       $ids =  $this->Radgroup->find('list', array('fields' => 'id'));

       $this->redirect(array('controller' => 'Radgroups', 'action' => 'export', implode($ids, ',') . '.csv'));
    }

    public function export($groupIds) {
        $groupsData = array();
        foreach (explode(',', $groupIds) as $groupId) {
            if (preg_match('#^[0-9]+$#', $groupId)) {
                $group = $this->Radgroup->read(null, $groupId);

                if ($group['Radgroup']) {
                    $checks = $this->Checks->getChecks($groupId);
                    $replies = $this->Checks->getReplies($groupId);
                    $usergroups = $this->Checks->getUserGroups($groupId);

                    $groupsData[] = array(
                        'Radgroup',
                        $group['Radgroup']['groupname'],
                        $group['Radgroup']['comment'],
                    );

                    foreach ($checks as $check) {
                        $groupsData[] = array(
                            'Radcheck',
                            $check['Radcheck']['groupname'],
                            $check['Radcheck']['attribute'],
                            $check['Radcheck']['op'],
                            $check['Radcheck']['value'],
                        );
                    }

                    foreach ($replies as $reply) {
                        $groupsData[] = array(
                            'Radreply',
                            $reply['Radreply']['groupname'],
                            $reply['Radreply']['attribute'],
                            $reply['Radreply']['op'],
                            $reply['Radreply']['value'],
                        );
                    }

                    foreach ($usergroups as $usergroup) {
                        $groupsData[] = array(
                            'Radusergroup',
                            $usergroup['Radusergroup']['username'],
                            $usergroup['Radusergroup']['groupname'],
                            $usergroup['Radusergroup']['priority'],
                        );
                    }
                }
            }
        }

        $this->set('groupsData', $groupsData);
        $this->set('filename', 'groups_' . date('d-m-Y'));
    }

    /**
     * Export severals groups.
     *
     * @param ids - array of group ID.
     */
    private function multipleExport($ids = array()) {
        if (isset($ids) && is_array($ids)) {
            try {
                $this->redirect(array('action' => 'export', implode(',', $ids) . '.csv'));

                $this->Session->setFlash(
                    __('Groups have been exported.'),
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
