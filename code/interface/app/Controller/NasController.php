<?php
App::import('Model', 'Backup');

class NasController extends AppController {
    public $helpers = array('Html', 'Form', 'JqueryEngine');
    public $uses = array('Nas');
    public $paginate = array(
        'limit' => 10,
        'order' => array('Nas.id' => 'asc')
    );
    public $components = array(
        'Filters' => array('model' => 'Nas'),
        'Session',
    	'BackupsChanges',
        'MultipleAction' => array('model' => 'Nas', 'name' => 'nas'),
    );

    public function isAuthorized($user) {
        
        if($user['role'] === 'admin' && in_array($this->action, array(
            'index', 'view',
        ))){
            return true;
        }

        return parent::isAuthorized($user);
    }

    public function beforeValidateForFilters() {
        unset($this->Nas->validate['nasname']['notEmpty']['required']);
        unset($this->Nas->validate['shortname']['notEmpty']['required']);
        unset($this->Nas->validate['secret']['notEmpty']['required']);
    }

    public function getRegexSynchronisation($args = array()) {
        if (!empty($args['input'])) {
            $data = &$this->request->data['Nas'][$args['input']];
            $regex = '(1 = 1';
            $ids = array();
            $flag = false;

            foreach ((array)$data as $choice) {
                switch ($choice) {
                case 'changed':
                    $ids = array_merge($ids, (array)$this->getUnwrittenNas());
                    $flag = true;
                    break;
                case 'notchanged':
                    $ids = array_merge($ids, (array)$this->getUnwrittenNas(true));
                    $flag = true;
                    break;
                }
            }

            if (!empty($ids)) {
                $regex .= ' AND id IN ('
                    . implode($ids, ',')
                    . '))';
            } else if ($flag) {
                $regex = '(1=0)'; 
            } else {
                $regex .= ')';
            }

            if (!empty($regex) && $regex != '(1 = 1)') {
                return $regex;
            }
        }
    }

    public function getUnwrittenNas($inverse = false) {
        $allnas = $this->Nas->find('all');

    	$unwrittenIds = array();

    	foreach ($allnas as $nas) {
            $changes = $this->BackupsChanges
                ->areThereChangesUnwrittenInThisNAS($nas);
            if ((!$inverse && $changes)
                || ($inverse && !$changes)
            ) {
                $unwrittenIds[] = $nas['Nas']['id'];
            }
    	}

        return $unwrittenIds;
    }
    
    public function index() {
        $this->MultipleAction->process(
            array(
                'success' => array(
                    'delete' => __('NAS have been removed.')
                ),
                'failed' => array(
                    'delete' => __('Unable to delete NAS.')
                ),
                'warning' => __('Please, select at least one NAS!'),
            )
        );

        $this->Filters->addStringConstraint(array(
            'fields' => array(
                'id',
                'nasname',
                'shortname',
                'type',
                'ports',
                'server',
                'community',
                'description',
            ),
            'input' => 'text',
            'ahead' => array('nasname','shortname', 'type', 'ports', 'server'),
        ));

        $this->Filters->addComplexConstraint(array(
            'select' => array(
                'items' => array(
                    'notchanged' => '<i class="icon-camera icon-green"></i> '
                    . __('Synchronized'),
                    'changed' => ' <i class="icon-camera icon-red"></i> '
                    . __('Not synchronized'),
                ),
                'input' => 'writemem',
                'title' => false,
            ),
            'callback' => array(
                'getRegexSynchronisation',
                array('input' => 'writemem'),
            )
        ));

        $this->Filters->paginate('nas');

    	$this->set('unwrittenids', $this->getUnwrittenNas());
    }

    public function view($id = null) {
        $this->Nas->id = $id;
        $nas = $this->Nas->read();

        $this->set('nas', $nas);

        $attributes = array();

        // Nas
        $attributes['IP address'] = $nas['Nas']['nasname'];
        $attributes['Short name'] = $nas['Nas']['shortname'];
        $attributes['Description'] = $nas['Nas']['description'];
        $attributes['Type'] = $nas['Nas']['type'];
        $attributes['Ports'] = $nas['Nas']['ports'];
        $attributes['Virtual server'] = $nas['Nas']['server'];
        $attributes['Community'] = $nas['Nas']['community'];

        $this->set('attributes', $attributes);
        $this->set(
            'showedAttr',
            array(
                'IP address',
                'Short name',
                'Description',
                'Type',
                'Ports',
            )
        );
	
    	$this->set('isunwritten', $this->BackupsChanges->areThereChangesUnwrittenInThisNAS($nas));
    }

    // method to display a warning field to restart the server after Nas changes
    public function alert_restart_server(){
        $this->Session->setFlash(
            __('You HAVE to restart the Radius server to apply NAS changes!') .
                '<a href="http://localhost/interface/systemDetails/restart/freeradius" 
                    class="btn btn-danger btn-mini" style="margin-left:10px;">  
                    <i class="icon-refresh icon-white"></i> ' . __('Restart Freeradius') .
                '</a>',
            'flash_error'
        );
    }

    public function add(){
        if($this->request->is('post')){
            $this->Nas->create();

            // init with default values
            $this->request->data['Nas']['type'] = 'other';
            $this->request->data['Nas']['ports'] = 1812;

            if ($this->Nas->save($this->request->data)) {
                $this->alert_restart_server();
                Utils::userlog(__('added NAS %s', $this->Nas->id));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('Unable to add NAS.'),
                    'flash_error'
                );
                Utils::userlog(__('error while adding NAS'), 'error');
            }
        }
    }

    public function edit($id = null)
    {
        $this->Nas->id = $id;
        if($this->request->is('get')){
            $this->request->data = $this->Nas->read();
        } else {
            if($this->Nas->save($this->request->data)){
                $this->alert_restart_server();
                Utils::userlog(__('edited NAS %s', $id));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('Unable to update NAS.'),
                    'flash_error'
                );
                Utils::userlog(__('error while editing NAS %s', $id), 'error');
            }
        }
    }

    public function delete($id = null)
    {
        if($this->request->is('get')){
            throw new MethodNotAllowedException();
        }

        $id = is_null($id) ? $this->request->data['Nas']['id'] : $id;

        if($this->Nas->delete($id)){
            $this->Session->setFlash(
                __('The NAS has been deleted.'),
                'flash_success'
            );
            Utils::userlog(__('deleted NAS %s', $id));
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(
                __('Unable to delete NAS.'));
            Utils::userlog(__('error while deleting NAS %s', $id), 'error');
        }
    }
}

?>
