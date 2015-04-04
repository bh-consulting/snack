<?php
class Logline extends AppModel {
	public $useTable = false;
	//public $primaryKey = 'id';
	//public $displayField = 'msg';
	public $name = 'Logline';
    public $path = '/home/snack/logs/';
   
    public $levels = array(
		'debug' => 'Debug',
		'info' => 'Info',
		'notice' => 'Notice',
		'warn' => 'Warning',
		'err' => 'Error',
		'crit' => 'Critical',
		'alert' => 'Alert',
		'emerg' => 'Emergency'
	);

	public $validate = array(
		'datefrom' => array(
			'rule' => array('datetime', 'dmy'),
			'message' => 'Format error with date from.',
			'allowEmpty' => true,
		),
		'dateto' => array(
			'rule' => array('datetime', 'dmy'),
			'message' => 'Format error with date to.',
			'allowEmpty' => true,
		),
		'severity' => array(
            'rule' => array(
                'inList',
                array(
                    'debug', 'info', 'notice', 'warn',
                    'err', 'crit', 'alert', 'emerg'
                )
            ),
			'message' => 'Format error with severity.',
			'allowEmpty' => true,
		)
	);
    
    public function findNas($file="snacklog") {
        $nas = array();
        $nas[]="All Nas";
        $cmd = 'cat '.$this->path.$file.' | cut -d" " -f2 | sort -r | uniq';
        $return = shell_exec($cmd);
        $infos = explode("\n", $return);
        foreach ($infos as $line) {
            if ($line != "") {
                $nas[] = $line;
            }
        }
        return $nas;
    }

    public function findLogs($page=-1, $options=array(), $file="snacklog") {
        $arr = array();
        $pageSize =  Configure::read('Parameters.paginationCount');
        $loglines = array();
        $log = array();
        if (isset($options['pageSize'])) {
            $pageSize = $options['pageSize'];
        }
        
        $cmd = "/home/snack/interface/tools/scriptLogs.sh -f " . $this->path . $file . " -n " . $pageSize . " --page " . $page . " ";
        if (isset($options['facility'])) {
            $cmd .= "--facility " . $options['facility'] . " ";
        }
        if (isset($options['priority'])) {
            if ($options['priority'] == "debug") {
                $cmd .= "--priority debug,notice,warn,err,crit,alert,emerg ";
            }
            if ($options['priority'] == "notice") {
                $cmd .= "--priority notice,warn,err,crit,alert,emerg ";
            }
            if ($options['priority'] == "warn") {
                $cmd .= "--priority warn,err,crit,alert,emerg ";
            }
            if ($options['priority'] == "err") {
                $cmd .= "--priority err,crit,alert,emerg ";
            }
            if ($options['priority'] == "crit") {
                $cmd .= "--priority crit,alert,emerg ";
            }
            if ($options['priority'] == "alert") {
                $cmd .= "--priority alert,emerg ";
            }
            if ($options['priority'] == "emerg") {
                $cmd .= "--priority emerg ";
            }
        }
        else {
            $cmd .= "--priority info,notice,warn,err,crit,emerg ";
        }
        if (isset($options['string'])) {
            if ($options['string'] != '') {
                $cmd .= "--string '" . $options['string'] . "' ";
            }
        }
        if (isset($options['host'])) {
            if ($options['host'] != '') {
                $cmd .= "--host " . $options['host'] . " ";
            }
        }
        if (isset($options['datefrom']) && isset($options['dateto'])) {
            if ($options['datefrom'] != '' && $options['dateto'] != '') {
                $cmd .= "--between-dates " . $options['datefrom'] . "/" . $options['dateto'] . " ";
            }
        }
        if (isset($options['type'])) {
            if ($options['type'] == 'voip') {
                $cmd .= "--voip ";
            }
        }
        $return = shell_exec($cmd);
        debug($cmd);
        //debug($return);
        $infos = explode("\n", $return);
        $arr['count'] = $infos[0];
        //debug($count);
        foreach ($infos as $line) {
            if ($line != '') {
                if(preg_match('/(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2})\s+([^\s]+)\s+([^\s]+):\s+\[(.*)\.(debug|info|notice|warning|err|crit|alert|emerg)\]\s+(.*)/', $line, $matches)) {
                    //debug($line);
                    $date = $matches[1];
                    $host = $matches[2];
                    $program = $matches[3];
                    $facility = $matches[4];
                    $priority = $matches[5];
                    $msg = $matches[6];
                    $log['Logline']['level'] = $priority;
                    $log['Logline']['datetime'] = $date;
                    $log['Logline']['host'] = $host;
                    $log['Logline']['msg'] = $msg;
                    $loglines[] = $log;
                }
            }
        }
        $arr['loglines'] = $loglines;
        return $arr;
    }
    
