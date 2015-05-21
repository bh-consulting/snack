<?php
App::uses('Sanitize', 'Utility');
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');

class ReportsController extends AppController {
    
    public $paginate = array('maxLimit' => 10, 'limit' => 10, 'order' => array('id' => 'desc'));
    public $uses = array('Logline', 'Radacct', 'Raduser');
    public $components = array('Mpdf.Mpdf', 'Users');
    
    public function index() {
    }

    public function errors_reports() {
        //debug("test.txt");
        //2014-01-30 22:14:28
        
        $today=date('Y-m-d');
        $yesterday  = date('Y-m-d',strtotime("-1 days"));
        $this->set('str_date', $yesterday);
        $this->get_errors_from_nas();
        $this->get_warnings_from_nas();
        //$this->users_snack_login($yesterday);
        $this->users_radius_connect_ok($yesterday);
        $this->get_failures_by_users();
        $this->get_failures_by_nas();
        //$this->send();
        //debug("test");
        
    }

    public function sessions_reports() {
        $users = array();
        $usernames = $this->Radacct->query('select distinct username from radacct;');
        $sessions = array();
        foreach ($usernames as $username) {
            $session = $this->Radacct->query('select * from radacct where username="'.$username['radacct']['username'].'" order by radacctid desc limit 1;');
            $users[$session[0]['radacct']['radacctid']] =
                $this->Users->extendUsers($username['radacct']['username']);
            $session[0]['radacct']['duration'] = Utils::formatDate(
                array(
                    $session[0]['radacct']['acctstarttime'],
                    $session[0]['radacct']['acctstoptime'],
                ),
                'durdisplay'
            ); 

            if(is_null($session[0]['radacct']['acctstoptime'])) {
                $session[0]['radacct']['durationsec'] = Utils::formatDate(
                    array(
                    $session[0]['radacct']['acctstarttime'],
                    $session[0]['radacct']['acctstoptime'],
                    ),
                    'durdisplaysec'
                ); 
            } else
                $session[0]['radacct']['durationsec'] = -1;
            $sessions[] = $session[0];
        }
        $sessionsbydatetime = array();
        foreach ($sessions as $key => $value){
            $sessionsbydatetime[] = strtotime($value['radacct']['acctstarttime']);
        }
        array_multisort($sessionsbydatetime, SORT_DESC, $sessions);
        $this->set('sessions', $sessions);
        $this->set('users', $users);
    }

    public function exportpdf() {
        // initializing mPDF
        $this->Mpdf->init();

        // setting filename of output pdf file
        $this->Mpdf->setFilename('file.pdf');

        // setting output to I, D, F, S
        $this->Mpdf->setOutput('I');

        // you can call any mPDF method via component, for example:
        $this->Mpdf->SetWatermarkText("default");
    }
    
    public function get_errors_from_nas() {
        $res = $this->Logline->get_errors_from_NAS();
        $err = $res['err'];
        $lasts = $res['lasts'];
        $this->set('err', $err);
        $this->set('lasts', $lasts);
    }
    
    public function get_warnings_from_nas() {
        $res = $this->Logline->get_warnings_from_NAS();
        $warn = $res['err'];
        $warnlasts = $res['lasts'];
        $this->set('warn', $warn);
        $this->set('warnlasts', $warnlasts);
    }
    
    public function users_snack_login($date) {
        $today = new DateTime($date);
        $tomorroy = new DateTime($date);
        $tomorroy->add(new DateInterval('P1D'));

        $str_today = $today->format('Y-m-d') . "\n";
        $str_tomorrow = $tomorroy->format('Y-m-d') . "\n";
        //echo $str_today." ".$str_tomorrow;
        $constraints=array('facility' => 'local4', 'string' => 'logged in', 'pageSize' => '100000');
        $page=1;
        $arr = $this->Logline->findLogs($page, $constraints);
        $logs = $arr['loglines'];
        //debug($logs);
        //$conditions = array("Logline.msg like" => "%logged in%");
        //$logs = $this->Logline->find('all', array('order' => array('Logline.id DESC'), 'recursive' => 0, 'limit' => 2000, 'conditions' => $conditions));
        $snack_users = array();
        //$logs = $this->Logline->query('select id,msg,datetime from (select id,msg,datetime from logs order by id  desc limit 10000 ) Logline where msg like "%logged in%" and datetime>"' . $str_today . '" and datetime<"' . $str_tomorrow . '"');

        //echo "$nb connected users on $str_today<br>";
        //debug($logs);
        foreach ($logs as $log) {
            $date = new DateTime($log['Logline']['datetime']);
            $strdate = $date->format('Y-m-d H:i:s');
            //echo $log['Logline']['datetime']." : ".$log['Logline']['msg']."<br>";
            $snack_users[$strdate] = $log['Logline']['msg'];
        }
        $this->set('snack_users', $snack_users);
    }

