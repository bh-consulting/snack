<?php
App::uses('File', 'Utility');
class SystemDetailsController extends AppController {
    
    public $name = 'SystemDetails';
    public $helpers = array('Html', 'Form');

    public function isAuthorized($user) {
        
        if($user['role'] === 'admin' && in_array($this->action, array(
            'index', 'refresh'
        ))){
            return true;
        }

        return parent::isAuthorized($user);
    }

    public function index() {
        $values = preg_grep("/.*Not After : (.*)/", file(Utils::getServerCertPath()));
        foreach ( $values as $val ) {
            if(eregi("Not After : (.*)",$val,$regs)) {
                continue;
            }
        }
        $this->set('ca_expiration', $regs[1]);
        $this->set('hostname', $this->SystemDetail->getHostname());

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
            if(eregi("^upgraded ([0-9]*).*",$tmp,$regs)) {
                $this->set('nbupgraded', $regs[1]);
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
        //$tmp=strstr($return, "snack");
        //$tmp=strstr($tmp, "snack");
        if(eregi("([0-9]*) upgraded, ([0-9]*) newly installed, ([0-9]*) to remove and ([0-9]*) not upgraded",$return,$regs)) {
            $file = new File(APP.'tmp/updates', true, 0644);
            $file->write("upgraded $regs[1]\nnewly installed $regs[2]\ntoremove $regs[3]\nupgraded $regs[4]");
        }
        elseif(eregi("([0-9]*) mis à jour, ([0-9]*) nouvellement installés, ([0-9]*) à enlever et ([0-9]*) non mis à jour",$return,$regs)) {
            $file = new File(APP.'tmp/updates', true, 0644);
            $file->write("upgraded $regs[1]\nnewly installed $regs[2]\ntoremove $regs[3]\nupgraded $regs[4]");
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
        $return = shell_exec("sudo apt-get update && apt-get upgrade snack -s");
        $this->set('return', $return);
    }
    
    public function export() {
        $return = shell_exec("sudo /home/snack/interface/tools/scriptSnackExport.sh");
        $today = date('Ymd');
        $this->response->file(
            "conf/snack-conf-".$today.".tar.gz", array('download' => true, 'name' => 'snack-conf-'.$today.'.tar.gz')
        );
        /*$this->redirect(
            array('action' => 'index')
        );*/
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
}

?>