    public function get_AuthReq() {
        $listNas = array();
        $constraints=array('facility' => 'local2', 'string' => 'Login', 'pageSize' => '100000');
        $page=1;
        $arr = $this->findLogs($page, $constraints);
        $logs = $arr['loglines'];
        foreach ($logs as $log) {
            if (preg_match('/Login .* \(from client\s+(.*)\s+port.*\)/', $log['Logline']['msg'], $matches)) {
                if (($matches[1] != "localhost") && ($matches[1] != "loop")) {
                    if (!in_array($matches[1], $listNas)) {
                        $listNas[] = $matches[1];
                    }
                }
            }
        }
        return $listNas;
    }

    public function get_failures() {
        $constraints=array('facility' => 'local2', 'string' => 'Login incorrect', 'pageSize' => '100000');
        $page=1;
        $arr = $this->findLogs($page, $constraints);
        $logs = $arr['loglines'];
        $usersnbfailures = array();
        $users = array();
        $logins = array();
        $usernames = array();
        foreach ($logs as $log) {
            if (preg_match('/^Login incorrect\s*\(*(.*)\)*: \[(.*)\/.*\] \(from client (.*) port (\d+) /', $log['Logline']['msg'], $matches)) {
                $login = $matches[2];
                $info = $matches[1];
                
                $nasstr = $matches[3];
                $portstr = $matches[4];
                if (array_key_exists($login, $usersnbfailures)) {
                    $usersnbfailures[$login] = $usersnbfailures[$login] + 1;
                } else {
                    $usersnbfailures[$login] = 1;
                    $lasts[$login] = $log['Logline']['datetime'];
                    //debug($info);
                    //debug($log['Logline']['datetime']);
                    $username = $this->query('select * from raduser where username="' . $login . '"');
                    $port[$login] = $portstr;
                    $nas[$login] = $nasstr;
                    $date = new DateTime($log['Logline']['datetime']);
                    $users[$login]['last'] = $date->format('Y-m-d H:i:s');
                    $users[$login]['info'] = $info;
                    $users[$login]['port'] = $portstr;
                    $users[$login]['nas'] = $nasstr;
                    $logins[] = $login;
                    
                    //$last = $this->Logline->query('select datetime from logs where msg like "Login incorrect: ['.$username.'%"');
                    //debug($last);
                    if (count($username) > 0) {
                        $username[0]['raduser']['username'] = Utils::formatMAC(
                                        $username[0]['raduser']['username']
                        );
                        //$users['login'] = $username[0]['raduser'];
                        $usernames[] = $username[0]['raduser'];
                    } else {
                        $usernames[] = array('id' => '-1', 'username' => Utils::formatMAC($login));
                    }
                    if (Utils::isMAC($login)) {
                        $url = "http://api.macvendors.com/" . urlencode(Utils::formatMAC($login));
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
                        curl_setopt($ch, CURLOPT_PROXYPORT, Configure::read('Parameters.proxy_port'));
                        curl_setopt($ch, CURLOPT_PROXY, Configure::read('Parameters.proxy_ip'));
                        curl_setopt($ch, CURLOPT_PROXYUSERPWD, Configure::read('Parameters.proxy_login') . ":" . Configure::read('Parameters.proxy_password'));
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $response = curl_exec($ch);
                        if ($response) {
                            $users[$login]['vendor'] = $response;
                        } else {
                            $users[$login]['vendor'] = "NA";
                        }
                    } else {
                        $users[$login]['vendor'] = "";
                    }
                }
            }
            //echo $log['Logline']['datetime']." : ".$log['Logline']['msg']."<br>";
        }
        $res = array();
        $res['usersnbfailures'] = $usersnbfailures;
        $res['users'] = $users;
        $res['usernames'] = $usernames;
        $res['logins'] = $logins;
        return $res;
    }
    
