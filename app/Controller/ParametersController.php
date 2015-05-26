<?php

// Configure::load('parameters');

class ParametersController extends AppController {
    public $helpers = array('Html', 'Form');
    public $uses = array('Parameter');

    public function index() {
        $this->Parameter->read();
        foreach ($this->Parameter->data['Parameter'] as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function email() {
        $this->Parameter->read();
        foreach ($this->Parameter->data['Parameter'] as $key => $value) {
            $this->set($key, $value);
        }
        
    }
    
    public function proxy() {
        $this->Parameter->read();
        foreach ($this->Parameter->data['Parameter'] as $key => $value) {
            $this->set($key, $value);
        }
    }
    
    public function cluster() {
        $this->Parameter->read();
        foreach ($this->Parameter->data['Parameter'] as $key => $value) {
            $this->set($key, $value);
        }
    }
    
    public function ad() {
        $this->Parameter->read();
        foreach ($this->Parameter->data['Parameter'] as $key => $value) {
            $this->set($key, $value);
        }
        //debug(checkdnsrr($this->Parameter->data['Parameter']['addomain'], "A"));
        if (isset($this->Parameter->data['Parameter']['addomain'])) {
            if (checkdnsrr($this->Parameter->data['Parameter']['addomain'], "A")) {
            //if(preg_match("/^Ping to winbindd succeeded.*/", $return, $matches)) {
                $return = shell_exec("sudo /usr/bin/perl /home/snack/interface/tools/scriptConfigAD.pl status");
                //debug($return);
                if(preg_match("/^checking the trust secret for domain(.*) via RPC calls (.*)/", $return, $matches)) {
                    if ($matches[2] == "succeeded") {
                        $this->set('adstatus', "Joined domain ".$matches[1]);
                    }
                }
                else {
                    $this->set('adstatus', $return);
                }
            } else {
                $this->set('adstatus', "SERVER UNREACHABLE");
            }
        }
        
        //}
    }
    
    public function edit() {
        $params = $this->Parameter->read();
        if ($this->request->is('post')) {
            Utils::cleanPath($this->request->data['Parameter']['scriptsPath']);
            Utils::cleanPath($this->request->data['Parameter']['certsPath']);
            if ($this->request->data['Parameter']['smtp_password'] == '') {
                $this->request->data['Parameter']['smtp_password'] = $params['smtp_password'];
            }
            /* if ($this->request->data['Parameter']['proxy_password'] == '') {
                $this->request->data['Parameter']['proxy_password'] = $params['proxy_password'];
            } */
            $this->Parameter->set($this->request->data);

            if ($this->Parameter->save()) {
                $this->Session->setFlash(
                    __('Parameters have been updated.'),
                    'flash_success'
                );
                Utils::userlog(__('Parameters have been updated.'));
                
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('Unable to update parameters.'),
                    'flash_error'
                );
                Utils::userlog(__('Unable to update parameters.'), 'error');
            }
        } else {
            $this->request->data = $this->Parameter->read();
        }
    }
    
    public function edit_general() {
        $params = $this->Parameter->read();
        if ($this->request->is('post')) {
            $this->Parameter->set($this->request->data);
            if ($this->Parameter->save()) {
                $this->Session->setFlash(
                    __('Parameters have been updated.'),
                    'flash_success'
                );
                Utils::userlog(__('Parameters have been updated.'));
                
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('Unable to update parameters.'),
                    'flash_error'
                );
                Utils::userlog(__('Unable to update parameters.'), 'error');
            }
        } else {
            $this->request->data = $this->Parameter->read();
        }
    }
    
    public function edit_email() {
        $params = $this->Parameter->read();
        if ($this->request->is('post')) {
            if ($this->request->data['Parameter']['smtp_password'] == '') {
                $this->request->data['Parameter']['smtp_password'] = $params['Parameter']['smtp_password'];
            }
            $this->Parameter->set($this->request->data);
            if ($this->Parameter->save()) {
                $this->Session->setFlash(
                    __('Parameters have been updated.'),
                    'flash_success'
                );
                Utils::userlog(__('Parameters have been updated.'));
                
                $this->redirect(array('action' => 'email'));
            } else {
                $this->Session->setFlash(
                    __('Unable to update parameters.'),
                    'flash_error'
                );
                Utils::userlog(__('Unable to update parameters.'), 'error');
            }
        } else {
            $this->request->data = $this->Parameter->read();
        }
    }
    
