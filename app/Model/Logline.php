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
    
    public function findLogs($page, $options=array(), $file="snacklog") {
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
                $cmd .= "--priority debug,notice,warn,err,crit,emerg ";
            }
            if ($options['priority'] == "notice") {
                $cmd .= "--priority notice,warn,err,crit,emerg ";
            }
            if ($options['priority'] == "warn") {
                $cmd .= "--priority warn,err,crit,emerg ";
            }
            if ($options['priority'] == "err") {
                $cmd .= "--priority err,crit,emerg ";
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
                $cmd .= "--between-dates " . $options['datefrom'] . " " . $options['dateto'];
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
                //debug($line);
                if(preg_match('/(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2})\s+([^\s]+)\s+([^\s]+):\s+\[(local\d+)\.(debug|info|notice|warn|err|crit|alert|emerg)\]\s+(.*)/', $line, $matches)) {
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
        $err = array();
        $lasts = array();
        foreach ($logs as $log) {
            //echo $log['Logline']['msg'];
            if(preg_match('/\d{2}:\d{2}:\d{2}\.\d{3}:\s+(%.+):\s+(.*)/', $log['Logline']['msg'], $matches)) {
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
                        //$lasts[$host][$msg] = $log['Logline']['datetime'];
                    }
                } else {
                    $err[$host] = array();
                    //$lasts[$host] = array();
                    
                }
            }
        }
        $res = array();
        $res['err'] = $err;
        $res['lasts'] = $lasts;
        return $res;
    }
}

?>