    public function get_errors_from_NAS() {
        $constraints = array('priority' => 'err', 'pageSize' => '100000');
        $page = 1;
        $arr = $this->findLogs($page, $constraints);
        $logs = $arr['loglines'];
        //debug($logs);
        $err = array();
        $lasts = array();
        foreach ($logs as $log) {
            //echo $log['Logline']['msg'];
            if($log['Logline']['level']=="err") {
                //if(preg_match('/\d{2}:\d{2}:\d{2}\.\d{3}:\s+(%[^:]+):\s+(.*)/', $log['Logline']['msg'], $matches)) {
                if(preg_match('/\S+\s+([^:]+):\s+(.*)/', $log['Logline']['msg'], $matches)) {
                    $errtype = $matches[1];
                    $msg = $matches[2];
                    //echo $msg;
                    $host = $log['Logline']['host'];
                    if (array_key_exists($host, $err)) {
                        if (array_key_exists($errtype, $err[$host])) {
                            if (array_key_exists($msg, $err[$host][$errtype])) {
                                //$err[$host][$errtype] = $err[$host][$errtype] + 1;
                                $err[$host][$errtype][$msg] = $err[$host][$errtype][$msg] + 1;
                            }
                            else {
                                $err[$host][$errtype][$msg] = 1;
                                $date = new DateTime($log['Logline']['datetime']);
                                $lasts[$host][$errtype][$msg] = $date->format('Y-m-d H:i:s');
                            }
                        }
                        else {
                            $err[$host][$errtype] = array();
                            $err[$host][$errtype][$msg] = 1;
                            $date = new DateTime($log['Logline']['datetime']);
                            $lasts[$host][$errtype][$msg] = $date->format('Y-m-d H:i:s');
                        }
                    } else {
                        $err[$host] = array();
                        $err[$host][$errtype][$msg] = 1;
                        $date = new DateTime($log['Logline']['datetime']);
                        $lasts[$host][$errtype][$msg] = $date->format('Y-m-d H:i:s');
                    }
                }
            }
        }
        $res = array();
        $res['err'] = $err;
        $res['lasts'] = $lasts;
        return $res;
    }
    
    public function get_warnings_from_NAS() {
        $constraints = array('priority' => 'warning', 'pageSize' => '100000');
        $page = 1;
        $arr = $this->findLogs($page, $constraints);
        $logs = $arr['loglines'];
        $err = array();
        $lasts = array();
        //debug($logs);
        foreach ($logs as $log) {
            //echo $log['Logline']['msg'];
            if($log['Logline']['level']=="warning") {
                if(preg_match('/\S+\s+([^:]+):\s+(.*)/', $log['Logline']['msg'], $matches)) {
                    $errtype = $matches[1];
                    $msg = $matches[2];
                    $host = $log['Logline']['host'];
                    if (array_key_exists($host, $err)) {
                        if (array_key_exists($errtype, $err[$host])) {
                            if (array_key_exists($msg, $err[$host][$errtype])) {
                                //$err[$host][$errtype] = $err[$host][$errtype] + 1;
                                $err[$host][$errtype][$msg] = $err[$host][$errtype][$msg] + 1;
                            }
                            else {
                                $err[$host][$errtype][$msg] = 1;
                                $date = new DateTime($log['Logline']['datetime']);
                                $lasts[$host][$errtype][$msg] = $date->format('Y-m-d H:i:s');
                            }
                        }
                        else {
                            $err[$host][$errtype] = array();
                            $err[$host][$errtype][$msg] = 1;
                            $date = new DateTime($log['Logline']['datetime']);
                            $lasts[$host][$errtype][$msg] = $date->format('Y-m-d H:i:s');
                        }
                    } else {
                        $err[$host] = array();
                        $err[$host][$errtype][$msg] = 1;
                        $date = new DateTime($log['Logline']['datetime']);
                        $lasts[$host][$errtype][$msg] = $date->format('Y-m-d H:i:s');
                    }
                }
            }
        }
        $res = array();
        $res['err'] = $err;
        $res['lasts'] = $lasts;
        return $res;
    }
    
