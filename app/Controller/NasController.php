<?php
App::uses('Security', 'Utility');
App::import('Model', 'Backup');

class NasController extends AppController {
    public $helpers = array('Html', 'Form', 'JqueryEngine');
    public $uses = array('Nas', 'Backup');
    public $paginate = array(
        'limit' => 10,
        'order' => array('Nas.id' => 'asc')
    );
    public $components = array(
        'Filters' => array('model' => 'Nas'),
        'Session',
        'MultipleAction' => array('model' => 'Nas', 'name' => 'nas'),
        'Security',
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

    public function getunBackupedNas() {
        $allnas = $this->Nas->find('all');
        $unBackupedNas = array();
        foreach ($allnas as $nas) {
            if ($nas['Nas']['nasname'] != "127.0.0.1") {
                if (!$this->Backup->isBackuped($nas['Nas']['nasname'])) {
                    $unBackupedNas[] = $nas['Nas']['nasname'];
                }
            }
        }
        return $unBackupedNas;
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

        /*$this->Filters->addComplexConstraint(array(
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
        ));*/

        $this->Filters->paginate('nas');

        $this->set('unBackupedNas', $this->getunBackupedNas());
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
        $attributes['Login'] = $nas['Nas']['login'];
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
                'Login',
            )
        );

        $this->set('unBackupedNas', $this->getunBackupedNas());
    }

    /**
     * method to display a warning field to restart the server after Nas changes
     */
    public function alert_restart_server(){
        $this->Session->setFlash(
            __('You HAVE to restart the Radius server to apply NAS changes!'),
            'flash_error_link',
            array(
                'title' => __('Restart Freeradius') . ' <i class="icon-refresh icon-white"></i>',
                'url' => array(
                    'controller' => 'systemDetails',
                    'action' => 'restart/freeradius',
                ),
                'style' => array(
                    'class' => 'btn btn-danger btn-mini',
                    'escape' => false,
                    'style' => 'margin-left: 15px;'
                ),
            )
        );
        Utils::userlog(__('restarted the Radius server'));
    }

    public function add(){
        if($this->request->is('post')){
            $this->Nas->create();
            // init with default values
            $this->request->data['Nas']['type'] = 'other';
            $this->request->data['Nas']['ports'] = 1812;
            debug($this->request->data);
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
        if($this->request->is('post')){
            $nas = $this->Nas->read();
            /*echo "<pre style=\"margin-left: 100px;\">";
                print_r($this->request->data);
                var_dump($this->request->data);
            echo "</pre>";
            debug($this->request->data);*/
            if (($this->request->data['Nas']['password'] == '' && $this->request->data['Nas']['confirm_password'] == '')) {
                unset($this->request->data['Nas']['password']);
                unset($this->request->data['Nas']['confirm_password']);
            }
            if (($this->request->data['Nas']['enablepassword'] == '') && ($this->request->data['Nas']['confirm_enablepassword'] == '')) {
                unset($this->request->data['Nas']['enablepassword']);
                unset($this->request->data['Nas']['confirm_enablepassword']);
            }
            //debug($this->request->data);
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
        } else {
            $this->request->data = $this->Nas->read();
            unset($this->request->data['Nas']['password']);
            unset($this->request->data['Nas']['confirm_password']);
            unset($this->request->data['Nas']['enablepassword']);
            unset($this->request->data['Nas']['confirm_enablepassword']);
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

    public function exporttocsv() {
        $nass = $this->Nas->find('all');
        foreach ($nass as $nas) {
            $nasData[] = array($nas['Nas']['nasname'], 
                               $nas['Nas']['shortname'], 
                               $nas['Nas']['description'], 
                               $nas['Nas']['version'], 
                               $nas['Nas']['image'], 
                               $nas['Nas']['serialnumber'], 
                               $nas['Nas']['model'], 
                               $nas['Nas']['login'],
                               $nas['Nas']['password'],
                               $nas['Nas']['enablepassword'],
                               $nas['Nas']['backuptype'],
                               );
        }
        $this->layout = false;
        $this->set('nasData', $nasData);
        $this->set('filename', 'nas_' . date('d-m-Y'));
    }

    public function import() {
        if ($this->request->isPost()) {
            $handle = fopen($_FILES['data']['tmp_name']['importCsv']['file'], "r");
            $results = array();
            $listnas = array();
            $col = array();
            while (($fields = fgetcsv($handle)) != false) {
                $i=0;
                $nas = array();
                foreach($fields as $field) {
                    $fieldlower = strtolower($field);
                    switch($fieldlower) {
                        case "nasname":
                        case "shortname":
                        case "description":
                        case "version":
                        case "image":
                        case "serialnumber":
                        case "model":
                        case "login":
                        case "password":
                        case "enablepassword":
                        case "secret":
                            $col[$i] = $fieldlower;
                            break;
                        default:
                            if ($field != "") {
                                $nas['Nas'][$col[$i]]=$field;
                            }
                            break;
                    }
                    $i++;
                }
                if (count($nas) > 0) {
                    $listnas[]=$nas;
                }
            }
            foreach ($listnas as $nas) {
                $this->Nas->create();
                if ($this->Nas->save($nas)) {
                    $results[$nas['Nas']['shortname']] = 1;
                    Utils::userlog(__('added NAS %s', $this->Nas->id));
                } else {
                    $results[$nas['Nas']['shortname']] = 0;
                    Utils::userlog(__('error while adding NAS'), 'error');
                }
            }
            $this->set('results', $results);
        }
    }

    /*
    * Get the configuration of all NAS
    */
    public function backupconfig($id) {
        $this->layout = "ajax";
        if (isset($id)) {
            $this->set('id', $id);
            $nas = $this->Nas->find('first', array(
                'conditions' => array('Nas.id' => $id)));
            if ($this->Nas->backupOneNas($nas['Nas']['nasname'], "Manual", $this->Session->read('Auth.User.username'))) {
                $this->set('res', '1');
            } else {
                $this->set('res', '0');
            }
        }
            /*
        if ($this->Nas->backupAllNas("Manual", $this->Session->read('Auth.User.username'))) {
            $this->Session->setFlash(__('Backup config succeed.'), 'flash_success');
            Utils::userlog(__('backup config succeed'));
            $this->redirect(array('action' => 'index'));
        }*/
    }

    /*
    * Download all configurations of NAS on the computer
    */
    public function downloadconfig() {
        $now = new DateTime('NOW');
        $strdate = $now->format("Y-m-d");
        $return = shell_exec("cd /home/snack/backups.git && zip ".APP."/tmp/nasconfig-".$strdate.".zip *");
        $this->response->file(APP."/tmp/nasconfig-".$strdate.".zip", array('download' => true, 'name' => "nasconfig-".$strdate.".zip"));
        return $this->response;
    }

    /*
    * Reinitalize the git repository of all configurations 
    */
    public function reinitconf() {
        $this->Backup->deleteAll(array('Backup.id >' => '0'), false);
        shell_exec("rm -rf /home/snack/backups.git/.git");
        $return = shell_exec("cd /home/snack/backups.git && git init");
        if(preg_match('/Reinitialized existing Git/', $return, $matches)) {
            Utils::userlog(__('reinitialize GIT'));
        }
        else {
            Utils::userlog(__('error while reinitializing GIT'), 'error');
        }
        $this->redirect(array('action' => 'index'));
    }
}
?>
