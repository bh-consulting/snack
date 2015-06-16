<?php
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
class SystemDetailsController extends AppController {
    
    public $name = 'SystemDetails';
    public $helpers = array('Html', 'Form', 'Js');
    public $components = array(
        'Process'
    );
    public $uses = array('Radcheck', 'Raduser', 'SystemDetail', 'nas');
    
    public function isAuthorized($user) {
        
        if($user['role'] === 'admin' && in_array($this->action, array(
            'index', 'refresh'
        ))){
            return true;
        }

        return parent::isAuthorized($user);
    }

    public function index() {
        $this->set('name', $this->SystemDetail->getName());

        $this->set('ca_expiration', $this->SystemDetail->getCAValidity());
        
        $this->set('hostname', $this->SystemDetail->getHostname());

        $this->set('release', $this->SystemDetail->getRelease());
        $this->set('version', $this->SystemDetail->getVersion($this->SystemDetail->getRelease()));
        $this->set('versionsnack', $this->SystemDetail->getVersionSnack());
        $uptimes = $this->SystemDetail->getUptimes();
        $this->set('uptime', $uptimes[0]);
        $this->set('idletime', $uptimes[1]);

        $this->set('curdate', $this->SystemDetail->getCurDate());

        $loads = $this->SystemDetail->getSystemLoad();
        $this->set('loadavg', $loads[0]);
        $this->set('tasks', $loads[1]);
        
        $cpu = $this->SystemDetail->getsystemLoadInPercent();
        $this->set('cpu', $cpu);
        
        $memory = $this->SystemDetail->getMemory();
        $this->set('freemem', $memory[0]);
        $this->set('totalmem', $memory[1]);
        $this->set('usedmem', $memory[2]);

        $disk = $this->SystemDetail->getDiskSpace();
        $this->set('freedisk', $disk[0]);
        $this->set('totaldisk', $disk[1]);
        $this->set('useddisk', $disk[2]);

        $this->set('intstats', $this->SystemDetail->getInterfacesStats());
        $this->set('ints', $this->SystemDetail->getInterfaces());

        $radiusUptime = $this->SystemDetail->checkService("freeradius");
        $this->set(
            'radiusstate',
            ($radiusUptime == -1) ? __("Disabled") : __("Enabled for ") 
            . $radiusUptime
        );

        $mysqlUptime = $this->SystemDetail->checkService("mysqld");
        $this->set(
            'mysqlstate',
            ($mysqlUptime == -1) ? __("Disabled") : __("Enabled for ")
            . $mysqlUptime
        );
        
        $nagiosUptime = $this->SystemDetail->checkService("nagios3");
        $this->set(
            'nagiosstate',
            ($nagiosUptime == -1) ? __("Disabled") : __("Enabled for ")
            . $nagiosUptime
        );

        $tftpUptime = $this->SystemDetail->checkService("tftp");
        $this->set(
            'tftpstate',
            ($tftpUptime == -1) ? __("Disabled") : __("Enabled for ")
            . $tftpUptime
        );

        $tdagentUptime = $this->SystemDetail->checkService("ruby");
        $this->set(
            'tdagentstate',
            ($tdagentUptime == -1) ? __("Disabled") : __("Enabled for ")
            . $tdagentUptime
        );
        $file = new File(APP.'tmp/updates', false, 0644);
        $tmp="";
        $this->set("updates", 0);
        if ($file->exists()) {
            $tmp=trim($file->read(false, 'rb', false));
            if(preg_match('/(\d+\.\d+-\d+)\s+(\d+\.\d+-\d+)/', $tmp, $matches)) {
                if (version_compare($matches[1], $matches[2]) >=0 ) {
                    $this->set("updates", $matches[1]);
                }
            }
        }
        $elasticstate = $this->SystemDetail->checkService("java");
        $this->set(
            'elasticstate',
            ($elasticstate == -1) ? __("Disabled") : __("Enabled for ")
            . $elasticstate
        );
        $elastichealth = $this->SystemDetail->getElasticClusterHealth();
        $this->set('elastichealth', $elastichealth);
    }

    public function refresh() {
        $this->redirect($this->referer());
    }

