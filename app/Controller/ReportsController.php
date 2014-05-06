<?php
App::uses('Sanitize', 'Utility');

class ReportsController extends AppController {
    
    public $paginate = array('maxLimit' => 10, 'limit' => 10, 'order' => array('id' => 'desc'));
    public $uses = array('Logline', 'Radacct', 'Raduser');
    
    public function index() {
        //debug("test.txt");
        //2014-01-30 22:14:28
        
        $today=date('Y-m-d');
        $yesterday  = date('Y-m-d',strtotime("-1 days"));
        $this->set('str_date', $yesterday);
        $this->users_snack_login($yesterday);
        $this->users_radius_connect_ok($yesterday);
        $this->get_failures_by_users();
        $this->get_failures_by_nas();
        //$this->send();
    }

    public function users_snack_login($date) {
        $today = new DateTime($date);
        $tomorroy = new DateTime($date);
        $tomorroy->add(new DateInterval('P1D'));

        $str_today = $today->format('Y-m-d') . "\n";
        $str_tomorrow = $tomorroy->format('Y-m-d') . "\n";
        //echo $str_today." ".$str_tomorrow;
        $logs = $this->Logline->find('all', array(
            'limit' => 12000,
            'contain' => array('Logline' => array(
                    "Logline.msg like" => "%logged in%",
                )),
        ));
        //debug($logs);
        //$conditions = array("Logline.msg like" => "%logged in%");
        //$logs = $this->Logline->find('all', array('order' => array('Logline.id DESC'), 'recursive' => 0, 'limit' => 2000, 'conditions' => $conditions));
        $snack_users = array();
        $logs = $this->Logline->query('select id,msg,datetime from (select id,msg,datetime from logs order by id  desc limit 10000 ) Logline where msg like "%logged in%" and datetime>"' . $str_today . '" and datetime<"' . $str_tomorrow . '"');

        //echo "$nb connected users on $str_today<br>";
        //debug($logs);
        foreach ($logs as $log) {
            //echo $log['Logline']['datetime']." : ".$log['Logline']['msg']."<br>";
            $snack_users[$log['Logline']['datetime']] = $log['Logline']['msg'];
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
        $logs = $this->Logline->query('select id,msg,datetime from (select id,msg,datetime from logs order by id  desc limit 100000 ) Logline where msg like "%Login incorrect%"');

        //debug($logs);
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
                    $users[$login]['last'] = $log['Logline']['datetime'];
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
        $this->set('failures', $usersnbfailures);
        $this->set('users', $users);
        $this->set('logins', $logins);
        $this->set('usernames', $usernames);
    }

    public function get_failures_by_nas() {
        $logs = $this->Logline->query('select id,msg,datetime from (select id,msg,datetime from logs order by id  desc limit 100000 ) Logline where msg like "%Login incorrect%"');
        
        //debug($logs);
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
    


}
