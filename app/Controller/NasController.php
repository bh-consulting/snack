<?php
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::uses('Security', 'Utility');
App::import('Model', 'Backup');

class NasController extends AppController {
    public $helpers = array('Html', 'Form', 'JqueryEngine');
    public $uses = array('Nas', 'Backup', 'Raduser');
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

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Security->unlockedActions = array('addunconfigurednas');
    }

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
        unset($this->Nas->validate['backup']['notEmpty']['required']);
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
            if ($nas['Nas']['nasname'] != "127.0.0.1" && $nas['Nas']['backup']) {
                if (!$this->Backup->isBackuped($nas['Nas']['nasname'])) {
                    $unBackupedNas[] = $nas['Nas']['nasname'];
                }
            }
        }
        return $unBackupedNas;
    }

    public function getLastBackupNas() {
        $allnas = $this->Nas->find('all');
        $lastBackup = array();
        foreach ($allnas as $nas) {
            if ($nas['Nas']['nasname'] != "127.0.0.1") {
                $lastBackup[$nas['Nas']['nasname']] = $this->Backup->dateOfLastBackup($nas['Nas']['nasname']);
            }
        }
        return $lastBackup;
    }

    public function index() {
        $listNas = $this->Nas->find('all', array(
            'fields' => array('Nas.login', 'Nas.shortname')
        ));

        $listnaserr = array();
        $isnaserrors = false;
        foreach ($listNas as $nas) {
            $user = $this->Raduser->find('first', array(
                'fields' => array('Raduser.id', 'Raduser.username', 'Raduser.is_cisco'),
                'conditions' => array('Raduser.username' => $nas['Nas']['login'], 'Raduser.is_cisco' => 1)
            ));
            if (count($user) > 0) {
                if ($user['Raduser']['username'] != "snack") {
                    $isnaserrors = true;
                    $listnaserr[] = $nas;
                }
            }
        }
        $this->set('isnaserrors', $isnaserrors);
        $this->set('listnaserr', $listnaserr);

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
                'backup',
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
        $this->set('lastBackupNas', $this->getLastBackupNas());
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
        $attributes['Backup'] = $nas['Nas']['backup'];
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
                'Backup',
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
            //debug($this->request->data);
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
            unset($this->request->data['Nas']['showpass']);
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
            $key = Configure::read('Security.snackkey');           
            $secret64Enc = $this->request->data['Nas']['password'];
            $secret64Dec = base64_decode($secret64Enc);
            $password = Security::decrypt($secret64Dec,$key);
            $this->set('password', $password);
            $this->set('backup', $this->request->data['Nas']['backup']);
            
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
            if ($nas['Nas']['nasname'] != "127.0.0.1") {
                $nasData[] = array($nas['Nas']['nasname'], 
                               $nas['Nas']['shortname'], 
                               $nas['Nas']['description'],
                               $nas['Nas']['secret'],
                               $nas['Nas']['login'],
                               "",
                               "",
                               $nas['Nas']['backup'],
                               );
            }
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
            $line=0;
            while (($fields = fgetcsv($handle)) != false) {
                $i=0;
                $nas = array();
                foreach($fields as $field) {
                    $fieldlower = strtolower($field);
                    if ($line == 0) {
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
                            case "backup":
                            case "secret":
                                $col[$i] = $fieldlower;
                                break;
                        }
                    } else {
                        if ($field != "") {
                            $nas['Nas'][$col[$i]]=$field;
                        }
                    }
                    $i++;
                }
                if (count($nas) > 0) {
                    $listnas[]=$nas;
                }
                $line++;
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

    /*
    * Search text in all configurations 
    */
    public function searchinconfig() {
        $this->set('post', false);
        $dir = '/home/snack/backups.git';
        if($this->request->is('post')){
            $results = array();
            $pattern = $this->request->data['Nas']['searchtext'];
            //debug($pattern);
            $this->set('post', true);
            $listNas = $this->Nas->find('all', array(
                'fields' => array('Nas.nasname', 'Nas.shortname')
            ));
            foreach ($listNas as $nas) {
                if ($nas['Nas']['nasname'] != "127.0.0.1") {
                    $path=$dir."/".$nas['Nas']['nasname'];
                    //debug($path);
                    $file = new File($path, false, 0644);
                    if ($file->exists()) {
                        $tmp=$file->read(false, 'rb', false);
                        $tmp2 = str_replace(array("\n\n", "\r"), '', $tmp);
                        //debug($tmp2);
                        if (preg_match_all('/(.*'.$pattern.'.*)/', $tmp2, $matches)) {
                            $results[$nas['Nas']['nasname']] = $matches[1];
                        }
                    }

                    $file->close();
                    //debug($files);
                }
            }
            $this->set('results', $results);
            $this->set('pattern', $pattern);
        }
    }

    /*
    * Get the template csv for import
    */
    public function downloadcsvtemplate() {
        $this->render('/Nas/index');
        return $this->response->file(APP."/templates/nas-template.csv", array('download' => true, 'name' => "nas-template.csv"));
    }

    public function getInfos() {
        $this->Nas->getInfosAllNas();
        $this->redirect(array('action' => 'index'));
    }

    public function getInfosAAA() {
        $results = $this->Nas->getInfosAllNasAAA();
    }

    public function checkconfnas() {
        $list = array(
            'Radius servers',
            'Test servers on NAS',
            'Show clock',
            'Show CDP'
        );
        $this->set('list', $list);
        if (isset($this->request->data['Nas']['checktype'])) {
            $this->set('type', $list[$this->request->data['Nas']['checktype']]);
            if ($list[$this->request->data['Nas']['checktype']] == "Radius servers") {
                $results = $this->Nas->getInfosAllNasAAA();
                $this->set('results', $results);
            }
            if ($list[$this->request->data['Nas']['checktype']] == "Test servers on NAS") {
                $results = $this->Nas->testAllNasAAA();
                $this->set('results', $results);
            }
            if ($list[$this->request->data['Nas']['checktype']] == "Show clock") {
                $results = $this->Nas->getInfosAllNasClock();
                $this->set('results', $results);
            }
            if ($list[$this->request->data['Nas']['checktype']] == "Show CDP") {
                $results = $this->Nas->getInfosAllNasCDP();
                $this->set('results', $results);
                debug($results);
            }
        }
    }

    public function discover() {
        $hostsToDisplay = array("All", "All except Phones");
        $this->set('hostsToDisplay', $hostsToDisplay);
        $this->set('post', false);
        /* A SUPPRIMER */
        $this->set('login', 'snack');
        $this->set('secret64Enc', "YzlmNDQ3YWQ5NDM1M2JkZGYwZTMwYzgzMTI5MDQ1YTNkNGJlZmI5OTljMzRmMmFjY2M2YjgyMzA5ZmQ2ZmE0ZdisBvO8HNety6NG+HA7kC8wp6CHh8Wh/cfbBKXBrDwF");
        if($this->request->is('post')){ 
            $this->set('post', true);
            $listNasTodo = array();
            $listNasDone = array();
            $login = $this->request->data['Nas']['login'];
            $password = $this->request->data['Nas']['password'];
            $enablepassword = $this->request->data['Nas']['password'];
            $key = Configure::read('Security.snackkey');
            $secret = Security::encrypt($this->data['Nas']['password'], $key);
            $secret64Enc = base64_encode($secret);
            $this->set('login', $login);
            $this->set('secret64Enc', $secret64Enc);
            $nasname = $this->request->data['Nas']['ipaddress'];
            $depth = $this->request->data['Nas']['depth'];
            $error = false;
            if (!preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $nasname, $matches)) {
                $this->Session->setFlash("IP must be look like x.x.x.x", 'flash_error');
                $error = true;
            }
            if ($login == "") {
                $this->Session->setFlash("Login is empty", 'flash_error');
                $error = true;
            }
            if ($password == "") {
                $this->Session->setFlash("Password is empty", 'flash_error');
                $error = true;
            }
            if (!preg_match('/\d+/', $depth, $matches)) {
                $this->Session->setFlash("Depth must be an integer", 'flash_error');
                $error = true;
            }
            $this->set('error', $error);
            if ($error) {
                return;
            }
            $cmd="/home/snack/interface/tools/command.sh $nasname hostname $login $password $enablepassword";
            $return = shell_exec($cmd);
            $hostname = trim($return);
            $listNasTodo[$hostname] = $nasname;
            //debug($this->request->data['Nas']);
            $i=0;
            $results = array();
            while($i<$depth) {
                if (count($listNasTodo) > 0) {
                    foreach ($listNasTodo as $hostname=>$nas) {
                        $results[$hostname] = $this->Nas->getInfosNasCDP($nas, $login, $password, $enablepassword);
                        unset($listNasTodo[$hostname]);
                        foreach ($results[$hostname] as $neigh) {
                            if (!array_key_exists($neigh['hostname'], $results) && !array_key_exists($neigh['hostname'], $listNasTodo)) {
                                if (preg_match('/Switch/', $neigh['capabilities'], $matches)) {
                                    if (isset($neigh['ipaddress'])) {
                                        $listNasTodo[$neigh['hostname']] = $neigh['ipaddress'];
                                    }
                                }
                            }
                            if (!array_key_exists($neigh['hostname'], $listNasDone)) {
                                if (isset($neigh['ipaddress'])) {
                                    $listNasDone[$neigh['hostname']]['ipaddress'] = $neigh['ipaddress'];
                                }
                                if (isset($neigh['platform'])) {
                                    $listNasDone[$neigh['hostname']]['platform'] = $neigh['platform'];
                                }
                                if (isset($neigh['capabilities'])) {
                                    $listNasDone[$neigh['hostname']]['capabilities'] = $neigh['capabilities'];
                                }
                                if (isset($neigh['version'])) {
                                    $listNasDone[$neigh['hostname']]['version'] = $neigh['version'];
                                }
                            }
                        }
                    }
                } else {
                    break;
                }
                $i++;
            }
            $listNasRadius = array();
            foreach($listNasDone as $hostname=>$nas) {
                if (preg_match('/Switch\s+IGMP/',$nas['capabilities'])) {
                    $listNasRadius[$hostname] = $nas['ipaddress'];
                }
                elseif (preg_match('/Router/',$nas['capabilities'])) {
                    $listNasRadius[$hostname] = $nas['ipaddress'];
                }
            }
            $allnasconfigured = $this->Nas->find('all');
            $allnasnotconfigured = array();
            foreach($listNasRadius as $hostname=>$ipaddress) {
                $found = false;
                foreach($allnasconfigured as $nas) {
                    if ($hostname == $nas['Nas']['shortname']) {
                        $found = true;
                    }
                    if ($ipaddress == $nas['Nas']['nasname']) {
                        $found = true;
                    }
                }
                if (!$found) {
                    $allnasnotconfigured[$hostname] = $ipaddress;
                }
            }
            $this->set('allnasnotconfigured', $allnasnotconfigured);
            $this->set('listNasDone', $listNasDone);
            $this->Nas->createGraph($results, $hostsToDisplay[$this->request->data['Nas']['hoststodisplay']]);
            $this->set('results', $results);
        }
    }

    public function findmacaddress() {
        if($this->request->is('post')){ 
            $this->set('post', true);
            $mac = Utils::cleanMAC($this->request->data['Nas']['macaddress']);
            if (!Utils::isMAC($mac)) {
                $this->Session->setFlash("Mac Address format is not good", 'flash_error');
                return ;
            }
            $newstr = substr_replace($mac, ".", 4, 0);
            $ciscomac = substr_replace($newstr, ".", 9, 0);
            $results = $this->Nas->getMacAllNas($ciscomac);
            $this->set('results', $results);
        }
    }

    public function addunconfigurednas() {
        $this->layout = "ajax";
        //$this->set("data", var_dump($this->request->data));
        $allnas = $this->Nas->find('all');
        $found = false;
        foreach ($allnas as $nas) {
            if ($nas['Nas']['nasname'] != "127.0.0.1" && $nas['Nas']['backup']) {
                if ($nas['Nas']['login'] != "" && $nas['Nas']['password'] != "" && $nas['Nas']['enablepassword'] != "") {
                    $login = $nas['Nas']['login'];
                    $password = $nas['Nas']['password'];
                    $found = true;
                    break;
                }
            }
        }
        if (!$found) {
            $login = $this->request->data['login'];
            $password = $this->request->data['encpassword'];
        }
        $data = array();
        if($this->request->is('post')){
            $this->Nas->create();
            $data['Nas']['nasname'] = $this->request->data['ip'];
            $data['Nas']['description'] = $this->request->data['hostname'];
            $data['Nas']['shortname'] = $this->request->data['hostname'];
            $data['Nas']['type'] = "other";
            $data['Nas']['ports'] = 1812;
            $data['Nas']['login'] = $login;
            $data['Nas']['password'] = $this->Nas->getPassword($password);
            $data['Nas']['enablepassword'] = $this->Nas->getPassword($password);
            $data['Nas']['secret'] = bin2hex(openssl_random_pseudo_bytes(6));
            $data['Nas']['backup'] = "1";
            //$this->set('data', $data);
            //$this->set('ip', $this->request->data['ip']);
            $this->set('id', $this->request->data['id']);
            if ($this->Nas->save($data)) {
                $this->alert_restart_server();
                Utils::userlog(__('added NAS %s', $this->Nas->id));
                $this->set('res', "OK");
            } else {
                /*$this->Session->setFlash(
                    __('Unable to add NAS.'),
                    'flash_error'
                );*/
                Utils::userlog(__('error while adding NAS'), 'error');
                $this->set('res', "NOK");
            }
        }
    }
}
?>
