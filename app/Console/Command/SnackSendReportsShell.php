<?php
App::uses('Utils', 'Lib');

class SnackSendReportsShell extends AppShell {
    public $name = 'SystemDetails';
    public $uses = array('SystemDetail', 'Raduser', 'Nas', 'Backup', 'Radacct', 'Logline');
    
    private $str="";
    private $strPB="";
    private $file;
    private $domain="";
    private $errors=0;
    
    public function main() {
        $values = preg_grep("/Issuer: C=FR, ST=France, O=B.H. Consulting, CN=/", file(Utils::getServerCertPath()));
        foreach ( $values as $val ) {
            if( preg_match('/\Issuer:.*CN=(.*)/', $val, $matches)) {
                continue;
            }
        }
        $this->domain = $matches[1];

        if (Configure::read('Parameters.role') == "master") {
            $this->file = new File(APP.'tmp/notifications.txt', true, 0644);
            $date = new DateTime('23:00');
            $this->str .= "<h2>SNACK Report</h2>";
            $this->getInfos();
            $this->checkServices();
            $this->checkBackup();
            $this->checkHA();

            /* check Problems*/
            $this->strPB .= "<h2>SNACK Problems</h2>";
            $this->checkProblem();

            //$this->testUsers();
            if ($date->format('H:i') == date('H:i')) {
                $this->cleanDBSessions();
                $this->get_failures_by_users();
                $this->get_Err_from_logs();
                if ($this->errors > 0) {
                    $subject = "[".$this->domain."][ERR] SNACK - Reports";
                }
                else {
                    $subject = "[".$this->domain."][INFO] SNACK - Reports";
                }

                $this->sendMail($subject, $this->str);
            }
            //echo $this->str;
            //echo $this->errors;
        }
    }
    
    public function getInfos() {
        $this->str .= "<h3>Infos</h3>";
        $this->str .= "Name : ".$this->SystemDetail->getName()."<br>";
        $this->str .= "Release : ".$this->SystemDetail->getRelease()."<br>";
        $this->str .= "Version : ".$this->SystemDetail->getVersion($this->SystemDetail->getRelease())."<br>";
        $this->str .= "Version of Snack : ".$this->SystemDetail->getVersionSnack()."<br>";
        $this->str .= "IP Address : ".Configure::read('Parameters.ipAddress')."<br>";
        $this->str .= "CA Validity : ".$this->SystemDetail->getCAValidity()."<br>";
    }
    
    public function checkServices() {
        $this->str .= "<h3>Services</h3>";
        $mysqlUptime = $this->SystemDetail->checkService("mysqld");
        if ($mysqlUptime == -1) {
            $this->errors++;
            $this->str .= '<span style="color:#FF0000">';
            $this->str .=  "ERROR : Mysql not running<br>";
            $this->str .= '</span>';
        }
        else {
            $this->str .= "Mysql enabled for ".$mysqlUptime."<br>";
        }
        $radiusUptime = $this->SystemDetail->checkService("freeradius");
        if ($radiusUptime == -1) {
            $this->errors++;
            $this->str .= '<span style="color:#FF0000">';
            $this->str .=  "ERROR : Freeradius not running<br>";
            $this->str .= '</span>';
        }
        else {
            $this->str .= "Freeradius enabled for ".$radiusUptime."<br>";
        }
    }
    
    public function testUsers() {
        $this->str .= "<h3>Tests Users</h3>";
        $results=array();
        $nas = $this->Nas->query('select nasname,secret from nas where nasname="127.0.0.1";');
        foreach ($nas as $n) {
            $nasname=$n['nas']['nasname'];
            $secret=$n['nas']['secret'];
        }
        $usernames = $this->Raduser->query('select username,comment from raduser;');
        foreach ($usernames as $username) {
            $this->SystemDetail->tests_users($username['raduser']['username'], $nasname, $secret, $results, $username['raduser']['comment']);
        }
        if (!empty($results)) {
            foreach ($results as $key => $result) {
                if (preg_match('/Received Access-Reject packet/', $result['res'], $matches)) {
                    $this->str .= $key.'<span style="color:#FF0000">';
                    $this->str .= ' ERROR<br>';
                    $this->str .= '</span>';
                } elseif (preg_match('/Received Access-Accept packet/', $result['res'], $matches)) {
                    $this->str .= $key.'<span style="color:#00FF00">';
                    $this->str .= ' OK<br>';
                    $this->str .= '</span>';
                } elseif (preg_match('/SUCCESS/', $result['res'], $matches)) {
                    $this->str .= $key.'<span style="color:#00FF00">';
                    $this->str .= ' OK<br>';
                    $this->str .= '</span>';
                } elseif (preg_match('/FAILURE/', $result['res'], $matches)) {
                    $this->str .= $key.'<span style="color:#FF0000">';
                    $this->str .= ' ERROR<br>';
                    $this->str .= '</span>';
                }
            }
        }       
    }
    