    public function edit_proxy() {
        $params = $this->Parameter->read();
        if ($this->request->is('post')) {
            if ($this->request->data['Parameter']['proxy_password'] == '') {
                $this->request->data['Parameter']['proxy_password'] = $params['Parameter']['proxy_password'];
            }
            $this->Parameter->set($this->request->data);
            if ($this->Parameter->save()) {
                $this->Session->setFlash(
                    __('Parameters have been updated.'),
                    'flash_success'
                );
                Utils::userlog(__('Parameters have been updated.'));
                
                $this->redirect(array('action' => 'proxy'));
            } else {
                $this->Session->setFlash(
                    __('Unable to update parameters.'),
                    'flash_error'
                );
                Utils::userlog(__('Unable to update parameters.'), 'error');
            }
        } else {
            $this->request->data = $this->Parameter->read();
        }
    }
    
    public function edit_cluster() {
        $params = $this->Parameter->read();
        if ($this->request->is('post')) {
            $this->Parameter->set($this->request->data);
            if ($this->Parameter->save()) {
                $this->Session->setFlash(
                    __('Parameters have been updated.'),
                    'flash_success'
                );
                Utils::userlog(__('Parameters have been updated.'));
                
                $this->redirect(array('action' => 'cluster'));
            } else {
                $this->Session->setFlash(
                    __('Unable to update parameters.'),
                    'flash_error'
                );
                Utils::userlog(__('Unable to update parameters.'), 'error');
            }
        } else {
            $this->request->data = $this->Parameter->read();
        }
    }
    
    public function edit_ad_group() {
        $return = shell_exec("sudo /usr/bin/perl /home/snack/interface/tools/scriptConfigAD.pl listgroups");
        $infos = explode("\n", $return);
        $adgroups = array();
        foreach ($infos as $info) {
            $group = explode('\\', $info);
            if (isset($group[1])) {
                if ($group[1] != '') {
                    $adgroups[$group[1]] = $group[1];
                }
            } else {
                $adgroups[$info] = $info;
            }
        }
        $this->set('adgroups', $adgroups);
        $params = $this->Parameter->read();
        $this->set('adgroup', $params['Parameter']['adgroupsync']);
        if ($this->request->is('post')) {
            $this->Parameter->set($this->request->data);
            if ($this->Parameter->save()) {
                $this->Session->setFlash(
                    __('Parameters have been updated.'),
                    'flash_success'
                );
                Utils::userlog(__('Parameters have been updated.'));
                
                $this->redirect(array('action' => 'ad'));
            } else {
                $this->Session->setFlash(
                    __('Unable to update parameters.'),
                    'flash_error'
                );
                Utils::userlog(__('Unable to update parameters.'), 'error');
            }
        }
    }
    
    public function edit_ad() {
        
                
        //debug($this->request);
        $params = $this->Parameter->read();
        if ($this->request->is('post')) {
            $request['Parameter'] = array(
                'adip' => $this->request->data['Parameter']['adip'],
                'addomain' => $this->request->data['Parameter']['addomain'],
            );
            $this->Parameter->set($request);
            if ($this->Parameter->save()) {
                $this->Session->setFlash(
                        __('Parameters have been updated.'), 'flash_success'
                );
                Utils::userlog(__('Parameters have been updated.'));
                shell_exec("sudo /usr/bin/perl /home/snack/interface/tools/scriptConfigAD.pl config "
                        . $this->Parameter->data['Parameter']['addomain']
                        . " "
                        . $this->Parameter->data['Parameter']['adip']
                        . " "
                        . $this->request->data['Parameter']['adminusername']
                        . " "
                        . $this->request->data['Parameter']['adminpassword']
                );        
                shell_exec("sudo service winbind restart");
                
            } else {
                $this->Session->setFlash(
                        __('Unable to update parameters.'), 'flash_error'
                );
                Utils::userlog(__('Unable to update parameters.'), 'error');
            }
        } else {
            $this->request->data = $this->Parameter->read();
        }
        //$return = shell_exec("wbinfo -p");
        
        $tmp = "";
        if (isset($this->Parameter->data['Parameter']['addomain']) && $this->Parameter->data['Parameter']['addomain'] != '') {
            if (checkdnsrr($this->Parameter->data['Parameter']['addomain'], "A")) {
                $return = shell_exec("sudo /usr/bin/perl /home/snack/interface/tools/scriptConfigAD.pl status");
                if (preg_match("/^checking the trust secret for domain(.*) via RPC calls (.*)/", $return, $matches)) {
                    if ($matches[2] == "succeeded") {
                        $this->set('adstatus', "Joined domain " . $matches[1]);
                    }
                } else {
                    $this->set('adstatus', $return);
                }
            } else {
                $this->set('adstatus', "SERVER UNREACHABLE");
            }
        }
    }
    
    public function cron() {
        $return = shell_exec("/home/snack/interface/tools/scriptCron.sh -l");
        $this->set('listcron', $return);
    }
    
