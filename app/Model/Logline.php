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
    
    public function findNas() {
        $nas = array();
        $nas[]="All Nas";
        $data = array();
        $data["aggs"] = array( "hosts" => array("terms" => array(
            "field" => "host",
            "order" => array("_count" => "asc")
        )));
        $data_string = json_encode($data, TRUE);
        $url = 'http://localhost:9200/_search?search_type=count';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);                                                                   
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);  
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        $data_decode = json_decode($data, true);
        foreach ($data_decode["aggregations"]["hosts"]["buckets"] as $data) {
            $nas[] = $data['key'];
        }
        return $nas;
    }

    public function findLogs($page=-1, $options=array()) {
        $arr = array();
        $levels = array_keys($this->levels);
        if (isset($options['pageSize'])) {
            $pageSize = $options['pageSize'];
        } else {
            $pageSize =  Configure::read('Parameters.paginationCount');
        }
        $loglines = array();
        $log = array();
        if (isset($options['pageSize'])) {
            $pageSize = $options['pageSize'];
        }
        /*$data = array("query" => array( "bool" => array( "must" => array( array("wildcard" => array("fluentd.severity" => "err"))), "must_not" => array(), "should" => array())), 
                                "from" => 0, 
                                "size" => 10, 
                                "sort" => array(), 
                                "facets" => new \stdClass(),
        );
        $data = array("query" => array( "bool" => array( "must" => array(), "must_not" => array(), "should" => array())), 
                      "from" => 0, 
                      "size" => $pageSize, 
                      "sort" => array(), 
                      "facets" => new \stdClass(),
        );*/
        $arrmustnot = array();
        $arrmust = array();
        //debug($options);
        if (isset($options['facility'])) {
            $arrmust[] = array("wildcard" => array("fluentd.facility" => $options['facility']));
        }
        if (isset($options['priority'])) {
            foreach($levels as $level) {
                if ($level == $options['priority']) {
                    break;
                }
                $arrmustnot[] = array("wildcard" => array("fluentd.severity" => $level));
                //debug($level);
            }
        }
        if (isset($options['host'])) {
            $arrmust[] = array("wildcard" => array("fluentd.host" => $options['host']));
        }
        if (isset($options['type'])) {
            if ($options['type'] == "voip") {
                $arrmust[] = array("query_string" => array("default_field" => "fluentd.message",
                                                         "query" => "*CALL_HISTORY")
                );
            }
        }
        /*if (isset($options['string'])) {
            $arrmust[] = array("query_string" => array("default_field" => "fluentd.message",
                                                         "query" => "*".$options['string']."*")
            );
        }*/
        if (isset($options['string'])) {
            //$arrmust[] = array("query_string" => array("default_field" => "fluentd.message",
                                                      //   "query" => $options['string'])
            $arrmust[] = array("match_phrase_prefix" => array("fluentd.message" => $options['string']));
        }
        if (isset($options['datefrom']) && isset($options['dateto'])) {
            $arrmust[] = array("range" => array("fluentd.@timestamp" => array("from" => $options['datefrom'], "to" => $options['dateto'])));
        }
        
        $from = ($page-1)*$pageSize;
        /*$data = array("query" => array( "bool" => array( "must" => isset($arrmust) ? array($arrmust) : array(), "must_not" => array(), "should" => array())), 
                      "from" => 0, 
                      "size" => $pageSize, 
                      "sort" => array(array("@timestamp" => array("order" => "desc"))), 
                      "facets" => new \stdClass(),
        );*/
        $data = array();
        $data["query"] = array( "bool" => array( "must" => $arrmust, "must_not" => $arrmustnot, "should" => array()));
        $data["from"] = $from;
        $data["size"] = $pageSize;
        $data["sort"] = array(array("@timestamp" => array("order" => "desc")));
        $data["facets"] = new \stdClass();
        /*$data = array("query" => array( "bool" => array( "must" => $arrmust, "must_not" => $arrmustnot, "should" => array())), 
                      "from" => $from, 
                      "size" => $pageSize, 
                      "sort" => array(array("@timestamp" => array("order" => "desc"))), 
                      "facets" => new \stdClass(),
        );*/
        //debug($data);
        //isset($input['escape']) ? $input['escape'] : true,
        $data_string = json_encode($data, TRUE);
        //debug($data_string);
        $url = 'http://localhost:9200/_search';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);  
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        //echo $data;

        $data_decode = json_decode($data, true);
        //debug($data_decode) ;
        $arr['count'] = $data_decode['hits']['total'];
        //debug($arr);
        //debug($data_decode['hits']['hits']);
        foreach ($data_decode['hits']['hits'] as $line) {
            if (isset($line['_source']['severity'])) {
                $log['Logline']['level'] = $line['_source']['severity'];
            }
            $log['Logline']['datetime'] = $line['_source']['@timestamp'];
            $log['Logline']['host'] = $line['_source']['host'];
            $log['Logline']['msg'] = $line['_source']['message'];
            $loglines[] = $log;
        }
        $arr['loglines'] = $loglines;
        //debug($arr);
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
        $constraints = array('severity' => 'err', 'pageSize' => '100000');
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
        $constraints = array('severity' => 'warn', 'pageSize' => '100000');
        $page = 1;
        $arr = $this->findLogs($page, $constraints);
        $logs = $arr['loglines'];
        $err = array();
        $lasts = array();
        //debug($logs);
        foreach ($logs as $log) {
            //echo $log['Logline']['msg'];
            if (isset($log['Logline']['level'])) {
                if($log['Logline']['level']=="warn") {
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
        }
        $res = array();
        $res['err'] = $err;
        $res['lasts'] = $lasts;
        return $res;
    }
    
    public function voiceNbCalls($constraints) {
        //$file="snacklog";
        //$pageSize=-1;
        debug($constraints);
        $page = 1;
        $results=array();
        /* Stats sur 15 jours*/
        $nb=15;
        $today = date("Y-m-d");
        //$today="2014-10-22";
        $date = new DateTime($today);
        $date->modify('-1 day');
        //debug($files);
        for ($i=0;$i<$nb;$i++) {
            $strdate=$date->format('Y-m-d');
            $startdate = $strdate."T00:00:00";
            $stopdate = $strdate."T23:59:59";
            
            if(isset($constraints['directorynumber'])) {
                $constraints2 = array('type' => 'voip', 'string' => 'PeerAddress '.$constraints['directorynumber'], 'datefrom' => $startdate, 'dateto' => $stopdate,'pageSize' => '100000');
                $arr = $this->findLogs($page, $constraints2);
                $logs = $arr['loglines'];
                //debug($logs);
                $calling=0;
                $called=0;
                foreach ($logs as $log) {
                    if (preg_match('/PeerAddress '.$constraints['directorynumber'].'.*CallOrigin 2/', $log['Logline']['msg'], $matches)) {
                        $calling++;
                    }
                    if (preg_match('/PeerAddress '.$constraints['directorynumber'].'.*CallOrigin 1/', $log['Logline']['msg'], $matches)) {
                        $called++;
                    }
                }
                $strdate2=$date->format('d M');
                $results['1'][$strdate2] = $calling;
                $results['2'][$strdate2] = $called;
                $results['0'][$strdate2]=$arr['count'];
                //debug($logs);
            } else {
                $constraints2 = array('type' => 'voip', 'datefrom' => $startdate, 'dateto' => $stopdate,'pageSize' => '100000');
                $arr = $this->findLogs($page, $constraints2);
                $logs = $arr['loglines'];
                //debug($logs);
                $strdate2=$date->format('d M');
                $results['0'][$strdate2]=$arr['count'];
            }
            //echo $strdate;
            //$cmd = "grep %VOIPAAA-5-VOIP_FEAT_HISTORY " . $this->path . $file;
            //$cmd .= " | awk -v datefrom=\"".$startdate."\" '$0 >= datefrom' | awk -v dateto=\"".$stopdate."\" '$0 <= dateto' | sort -u -t, -k7,7";
            
            /*if(isset($constraints['directorynumber'])) {
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
            }*/

            /* Next day */
            
            $date->modify('-1 day');
        }
        //debug($results);
        return $results;
    }
    
    /*
    public function voiceTopCalled ($file) {
        //$file="snacklog";
        $constraints = array('type' => 'voip', 'pageSize' => '100000');
        $page = 1;
        $arr = $this->findLogs($page, $constraints);
        $logs = $arr['loglines'];
        foreach ($logs as $log) {
            //debug($log);
            if (preg_match('/%VOIPAAA-5-VOIP_FEAT_HISTORY:.*cgn:(\d+),cdn:(\d+)/', $log['Logline']['msg'], $matches)) {
                    //$res['times'] = intval($matches[1]);
                    //$res['dest'] = strval($matches[2]);
                $results[strval($matches[2])] = intval($matches[1]);
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
    }*/
}

?>