    public function restart($server) {
        if (isset($server)) {
            switch ($server) {
            case 'mysql':
                $result = Utils::shell('sudo /usr/sbin/service mysql restart');
                break;
            case 'freeradius':
                $result = Utils::shell('sudo /usr/sbin/service freeradius restart');
                break;
            case 'nagios':
                $result = Utils::shell('sudo /usr/sbin/service nagios3 restart');
                break; 
            case 'elasticsearch':
                $result = Utils::shell('sudo /usr/sbin/service elasticsearch restart');
                break;
            case 'tftp':
                $result = Utils::shell('sudo /usr/sbin/service tftpd-hpa restart');
                break;    
            }
            if (isset($result['code']) && $result['code'] == 0) {
                $this->Session->setFlash(
                    __('Server %s has been restarted.', $server),
                    'flash_success'
                );
                Utils::userlog(__('Server %s has been restarted.', $server));
            } else {
                $this->Session->setFlash(
                    __('Server %s cannot be restarted.', $server),
                    'flash_error'
                );
                Utils::userlog(__('Server %s cannot be restarted.', $server), 'error');
            }
        }

        $this->redirect(array('action' => 'index'));
    }
    
    public function checkupdates() {
        $this->SystemDetail->checkUpdates();
        $this->redirect(
            array('action' => 'index')
        );
    }
    
    public function upgrade($bool=false) {
        $file = new File(APP.'tmp/updates', false, 0644);
        $tmp="";
        $this->set("updates", 0);
        if ($file->exists()) {
            $tmp=trim($file->read(false, 'rb', false));
            if(preg_match('/(\d+\.\d+-\d+)\s+(\d+\.\d+-\d+)/', $tmp, $matches)) {
                if (version_compare($matches[1], $matches[2]) >=0 ) {
                    $this->set("updates", $matches[1]);
                }
            }
        }
        /* status :
              0 : no upgrade
              1 : upgrade started
              2 : upgrade in progress
        */
        $status=0;
        $return = trim(shell_exec('ps aux | grep "apt-get install snack" | grep -v "grep apt-get install snack" | wc -l'));
        if ($return < 1) {
            if ($bool) {
                $status=1;
                $cmd = "date '+%Y-%m-%d' && sudo /usr/bin/apt-get install snack -y --force-yes";
                $this->Process->run($cmd,APP.'tmp/logs/upgrade.log');
            } else {
                $status=0;
            }
        }
        else {
            $status=2;
        }
        $file = new File(APP.'tmp/logs/upgrade.log', true, 0644);
        $tmp=trim($file->read(false, 'rb', false));
        $file->close();
        $this->set('logupgrade', $tmp);
        $this->set('bool', $bool);
        $this->set('status', $status);
    }
    
    public function export() {
        $return = shell_exec("sudo /home/snack/interface/tools/scriptSnackExport.sh");
        //echo $return;
        /*$this->response->file(
            //"conf/snack-conf_BHC_2014-04-17_15-33.tar.gz", array('download' => true, 'name' => 'snack-conf_BHC_2014-04-17_15-33.tar.gz')    
            "conf/$return", array('download' => true, 'name' => $return)
        );*/
        //echo $return;
        $this->redirect(
            array('action' => 'backup')
        );
    }

    public function import() {
        debug($_FILES);
        if ($this->request->isPost()) {
            $path = $_FILES['data']['name']['importConf']['file'];
            $i = strripos($path, ".");
            $j = strripos(substr($path, 0, $i), ".");
            $ext = substr($path, $j);
            if (strcmp($ext, ".tar.gz") == 0) {
                $uploadfile = APP."/tmp/".$_FILES['data']['name']['importConf']['file'];
                if (move_uploaded_file($_FILES['data']['tmp_name']['importConf']['file'], $uploadfile)) {
                    //debug("test");
                    //$cmd = "sudo /home/snack/interface/tools/scriptSnackImport.sh ".APP."webroot/conf/".$path;
                    //exec($cmd , $ouput, $err);
                    if ($this->request->data['importConf']['force'] == "1") {
                        $return = shell_exec("sudo /home/snack/interface/tools/scriptSnackImport.sh --file ".$uploadfile." --force");
                    } else {
                        $return = shell_exec("sudo /home/snack/interface/tools/scriptSnackImport.sh --file ".$uploadfile);
                    }
                    debug($return);
                    $file = new File('/tmp/log-import', false, 0644);
                    $tmp="";
                    if ($file->exists()) {
                        $tmp=$file->read(false, 'rb', false);
                        //echo $tmp;
                        $res_rsync = -1;
                        $res_mysql = -1;
                        $res_versions = 0;
                        if(preg_match('/VERSIONS MISMATCH/', $tmp, $matches)) {
                            $res_versions = 1;
                        }
                        if(preg_match('/RSYNC RES :(.*)/', $tmp, $matches)) {
                            $res_rsync = intval($matches[1]);
                        }
                        if(preg_match('/MYSQL RES :(.*)/', $tmp, $matches)) {
                            $res_mysql = intval($matches[1]);
                        }
                        if ($res_mysql == 0 && $res_rsync == 0 && $res_versions == 0) {
                            $this->Session->setFlash(
                                __('Import succeded.'),
                                'flash_success'
                            );
                        }
                        else {
                            $this->Session->setFlash(
                                __('Import failed.'),
                                'flash_error'
                            );
                        }
                        $this->redirect(
                            array('action' => 'index')
                        );
                    }
                    $file = new File($uploadfile);
                    $file->delete();
                }
            }
        }
    }
    