    public function edit_cron($script) {
        $return = shell_exec("/home/snack/interface/tools/scriptCron.sh -l | grep $script");
        $REGEX = "/(.*".$script.".*)\n/";
        //debug ($REGEX);
        //debug($return);
        if (preg_match($REGEX, $return, $matches)) {
            $this->set('listcron', $matches[1]);
            $this->set('script', $script);
        }
        if ($this->request->is('post')) {
            debug($this->request->data);
            $data = $this->request->data;
            if ($data['Parameter']['crontype'] == 'cronregular') {
                if (preg_match("/(\d+)(\w+)/", $data['Parameter']['cronfreq'], $matches)) {
                    if ($matches[2] == "min") {
                        $str = "*/".$matches[1];
                    }
                    if ($matches[2] == "hour") {
                        $str = "0 */".$matches[1];
                    }
                    $return = shell_exec('/home/snack/interface/tools/scrCron.sh -m '.$script.' "'.$str.'"');
                }    
            }
            if ($data['Parameter']['crontype'] == 'cronhourmin') {
                $hour = $data['Parameter']['cronhour'];
                $min = $data['Parameter']['cronmin'];
                $str = $min." ".$hour;
                $return = shell_exec('/home/snack/interface/tools/scrCron.sh -m '.$script.' "'.$str.'"');
            }
        }
    }
    
    public function nagios() {
        $this->Parameter->read();
        foreach ($this->Parameter->data['Parameter'] as $key => $value) {
            $this->set($key, $value);
        }
        
    }
    
    public function edit_nagios() {
        $params = $this->Parameter->read();
        if ($this->request->is('post')) {
            if ($this->request->data['Parameter']['nagios_password'] == '') {
                $this->request->data['Parameter']['nagios_password'] = $params['Parameter']['nagios_password'];
            }
            $this->Parameter->set($this->request->data);
            if ($this->Parameter->save()) {
                $this->Session->setFlash(
                    __('Parameters have been updated.'),
                    'flash_success'
                );
                Utils::userlog(__('Parameters have been updated.'));
                
                $this->redirect(array('action' => 'nagios'));
            } else {
                $this->Session->setFlash(
                    __('Unable to update parameters.'),
                    'flash_error'
                );
                Utils::userlog(__('Unable to update parameters.'), 'error');
            }
        } else {
            $this->request->data = $this->Parameter->read();
        }
    }

    public function send_emailtest() {
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
        $subject = "SNACK - " . $matches[1] . " - TEST";
        $Email->subject($subject);
        $Email->send('This is a test');
        $this->redirect(
                array('action' => 'index')
        );
    }

    public function logs() {
        $params = $this->Parameter->read();
        $this->set('logs_delete_date', $params['Parameter']['logs_delete_date']);
        $this->set('logs_archive_date', $params['Parameter']['logs_archive_date']);
    }

    public function edit_logs() {
        $archive_dates = array();
        $archive_dates[] = "15";
        $archive_dates[] = "30";
        $archive_dates[] = "60";
        $archive_dates[] = "90";
        $delete_dates = array();
        $delete_dates[] = "30";
        $delete_dates[] = "60";
        $delete_dates[] = "90";
        $delete_dates[] = "180";
        $params = $this->Parameter->read();
        if ($this->request->is('post')) {
            $request = array();
            $request['Parameter'] = array();
            $delete_date = $delete_dates[$this->request->data['Parameter']['logs_delete_date']];
            $archive_date = $archive_dates[$this->request->data['Parameter']['logs_archive_date']];
            if ($archive_date >= $delete_date) {
                $this->Session->setFlash(
                    __('Archive date must be lower than delete date'),
                    'flash_error'
                );
                Utils::userlog(__('Unable to update parameters.'), 'error');
            }
            else {
                $request['Parameter']['logs_archive_date'] = $archive_date;
                $request['Parameter']['logs_delete_date'] = $delete_date;
                debug($request);
                $this->Parameter->set($request);
                if ($this->Parameter->save()) {
                    $this->Session->setFlash(
                        __('Parameters have been updated.'),
                        'flash_success'
                    );
                    Utils::userlog(__('Parameters have been updated.'));
                    $this->redirect(array('action' => 'logs'));
                } else {
                    $this->Session->setFlash(
                        __('Unable to update parameters.'),
                        'flash_error'
                    );
                    Utils::userlog(__('Unable to update parameters.'), 'error');
                }
            }
        } else {
            $this->request->data = $this->Parameter->read();
            $delete_date = $this->request->data['Parameter']['logs_delete_date'];
            $archive_date = $this->request->data['Parameter']['logs_archive_date'];
        }
        $this->set('archive_dates', $archive_dates);
        $this->set('archive_date', $archive_date);
        $this->set('delete_dates', $delete_dates);
        $this->set('delete_date', $delete_date);
    }
}

?>
