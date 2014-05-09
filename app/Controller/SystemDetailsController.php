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
        $file = new File(APP.'tmp/updates', false, 0644);
        $tmp="";
        if ($file->exists()) {
            $tmp=$file->read(false, 'rb', false);
            if(preg_match('/^upgraded ([0-9]*).*/', $tmp, $matches)) {
                $this->set('nbupgraded', $matches[1]);
            }
        }
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
        $return = shell_exec("sudo apt-get update && apt-get upgrade snack -s");
        if(preg_match('/([0-9]*) upgraded, ([0-9]*) newly installed, ([0-9]*) to remove and ([0-9]*) not upgraded/', $return, $matches)) {
            $file = new File(APP.'tmp/updates', true, 0644);
            $file->write("upgraded $matches[1]\nnewly installed $matches[2]\ntoremove $matches[3]\nupgraded $matches[4]");
        }
        elseif(preg_match('/([0-9]*) mis à jour, ([0-9]*) nouvellement installés, ([0-9]*) à enlever et ([0-9]*) non mis à jour/', $return, $matches)) {
            $file = new File(APP.'tmp/updates', true, 0644);
            $file->write("upgraded $matches[1]\nnewly installed $matches[2]\ntoremove $matches[3]\nupgraded $matches[4]");
        }
        else {
            $this->Session->setFlash(
                __('Unable to check updates.'),
                'flash_error');
        }
        
        $this->redirect(
            array('action' => 'index')
        );
    }
    
    public function upgrade() {
        $cmd = "sudo apt-get upgrade snack -y --force-yes";
        $this->Process->run($cmd,APP.'tmp/logs/upgrade.log');
        
        $this->set('return', $return);
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
            if ($_FILES['data']['type']['importConf']['file'] == 'application/gzip') {
                debug("test");
                $return = shell_exec("sudo /home/snack/interface/tools/scriptSnackImport.sh ".$_FILES['data']['tmp_name']['importConf']['file']);
                debug($return);
            }
        }
    }
    
    public function backup() {
        $dir = new Folder(APP.'webroot/conf');
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
    
    public function send_backup() {
        App::uses('CakeEmail', 'Network/Email');
        $Email = new CakeEmail();
        $Email->config(array('transport' => 'Smtp',
                             'port' => Configure::read('Parameters.smtp_port'),
                             'host' => Configure::read('Parameters.smtp_ip'),
                             'username' => Configure::read('Parameters.smtp_login'),
                             'password' => Configure::read('Parameters.smtp_password')));
        $Email->emailFormat('both');
        $Email->from(array(Configure::read('Parameters.smtp_email_from') => 'SNACK'));
        $emails = explode(';', Configure::read('Parameters.configurationEmail'));
        $listemails = array();
        foreach ( $emails as $email) {
            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $listemails[] = $email;
            }
        }
        $Email->to($listemails);
        $values = preg_grep("/Issuer: C=FR, ST=France, O=B.H. Consulting, CN=/", file(Utils::getServerCertPath()));
        foreach ( $values as $val ) {
            if( preg_match('/\Issuer:.*CN=(.*)/', $val, $matches)) {
                continue;
            }
        }
        $subject = "SNACK - ".$matches[1]." - CONFIG";
        $Email->subject($subject);
        $return = shell_exec("sudo /home/snack/interface/tools/scriptSnackExport.sh");
        $infos = explode("\n", $return);
        $name = $infos[0];
        $Email->attachments(array(
            $name => array(
                'file' => 'conf/'.$name,
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
        $file = new File(WWW_ROOT . "conf/" . $name);
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
                if (preg_match('/RSYNC RES :([0-9]+)/', $tmp, $matches)) {
                    $rsync = $matches[1];
                }
                if (preg_match('/MYSQL RES :([0-9]+)/', $tmp, $matches)) {
                    $mysql = $matches[1];
                }
                if (preg_match('/IP:([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/', $tmp, $matches)) {
                    $ip = $matches[1];
                }                
                if ($rsync == 0 && $mysql == 0) {
                    $file_list[] = array('name' => $files[$index + $i], 'results' => 0, 'slave' => $ip);
                } else {
                    $file_list[] = array('name' => $files[$index + $i], 'results' => 1, 'slave' => $ip);
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
        $results=array();
        $nas = $this->Raduser->query('select nasname,secret from nas where nasname="127.0.0.1";');
        foreach ($nas as $n) {
            $nasname=$n['nas']['nasname'];
            $secret=$n['nas']['secret'];
        }
        $usernames = $this->Raduser->query('select username,comment from raduser;');
        foreach ($usernames as $username) {
            $this->SystemDetail->tests_users($username['raduser']['username'], $nasname, $secret, $results, $username['raduser']['comment']);
        }
        $this->set('results', $results);
    }

    public function testslog($username) {
        $return = shell_exec("getconf LONG_BIT");
        if ($return == "64\n") {
            $this->eapol="eapol_test_64";
        }
        elseif ($return == "32\n") {
            $this->eapol="eapol_test_x86";
        }
        $results=array();
        $tls = 0;
        $ttls = 0;
        $nasporttype = "";
        $nas = $this->Raduser->query('select nasname,secret from nas where nasname="127.0.0.1";');
        foreach ($nas as $n) {
            $nasname=$n['nas']['nasname'];
            $secret=$n['nas']['secret'];
        }
        $this->SystemDetail->tests_users($username, $nasname, $secret, $results, "");
        //debug($results[$username]['res']);
        $this->set('log', $results[$username]['res']);
        $this->layout = 'ajax';
    }
}

?>