    public function checkBackup() {
        $oid_writeNet="iso.3.6.1.4.1.9.2.1.55";
        $ip_address="10.254.20.192";
        $this->str .= "<h3>Backups NAS</h3>";
        $nas = $this->Nas->query('select nasname,secret from nas;');
        $datetime1 = new DateTime();
        //echo $datetime1->format('Y-m-d H:i:s');
        foreach ($nas as $n) {
            $nasname=$n['nas']['nasname'];
            /*$return = shell_exec("export NAS_IP_ADDRESS=".$nasname." ;
                                  export USER_NAME=AUTO ;
                                  export ACCT_STATUS_TYPE=Write ;
                                   /home/snack/scripts/backup_create.sh");*/
            //echo "RETURN : ".$return;
            if ($nasname != "127.0.0.1") {
                $backup = $this->Backup->query("select * from backups where nas='".$nasname."' order by id desc limit 1;");
                $this->str .= $nasname." ";
                if (count($backup) > 0) {
                    $datetime2 = new DateTime($backup[0]['backups']['datetime']);
                    $diff=$datetime2->diff($datetime1);
                    $years=$diff->format('%y');
                    $months=$diff->format('%m');
                    $days=$diff->format('%d');
                    if ($years > 0 or $months > 0) {
                        $this->str .= '<span style="color:#FF0000">';
                        $this->str .= "WARNING : No backup  since ".$backup[0]['backups']['datetime']."<br>";
                        $this->str .= '</span>';
                    }
                    else {
                        $this->str .= 'Last backup : <span style="color:#00FF00">OK</span> : '.$backup[0]['backups']['datetime'].'<br>';
                    }
                }
                else {
                    $this->str .= '<span style="color:#FF0000">';
                    $this->str .= "No backup <br>";
                    $this->str .= '</span>';
                }
            }
        }
         /*   export NAS_IP_ADDRESS; export USER_NAME=admin; export ACCT_STATUS_TYPE=Auto; ~snack/scripts/backup_create.sh*/

    }
    