    public function users_radius_connect_ok($date) {
        $today = new DateTime($date);
        $tomorroy = new DateTime($date);
        $tomorroy->add(new DateInterval('P1D'));

        $str_today = $today->format('Y-m-d') . "\n";
        $str_tomorrow = $tomorroy->format('Y-m-d') . "\n";
        //echo $str_today." ".$str_tomorrow;

        $conditions = array("Logline.msg like" => "%logged in%");
        //$logs = $this->Logline->find('all', array('order' => array('Logline.id DESC'), 'recursive' => 0, 'limit' => 2000, 'conditions' => $conditions));
        $logs = $this->Radacct->query('select radacct.username,user.comment,radacct.acctstarttime,radacct.nasipaddress from (select username, radacctid, acctstarttime,  nasipaddress from radacct order by radacctid desc limit 40) as radacct, raduser as user where radacct.username=user.username and radacct.acctstarttime>"' . $str_today . '" and radacct.acctstarttime<"' . $str_tomorrow . '"');
        //$logs = $this->Logline->query('select id,msg,datetime from (select id,msg,datetime from radacct order by id  desc limit 10000 ) Logline where msg like "%Login OK%" and datetime>"'.$str_today.'" and datetime<"'.$str_tomorrow.'"');
        //debug($logs);
        $this->set('sessions', $logs);
        /* foreach($logs as $log) {
          echo $log['radacct']['acctstarttime']." : ".$log['radacct']['username']."<br>";
          } */
    }

    public function get_failures_by_users() {
        //$logs = $this->Logline->query('select id,msg,datetime from (select id,msg,datetime from logs order by id  desc limit 100000 ) Logline where msg like "%Login incorrect%"');
        /*$constraints=array('facility' => 'local2', 'string' => 'Login incorrect', 'pageSize' => '100000');
        $page=1;
        $arr = $this->Logline->findLogs($page, $constraints);
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
                    $username = $this->Logline->query('select * from raduser where username="' . $login . '"');
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
        }*/
        $res = $this->Logline->get_failures();
        $this->set('failures', $res['usersnbfailures']);
        $this->set('users', $res['users']);
        $this->set('logins', $res['logins']);
        $this->set('usernames', $res['usernames']);
    }

    public function get_failures_by_nas() {
        //$logs = $this->Logline->query('select id,msg,datetime from (select id,msg,datetime from logs order by id  desc limit 100000 ) Logline where msg like "%Login incorrect%"');
        $constraints=array('facility' => 'local2', 'string' => 'Login incorrect', 'pageSize' => '100000');
        $page=1;
        $arr = $this->Logline->findLogs($page, $constraints);
        $logs = $arr['loglines'];
        $nasnbfailures=array();
        $lasts=array();
        
        foreach($logs as $log) {
            if (preg_match('/^Login incorrect\s*\(*(.*)\)*: \[(.*)\/.*\] \(from client (.*) port (\d+) /', $log['Logline']['msg'], $matches)) {
                //echo "ok ".$matches[1]." ".$matches[2]." ".$matches[3]." ".$matches[4]."\n";
                $login = $matches[2];
                $info = $matches[1];
                $nas = $matches[3];
            
                $info = explode("[", $log['Logline']['msg']);
                $info = explode("/", $info[1]);
                $info2 = explode(" ", $info[1]);
                //debug($info2);
                if (array_key_exists($nas, $nasnbfailures)) {
                    $nasnbfailures[$nas] = $nasnbfailures[$nas] + 1;
                } else {
                    $nasnbfailures[$nas] = 1;
                    $lasts[$nas] = $log['Logline']['datetime'];
                }
            }//echo $log['Logline']['datetime']." : ".$log['Logline']['msg']."<br>";
        }
        $this->set('nasfailures', $nasnbfailures);
        $this->set('naslasts', $lasts);
    }
    
