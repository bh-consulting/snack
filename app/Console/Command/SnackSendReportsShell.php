<?php
App::uses('Utils', 'Lib');

class SnackSendReportsShell extends AppShell {
    public $name = 'SystemDetails';
    public $uses = array('SystemDetail', 'Raduser', 'Nas', 'Backup', 'Radacct');
    
    private $str="";
    private $errors=0;
    
    public function main() {
        if (Configure::read('Parameters.role') == "master") {
            $this->str .= "<h2>SNACK Report</h2>";
            $this->getInfos();
            $this->checkServices();
            $this->checkBackup();
            $this->checkHA();
            //$this->testUsers();
            $this->cleanDBSessions();
            $this->sendMail($this->str);
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
        $slaves=explode(';', Configure::read('Parameters.slave_ip_to_monitor'));
        foreach($slaves as $slave) {
            $this->str .= "<h4>Slave IP Address : ".$slave."</h4>";
            foreach ($files as $file) {
                $rsync = -1;
                $mysql = -1;
                if (preg_match('/ha-([0-9]{4}-[0-9]{2}-[0-9]{2})_([0-9]{2})-([0-9]{2})\.log/', $file, $matches)) {
                    $datetime2 = new DateTime($matches[1]." ".$matches[2].":".$matches[3]);
                }
                $fileha = new File(APP.'tmp/ha/'.$file, false, 0644);
                $tmp=$fileha->read(false, 'rb', false);
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
                    if ($rsync == 0 && $mysql == 0) {
                        $diff = $datetime2->diff($datetime1);
                        $years = $diff->format('%y');
                        $months = $diff->format('%m');
                        $days = $diff->format('%d');
                        if ($years > 0 or $months > 0 or $days > 0) {
                            $this->errors++;
                            $this->str .= '<span style="color:#FF0000">';
                            $this->str .= "ERROR : No replication since ".$datetime2->format('Y-m-d H:i:s')."<br>";
                            $this->str .= '</span>';
                        }
                        else {
                            $this->str .= "Last replication OK : ".$datetime2->format('Y-m-d H:i:s')."<br>";
                        }
                        break;
                    } else {
                        $this->str .= '<span style="color:#FF0000">';
                        $this->str .= "WARNING : Replication failed :  ".$datetime2->format('Y-m-d H:i:s')."<br>";
                        $this->str .= '</span>';
                    }
                }
            }
        }
    }
    
    public function cleanDBSessions() {
        $this->Radacct->query('delete from radacct where acctauthentic!="RADIUS"');
    }
    
    public function sendMail($body) {
        App::uses('CakeEmail', 'Network/Email');
        $values = preg_grep("/Issuer: C=FR, ST=France, O=B.H. Consulting, CN=/", file(Utils::getServerCertPath()));
        foreach ( $values as $val ) {
            if( preg_match('/\Issuer:.*CN=(.*)/', $val, $matches)) {
                continue;
            }
        }
        $domain = $matches[1];
        $Email = new CakeEmail();
        if (Configure::read('Parameters.smtp_login') != '') {
            $Email->config(array('transport' => 'Smtp',
                                 'port' => Configure::read('Parameters.smtp_port'),
                                 'host' => Configure::read('Parameters.smtp_ip'),
                                 'username' => Configure::read('Parameters.smtp_login'),
                                 'password' => Configure::read('Parameters.smtp_password'),
                                 'client' => 'snack'.$domain));
        } else {
            $Email->config(array('transport' => 'Smtp',
                                 'port' => Configure::read('Parameters.smtp_port'),
                                 'host' => Configure::read('Parameters.smtp_ip'),
                                 'client' => 'snack'.$domain));
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
        
        if ($this->errors > 0) {
            $subject = "[".$domain."][WARN] SNACK - Reports";
        }
        else {
            $subject = "[".$domain."][INFO] SNACK - Reports";
        }
        $Email->subject($subject);
        /*$return = shell_exec("sudo /home/snack/interface/tools/scriptSnackExport.sh");
        $infos = explode("\n", $return);
        $name = $infos[0];
        $Email->attachments(array(
            $name => array(
                'file' => 'conf/' . $name,
                'mimetype' => 'application/gzip',
                'contentId' => '123456789'
            )
        ));*/
        $Email->send($body);
        /*$this->redirect(
            array('action' => 'index')
        );*/
    }

}
?>