    public function checkHA() {
        $this->str .= "<h3>High Availability</h3>";
        $dir = new Folder(APP.'tmp/ha');
        $datetime1 = new DateTime();
        $results = array();
        $files = $dir->find('ha-[0-9]{4}-[0-9]{2}-[0-9]{2}_[0-9]{2}-[0-9]{2}\.log');
        sort($files);
        $files=array_reverse($files);
        $found = false;
        $slaves=explode(';', Configure::read('Parameters.slave_ip_to_monitor'));
        foreach($slaves as $slave) {
            if ($slave != "") {
                $this->str .= "<h4>Slave IP Address : ".$slave."</h4>";
                foreach ($files as $file) {
                    $rsync = -1;
                    $mysql = -1;
                    if (preg_match('/ha-([0-9]{4}-[0-9]{2}-[0-9]{2})_([0-9]{2})-([0-9]{2})\.log/', $file, $matches)) {
                        $datetime2 = new DateTime($matches[1]." ".$matches[2].":".$matches[3]);
                    }
                    $fileha = new File(APP.'tmp/ha/'.$file, false, 0644);
                    $tmp=$fileha->read(false, 'rb', false);
                    $res_versions = 0;
                    if (preg_match('/VERSIONS MISMATCH/', $tmp, $matches)) {
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
                    if ($ip == $slave) {
                        $found = true;
                        if ($rsync == 0 && $mysql == 0 && $res_versions == 0 ) {
                            $diff = $datetime2->diff($datetime1);
                            $years = $diff->format('%y');
                            $months = $diff->format('%m');
                            $days = $diff->format('%d');
                            if ($years > 0 or $months > 0 or $days > 1) {
                                $this->errors++;
                                $this->str .= '<span style="color:#FF0000">';
                                $this->str .= "ERROR : No replication since ".$datetime2->format('Y-m-d H:i:s')."<br>";
                                $this->str .= '</span>';
                                $this->file->append("[] [ERR] SNACK No replication since ".$datetime2->format('Y-m-d H:i:s'));
                            }
                            else {
                                $this->str .= "Last replication OK : ".$datetime2->format('Y-m-d H:i:s')."<br>";
                            }
                            break;
                        } else {
                            $this->str .= '<span style="color:#FF0000">';
                            $this->str .= "WARNING : Replication failed :  ".$datetime2->format('Y-m-d H:i:s')."<br>";
                            $this->str .= '</span>';
                            $this->file->append("[".$datetime2->format('Y-m-d H:i:s')."] [WARN] SNACK Last replication failed ");
                        }
                    }
                }
            }
            if ($found == false) {
                if ($slave != "") {
                    $this->str .= '<span style="color:#FF0000">';
                    $this->str .= "ERR : No replication for " . $slave . "<br>";
                    $this->str .= '</span>';
                    $this->file->append("[] [ERR] SNACK No replication for ".$slave);
                }
            }
        }
    }
    
    public function get_Err_from_logs() {
        
    }
    
    public function get_failures_by_users() {
        $res = $this->Logline->get_failures();
        $usersnbfailures = $res['usersnbfailures'];
        $users = $res['users'];
        $usernames = $res['usernames'];
        $logins =$res['logins'];
        $nb = count($usersnbfailures);
        $this->str .= "<h3>$nb failures of connections order by users<br></h3>";
        $this->str .=  "<table>";
        $this->str .=  "<th>" . __('User') . "</th>";
        $this->str .=  "<th>" . __('Nb') . "</th>";
        $this->str .=  "<th>" . __('Last') . "</th>";
        $this->str .=  "<th>" . __('Vendor') . "</th>";
        $this->str .=  "<th>" . __('NAS') . "</th>";
        $this->str .=  "<th>" . __('Port') . "</th>";
        $this->str .=  "<th>" . __('Why ?') . "</th>";
        //debug($users);
        /*$infos = explode(",", $this->element('formatUsersList', array(
                    'users' => $usernames
        )));*/
        $i = 0;
        
        foreach ($usersnbfailures as $key => $value) {
            $this->str .=  "<tr>";
            $this->str .=  "<td>" . $usernames[$i]['username'] . "</td>";
            $this->str .=  "<td>" . $value . "</td>";
            $this->str .=  "<td>" . $users[$logins[$i]]['last'] . "</td>";
            $this->str .=  "<td>" . $users[$logins[$i]]['vendor'] . "</td>";
            $this->str .=  "<td>" . $users[$logins[$i]]['nas'] . "</td>";
            $this->str .=  "<td>" . $users[$logins[$i]]['port'] . "</td>";
            $this->str .=  "<td>" . $users[$logins[$i]]['info'] . "</td>";
            //echo " : ".$value." tentatives";
            $i++;
            $this->str .=  "</tr>";
        }
        $this->str .=  "</table>";
    }

    public function cleanDBSessions() {
        $this->Radacct->query('delete from radacct where acctauthentic!="RADIUS"');
    }

    public function checkProblem() {
        $results=array();
        $resultsprec=array();
        $this->SystemDetail->checkProblem($resultsprec,"notifications-prec.txt");
        $this->SystemDetail->checkProblem($results);
        $this->errors = $this->errors + count($results);
        $olderrfound=array();
        $newerrfound=array();
        $fixfound=array();
        $found=false;
        foreach ($results as $res) {
            foreach ($resultsprec as $resprec) {
                if ($resprec['msg']==$res['msg'] && $resprec['type'] == $res['type']) {
                    $found=true;
                    $olderrfound[] = $resprec['date']." ".$resprec['type']." ".$resprec['msg'];
                }
            }
            if ($found==false) {
                $newerrfound[] = $res['date']." ".$res['type']." ".$res['msg'];
            }
        }
        $found=false;
        foreach ($resultsprec as $resprec) {
            foreach ($results as $res) {
                if ($resprec['msg']==$res['msg'] && $resprec['type'] == $res['type']) {
                    $found=true;
                }
            }
            if ($found==false) {
                $fixfound[] = $resprec['date']." ".$resprec['type']." ".$resprec['msg'];
            }
        }

        if (count($newerrfound)>0 || count($fixfound)>0) {
            if (count($fixfound)>0) {
                $subject = "[".$this->domain."][FIX] SNACK - Infos";
                $this->strPB .= "<h3>Fix Errors</h3>\n";
                foreach ($fixfound as $fix) {
                    $this->strPB .= $fix."\n";
                }
            }
            echo "\n";
            if (count($newerrfound)>0) {
                $subject = "[".$this->domain."][ERR] SNACK - Infos";
                $this->strPB .= "<h3>New Errors</h3>\n";
                foreach ($newerrfound as $err) {
                    $this->strPB .= $err."\n";
                }
            }
            echo "\n";
            if (count($olderrfound)>0) {
                $this->strPB .= "<h3>Old Errors</h3>\n";
                foreach ($olderrfound as $err) {
                    $this->strPB .= $err."\n";
                }
            }
            $this->sendMail($subject, $this->strPB);
        }
    }

    public function sendMail($subject, $body) {
        App::uses('CakeEmail', 'Network/Email');
        $Email = new CakeEmail();
        $Email->subject($subject);
        $Email->template('default');
        if (Configure::read('Parameters.smtp_login') != '') {
            $Email->config(array('transport' => 'Smtp',
                                 'port' => Configure::read('Parameters.smtp_port'),
                                 'host' => Configure::read('Parameters.smtp_ip'),
                                 'username' => Configure::read('Parameters.smtp_login'),
                                 'password' => Configure::read('Parameters.smtp_password'),
                                 'client' => 'snack'.$this->domain));
        } else {
            $Email->config(array('transport' => 'Smtp',
                                 'port' => Configure::read('Parameters.smtp_port'),
                                 'host' => Configure::read('Parameters.smtp_ip'),
                                 'client' => 'snack'.$this->domain));
        }
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
        //$Email->to('groche@guigeek.org');
        

        $Email->send($body);
    }

}
?>