    public function voice_reports_pdf($directorynumber="") {
        $file="snacklog";
        $dir = new Folder('/home/snack/logs');
        $files = $dir->find('snacklog.*');
        sort($files);
        $constraints=array();
        //debug($this->request->data);
        if ($directorynumber != "") {
            $constraints['directorynumber'] = $directorynumber;
            $this->set('directorynumber', $constraints['directorynumber']);
        }
        $arr = $this->Logline->voiceNbCalls($constraints);
        $this->set('nbappelsstats', $arr);
        //debug($arr);
        $results = $this->Logline->voiceTopCalled($file);
        //debug($results);
        $this->set('resultsCalled', $results);

        $results = $this->Logline->voiceTopCalling($file);
        //debug($results);
        $this->set('resultsOutgoingCalling', $results);
        $this->set('file', $file);
        $G = new phpGraph();
        $graph = $G->draw($arr,array(
                //'steps' => 50,
                'height'=>300,
                'width'=>700,
                'filled'=>true,
                'tooltips'=>true,
                'diskLegends' => true,
                'diskLegendsType' => 'label',
                'type' => array(
                    '0'=>'bar',
                    '1'=>'bar',
                    '2'=>'bar',
                    '4'=>'pie',
                ),
                'stroke' => array(
                    '0'=>'red',
                    '1'=>'blue',
                    '2'=>'green'
                ),
                'legends' => array(
                    '0'=>'Total',
                    '1'=>'Calling',
                    '2'=>'Called',
                ),
                'tooltipLegend' => array(
                    'calling'=>'Sample of legend : ',
                    'called'=>'Sample of legend : ',
                ),
                'title' => 'Calls',
            )
        );
        $this->set('graph', $graph);
        $file = new File(APP.'/webroot/img/graph.svg');
        $file->write($graph);
        $this->exportpdf();
        
    }
    
    public function voice_reports() {
        //debug($this->request->data);
        $file="snacklog";
        $dir = new Folder('/home/snack/logs');
        $files = $dir->find('snacklog.*');
        sort($files);
        $constraints=array();
        //debug($this->request->data);
        if (isset($this->request->data)) {
            if (isset($this->request->data['Reports']['directorynumber'])) {
                if ($this->request->data['Reports']['directorynumber'] != '') {
                    $constraints['directorynumber'] = $this->request->data['Reports']['directorynumber'];
                    $this->set('directorynumber', $constraints['directorynumber']);
                }
            }
            if (isset($this->request->data['Reports']['logfile'])) {
                $file=$files[$this->request->data['Reports']['logfile']];
            }
        }
        $arr = $this->Logline->voiceNbCalls($constraints);
        $this->set('nbappelsstats', $arr);
        //debug($arr);
        $results = $this->Logline->voiceTopCalled($file);
        //debug($results);
        $this->set('resultsCalled', $results);

        $results = $this->Logline->voiceTopCalling($file);
        //debug($results);
        $this->set('resultsOutgoingCalling', $results);
        $this->set('file', $file);
        $G = new phpGraph();
        $graph = $G->draw($arr,array(
                //'steps' => 50,
                'height'=>300,
                'width'=>700,
                'filled'=>true,
                'tooltips'=>true,
                'diskLegends' => true,
                'diskLegendsType' => 'label',
                'type' => array(
                    '0'=>'bar',
                    '1'=>'bar',
                    '2'=>'bar',
                    '4'=>'pie',
                ),
                'stroke' => array(
                    '0'=>'red',
                    '1'=>'blue',
                    '2'=>'green'
                ),
                'legends' => array(
                    '0'=>'Total',
                    '1'=>'Calling',
                    '2'=>'Called',
                ),
                'tooltipLegend' => array(
                    'calling'=>'Sample of legend : ',
                    'called'=>'Sample of legend : ',
                ),
                'title' => 'Calls',
            )
        );
        $this->set('graph', $graph);
    }

}