    public function voiceNbCalls($constraints) {
        $file="snacklog";
        $pageSize=-1;
        $age=40;
        $results=array();
        /* Stats sur 5 jours*/
        $nb=15;
        $today = date("Y-m-d");
        //$today="2014-10-22";
        $date = new DateTime($today);
        $dir = new Folder('/home/snack/logs');
        $files = $dir->find('snacklog-\d{4}-.*');
        sort($files);
        //debug($files);
        $index=count($files)-1;
        for ($i=0;$i<$nb;$i++) {
            $strdate=$date->format('Y-m-d');
            //debug($file." ".$strdate);
            $startdate = $strdate."T00:00:00";
            $stopdate = $strdate."T23:59:59";
            //echo $strdate;
            $cmd = "grep %VOIPAAA-5-VOIP_FEAT_HISTORY " . $this->path . $file;
            $cmd .= " | awk -v datefrom=\"".$startdate."\" '$0 >= datefrom' | awk -v dateto=\"".$stopdate."\" '$0 <= dateto' | sort -u -t, -k7,7";
            $strdate2=$date->format('d M');
            if(isset($constraints['directorynumber'])) {
                $cmd_calling = $cmd . " |grep -E \"cgn:[0-9]*" . $constraints['directorynumber'] . ",\" |wc -l";
                //debug($cmd_calling);
                $cmd_called = $cmd . " |grep -E \"cdn:[0-9]*" . $constraints['directorynumber'] . ",\" |wc -l";
                //debug($cmd_called);
                $return = shell_exec($cmd_calling);
                $infos = explode("\n", $return);
                $results['1'][$strdate2] = $infos[0];
                $return = shell_exec($cmd_called);
                $infos = explode("\n", $return);
                $results['2'][$strdate2] = $infos[0];
            }
            else {
                ///debug("test");
                $cmd .= " |wc -l";
                $return = shell_exec($cmd);
                //debug($return);
                $infos = explode("\n", $return);
                $results['0'][$strdate2]=$infos[0];
            }

            /* Next day */
            $nbDay=date('N', strtotime($strdate));
            if ($nbDay == 1) {
                if ($index>=0 && $index<count($files)) {
                    $file=$files[$index];
                    $index--;
                }
            }
            $date->modify('-1 day');
        }
        //debug($results);
        return $results;
    }
    
    public function voiceTopCalled ($file) {
        //$file="snacklog";
        $results = array();
        $return = shell_exec("grep %VOIPAAA-5-VOIP_FEAT_HISTORY ".$this->path . $file." | sort -u -t, -k7,7 | awk -F',' '{print $4}' | sort | uniq -c | sort -rn | head");
        $infos = explode("\n", $return);
        foreach ($infos as $line) {
            if ($line != '') {
                if (preg_match('/^\s*(\d+)\s+cdn:(\d+)/', $line, $matches)) {
                    //$res['times'] = intval($matches[1]);
                    //$res['dest'] = strval($matches[2]);
                    $results[strval($matches[2])] = intval($matches[1]);
                    //$results[] = $res;
                }
            }
        }
        //debug($results);
        return $results;
    }
    
    public function voiceTopCalling ($file) {
        //$file="snacklog";
        $results = array();
        $cmd = "grep %VOIPAAA-5-VOIP_FEAT_HISTORY ".$this->path . $file." | sort -u -t, -k7,7 | awk -F',' '{print $3}' | sort | uniq -c | sort -rn | head";
        //debug($cmd);
        $return = shell_exec($cmd);
        $infos = explode("\n", $return);
        foreach ($infos as $line) {
            if ($line != '') {
                if (preg_match('/^\s*(\d+)\s+cgn:(\d+)/', $line, $matches)) {
                    //$res['times'] = intval($matches[1]);
                    //$res['dest'] = strval($matches[2]);
                    $results[strval($matches[2])] = intval($matches[1]);
                    //$results[] = $res;
                }
            }
        }
        //debug($results);
        return $results;
    }
}

?>