    public function backup() {
        $dir = new Folder(APP.'/conf');
        $pageSize =  Configure::read('Parameters.paginationCount');
        $files = $dir->find('snack-conf_.*');
        sort($files);
        $files=array_reverse($files);
        $totalPages = intval(floor(count($files)/$pageSize)+1);
        //debug($totalPages);
        if (isset($this->passedArgs['page'])) {
            $page = $this->passedArgs['page'];
        }
        else {
            $page = 1;
        }
        $index = ($page-1) * $pageSize;
        $listfiles = array();
        for ($i = 0; $i < $pageSize; $i ++) {
            if (isset($files[$index + $i])) {
                $listfiles[] = array('name' => $files[$index + $i]);
            }
        }
        sort($listfiles);
        $listfiles = array_reverse($listfiles);
        $this->set('listfiles', $listfiles);
        $this->set('page', $page);
        $this->set('totalPages', $totalPages);
    }
    
    public function download_backup($name) {
        $this->response->file(APP."/conf/".$name);
        // Return response object to prevent controller from trying to render
        // a view
        return $this->response;
    }
    
    public function send_backup() {
        App::uses('CakeEmail', 'Network/Email');
        $Email = new CakeEmail();
        if (Configure::read('Parameters.smtp_login') != '') {
            $Email->config(array('transport' => 'Smtp',
                'port' => Configure::read('Parameters.smtp_port'),
                'host' => Configure::read('Parameters.smtp_ip'),
                'username' => Configure::read('Parameters.smtp_login'),
                'password' => Configure::read('Parameters.smtp_password')));
        } else {
            $Email->config(array('transport' => 'Smtp',
                'port' => Configure::read('Parameters.smtp_port'),
                'host' => Configure::read('Parameters.smtp_ip')));
        }
        $Email->emailFormat('both');
        $Email->from(array(Configure::read('Parameters.smtp_email_from') => 'SNACK'));
        $emails = explode(';', Configure::read('Parameters.configurationEmail'));
        $listemails = array();
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $listemails[] = $email;
            }
        }
        $Email->to($listemails);
        $values = preg_grep("/Issuer: C=FR, ST=France, O=B.H. Consulting, CN=/", file(Utils::getServerCertPath()));
        foreach ($values as $val) {
            if (preg_match('/\Issuer:.*CN=(.*)/', $val, $matches)) {
                continue;
            }
        }
        $subject = "SNACK - " . $matches[1] . " - CONFIG";
        $Email->subject($subject);
        $return = shell_exec("sudo /home/snack/interface/tools/scriptSnackExport.sh");
        $infos = explode("\n", $return);
        $name = $infos[0];
        $Email->attachments(array(
            $name => array(
                'file' => APP . 'conf/' . $name,
                'mimetype' => 'application/gzip',
                'contentId' => '123456789'
            )
        ));
        $Email->send('Configuration');
        $this->redirect(
                array('action' => 'index')
        );
    }

    public function delete_backup($name) {
        $path = APP . "conf/" . $name;
        $file = new File($path);
        if ($file->delete()) {
            
            $this->Session->setFlash(
                    __('The backup %s has been deleted.', $name), 'flash_success'
            ); 
        }
        else {
            $this->Session->setFlash(
                    __('The backup %s has not been deleted.', $name), 'flash_error'
            );
        }
        $this->redirect(
                    array('action' => 'backup')
            );
    }
    
    public function ha() {
        //echo '<script type="text/javascript" src="/js/snack.js">updateProgress();</script>';
        //echo '<script>alert("toto");</script>';
        $dir = new Folder(APP.'tmp/ha');
        $results = array();
        $pageSize =  Configure::read('Parameters.paginationCount');
        $files = $dir->find('ha-[0-9]{4}-[0-9]{2}-[0-9]{2}_[0-9]{2}-[0-9]{2}\.log');
        sort($files);
        $files=array_reverse($files);
        $totalPages = intval(floor(count($files)/$pageSize)+1);
        if (isset($this->passedArgs['page'])) {
            $page = $this->passedArgs['page'];
        }
        else {
            $page = 1;
        }
        $index = ($page-1) * $pageSize;
        $file_list = array();
        for ($i = 0; $i < $pageSize; $i ++) {            
            if (isset($files[$index + $i])) {
                $rsync = -1;
                $mysql = -1;
                $file = new File(APP.'tmp/ha/'.$files[$index + $i], false, 0644);
                $tmp=$file->read(false, 'rb', false);
                $res_versions = 0;
                if(preg_match('/VERSIONS MISMATCH/', $tmp, $matches)) {
                    $res_versions = 1;
                }
                if (preg_match('/RSYNC RES :([0-9]+)/', $tmp, $matches)) {
                    $rsync = $matches[1];
                }
                if (preg_match('/MYSQL RES :([0-9]+)/', $tmp, $matches)) {
                    $mysql = $matches[1];
                }
                if (preg_match('/IP:([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/', $tmp, $matches)) {
                    $ip = $matches[1];
                }
                if (isset($ip) && isset($files)) {                
                    if ($rsync == 0 && $mysql == 0 && $res_versions == 0) {
                        $file_list[] = array('name' => $files[$index + $i], 'results' => 0, 'slave' => $ip);
                    } else {
                        $file_list[] = array('name' => $files[$index + $i], 'results' => 1, 'slave' => $ip);
                    }
                }
            }     
        }
        //nat_sort($file_list);
        $this->set('listlogs', $file_list);
        $this->set('page', $page);
        $this->set('totalPages', $totalPages);
    }
    
    public function halog($name) {
        //debug($path);
        $log = file_get_contents(APP.'tmp/ha/'.$name);
        $this->set('log', $log);
        $this->layout = 'ajax';
    }
    
    public function tests() {
        $users = array();
        $arr = $this->Raduser->query('select username from raduser;');
        foreach ($arr as $user) {
            $users[] = $user['raduser']['username'];
        }
        $this->set('user', 0);
        $this->set('users', $users);
    }
    
    public function test_users($username, $password="", $authtype) {
        $nas = $this->Raduser->query('select nasname,secret from nas where nasname="127.0.0.1";');
        foreach ($nas as $n) {
            $nasname=$n['nas']['nasname'];
            $secret=$n['nas']['secret'];
        }

        $results=array();
        $usernames = $this->Raduser->query('select * from raduser where username="'.$username.'";');
        if (count($usernames)>0) {
            $log = $this->SystemDetail->check_auth_user($username, $password, $authtype, $nasname, $secret);
            if (preg_match('/Received Access-Accept packet/', $log, $matches)) {
                $result=1;
            } elseif (preg_match('/Received Access-Reject packet/', $log, $matches)) {
                $result=0;
            } elseif (preg_match('/SUCCESS/', $log, $matches)) {
                $result=1;
            } elseif (preg_match('/FAILURE/', $log, $matches)) {
                $result=0;
            }
            else {
                $result=-1;
            }
            $this->set('username', $username);
            $this->set('password', $password);
            $this->set('comment', $usernames[0]['raduser']['comment']);
            if ($usernames[0]['raduser']['is_windowsad']) {
                $this->set('type', 'windowsad');
            }
            if ($usernames[0]['raduser']['is_phone']) {
                $this->set('type', 'phone');
            }
            if ($usernames[0]['raduser']['is_loginpass']) {
                $this->set('type', 'loginpass');
            }
            if ($usernames[0]['raduser']['is_cert']) {
                $this->set('type', 'cert');
            }
            if ($usernames[0]['raduser']['is_mac']) {
                $this->set('type', 'mac');
            }
            if ($usernames[0]['raduser']['is_cisco']) {
                $this->set('type', 'cisco');
            }
            $this->set('authtype', $authtype);
            $this->set('result', $result);
            $this->set('log', $log);
        }
        //For debug
        // ntlm_auth --request-nt-key --domain=BH-CONSULTING --username=Administrator --password=xxx
        //
        
        $this->layout = 'ajax';
        //   $this->render('testslog');
        //debug($results);
    }
    
    public function notifications() {
        $results=array();
        $this->SystemDetail->checkProblem($results);
        //debug($results);
        $this->set('results', $results);
    }
}

?>
