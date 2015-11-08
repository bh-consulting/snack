<?php
App::uses('Nas', 'Model');
App::uses('Backup', 'Model');
App::uses('Logline', 'Model');

class SystemDetail extends AppModel {
    public $useTable = false;
	/* Gets the uptime of a service or -1 if the service is down. */
	function checkService( $service ) {
		$result = Utils::shell( "ps -e -o comm,etime | grep " . $service . " | tail -1");

        if (!empty($result['msg'][0])
            && preg_match(
                "#.*?(?:(?:([0-9]{2})-)?(?:([0-9]{2}):))?([0-9]{2}):([0-9]{2})#",
                $result['msg'][0],
                $m
            )
        ) {
            return Utils::formatTime($m[1], $m[2], $m[3], $m[4]);
		}

		return -1;
	}

	/* Gets the current date. */
	function getCurDate() {
		$format_en = "F j, Y, g:i a e P";
		$format_fr = "l jS F Y, H:i:s e P";
		return date($format_fr);
	}

	/* Gets the system hostname. */
	public function getHostname() {
		$readfile = Utils::readFile( "/proc/sys/kernel/hostname" );

		return $readfile[0]; /* Hostname */
	}

	/* Gets the system uptime and idletime. */
	public function getUptimes() {
		$content	= Utils::readFile( "/proc/uptime" );
		$result		= explode( " ", $content[0] );
		$result[0]	= Utils::secondToTime( $result[0] ); /* Up Time */
		$result[1]	= Utils::secondToTime( $result[1] ); /* Idle Time */

		return $result;
	}

	/* Gets System Load Average. */
	function getSystemLoad() {
		$content	= Utils::readFile( "/proc/loadavg" );
		$tmp		= explode(" ", $content[0]);
		$result[0]	= $tmp[0] . " " . $tmp[1] . " " . $tmp[2]; /* Load average */
		$tmp		= Utils::shell("top -b -n1 | grep \"Tasks:\"");
		$result[1]	= substr( $tmp['msg'][0], strpos($tmp['msg'][0], ":") + 2 ); /* Tasks */
	
		return $result;
	}

	/* Gets Memory Total&Free */
	function getMemory() {
		$content 	= Utils::readFile( "/proc/meminfo" );
		$result[0]	= substr( $content[1], strpos($content[1], ":") + 2 );	/* Total memory */
		$result[1]	= substr( $content[0], strpos($content[0], ":") + 2 );	/* Free memory */
		$result[2]	= ($result[1] - $result[0]) . " kB";			/* Used disk */
	
		return $result;
	}

	/* Gets Disk Space */
	function getDiskSpace() {
		$result[0] = disk_free_space("/") . " kB"; 	/* Free space */
		$result[1] = disk_total_space("/") . " kB"; 	/* Total space */
		$result[2] = ($result[1] - $result[0]) . " kB";	/* Used space */

		return $result;
	}

	/* Gets all network devices statistics */
	function getInterfacesStats() {
		$content = array_slice( Utils::readFile( "/proc/net/dev" ), 2);

		foreach( $content as $key => &$value){
			$value		= preg_split( "/\s+/", $value );
			$value[0]	= substr( $value[0], 0, strpos( $value[0], ":") );
		}

		return $content;
	}

	/* Gets all network devices details */
	function getInterfaces() {
		$content = Utils::shell( "/sbin/ip a" );
		$n = 0;
		$nV4 = 0;
		$nV6 = 0;

		foreach( $content['msg'] as $line){
			if( preg_match("#^([0-9]+): (.*)?:#i", $line, $match)) {
				$n = $match[1];
				$nV4 = 0;
				$nV6 = 0;
				$result[$n]['name'] = $match[2];
			}elseif( preg_match("#link\/(ether|loopback) ([0-9a-f:]+)#i", $line, $match)) {
				$result[$n]['mac'] = $match[2];
			}elseif( preg_match("#inet ([0-9\.\/]+)#i", $line, $match)) {
				$result[$n]['ipv4'][$nV4] = $match[1];
				++$nV4;
			}elseif( preg_match("#inet6 ([0-9a-f\:\/]+)#i", $line, $match)) {
				$result[$n]['ipv6'][$nV6] = $match[1];
				++$nV6;
			}
		}

		return $result;
	}
    
    /* Get system/processor load in percent */
    function getsystemLoadInPercent($coreCount = 2, $interval = 1) {
        $rs = sys_getloadavg();
        $interval = $interval >= 1 && 3 <= $interval ? $interval : 1;
        $load = $rs[$interval];
        return round(($load * 100) / $coreCount, 2);
    }
    
    /* Get release  */
    function getRelease() {
        $file = new File('/etc/debian_version', false);
        if ($file->exists()) {
            return "debian";
        }
        $file = new File('/etc/lsb-release', false);
        if ($file->exists()) {
            return "ubuntu";
        }
    }
    
    /* Get version  */
    function getVersion($release) {
        if ($release == "ubuntu") {
            $file = new File('/etc/lsb-release', false);
            $tmp=$file->read(false, 'rb', false);
            if(preg_match('/DISTRIB_RELEASE=(.*)\s/', $tmp, $matches)) {
                return $matches[1];
            }
        }
        if ($release == "debian") {
            $file = new File('/etc/debian_version', false);
            $tmp=$file->read(false, 'rb', false);
            if(preg_match('/(.*)/', $tmp, $matches)) {
                return $matches[1];
            }
        }
    }
    
    /* Get Version of SNACK */
    function getVersionSnack() {
        $file = new File(APP . 'VERSION.txt', false);
        $tmp = "";
        if ($file->exists()) {
            $tmp = $file->read(false, 'rb', false);
            return $tmp;
        }        
    }

    /* Get name */
    function getName() {
        $values = preg_grep("/Issuer: C=FR, ST=France, O=B.H. Consulting, CN=/", file(Utils::getServerCertPath()));
        foreach ( $values as $val ) {
            if( preg_match('/\Issuer:.*CN=(.*)/', $val, $matches)) {
                continue;
            }
        }
        return $matches[1];
    }
    
    /* Get CA validity */
    function getCAValidity() {
        $values = preg_grep("/.*Not After : (.*)/", file(Utils::getServerCertPath()));
        foreach ( $values as $val ) {
            if( preg_match('/\Not After : (.*)/', $val, $matches)) {
                continue;
            }
        }
        return $matches[1];
    }
    
    /* Check auth user */
    function check_auth_user($username, $password, $authtype, $nasname, $nassecret) {        
        $return = shell_exec("getconf LONG_BIT");
        if ($return == "64\n") {
            $this->eapol = "eapol_test_64";
        } elseif ($return == "32\n") {
            $this->eapol = "eapol_test_x86";
        }
        $this->Radcheck = ClassRegistry::init('Radcheck');
        $radchecks = $this->Radcheck->query('select * from radcheck where username="' . $username . '";');
        $tls = 0;
        $ttls = 0;
        $nasporttype = "";
        $return = "";
        foreach ($radchecks as $radcheck) {
            if ($radcheck['radcheck']['attribute'] == "EAP-Type") {
                if ($radcheck['radcheck']['value'] == "EAP-TTLS") {
                    $ttls = 1;
                }
                if ($radcheck['radcheck']['value'] == "EAP-TLS") {
                    $tls = 1;
                }
            }
            if ($radcheck['radcheck']['attribute'] == "NAS-Port-Type") {
                $nasporttype = $radcheck['radcheck']['value'];
            }
        }
        if ($authtype == "EAP-MD5") {
            $nasports = explode("|", $nasporttype);
            if (count($nasports) > 0) {
                $nasporttype = $nasports[0];
                $request = '( echo "User-Name = \"' . $username . '\""; echo "Cleartext-Password = \"' . $password . '\"";  echo "NAS-Port-Type= \"' . $nasporttype . '\""; echo "EAP-Code = Response";   echo "EAP-Id = 210";   echo "EAP-Type-Identity = \"' . $username . '\"";   echo "Message-Authenticator = 0x00"; ) | radeapclient -x ' . $nasname . ' auth ' . $nassecret;
            } else {
                $request = '( echo "User-Name = \"' . $username . '\""; echo "Cleartext-Password = \"' . $password . '\"";  echo "EAP-Code = Response";   echo "EAP-Id = 210";   echo "EAP-Type-Identity = \"' . $username . '\"";   echo "Message-Authenticator = 0x00"; ) | radeapclient -x ' . $nasname . ' auth ' . $nassecret;
            }
            //debug($request);
            $return = shell_exec($request);
        } elseif ($authtype == "EAP-TTLS-PAP") {
            $file = new File(APP . 'tmp/eap.conf', true, 0644);
            $file->write("network={\n");
            $file->write("\teap=TTLS\n");
            $file->write("\teapol_flags=0\n");
            $file->write("\tkey_mgmt=IEEE8021X\n");
            $file->write("\tidentity=\"" . $username . "\"\n");
            $file->write("\tpassword=\"" . $password . "\"\n");
            $file->write("\tphase2=\"auth=PAP\"\n");
            $file->write("\tca_cert=\"" . Utils::getServerCertPath() . "\"\n");
            $file->write("}");
            $request = "/home/snack/interface/tools/" . $this->eapol . " -c /home/snack/interface/app/tmp/eap.conf -a".$nasname." -p1812 -s".$nassecret;
            $return = shell_exec($request);
        } elseif ($authtype == "EAP-TTLS-MSCHAPV2") {
            $file = new File(APP . 'tmp/eap.conf', true, 0644);
            $file->write("network={\n");
            $file->write("\teap=TTLS\n");
            $file->write("\teapol_flags=0\n");
            $file->write("\tkey_mgmt=IEEE8021X\n");
            $file->write("\tidentity=\"" . $username . "\"\n");
            $file->write("\tpassword=\"" . $password . "\"\n");
            $file->write("\tphase2=\"auth=MSCHAPv2\"\n");
            $file->write("\tca_cert=\"" . Utils::getServerCertPath() . "\"\n");
            $file->write("}");
            $request = "/home/snack/interface/tools/" . $this->eapol . " -c /home/snack/interface/app/tmp/eap.conf -a".$nasname." -p1812 -s".$nassecret;
            $return = shell_exec($request);
        } elseif ($authtype == "EAP-TLS") {
            $file = new File(APP . 'tmp/eap.conf', true, 0644);
            $file->write("network={\n");
            $file->write("\teap=TLS\n");
            $file->write("\teapol_flags=0\n");
            $file->write("\tkey_mgmt=IEEE8021X\n");
            $file->write("\tidentity=\"" . $username . "\"\n");
            $file->write("\tca_cert=\"" . Utils::getServerCertPath() . "\"\n");
            $file->write("\tclient_cert=\"" . Utils::getUserCertsPemPath($username) . "\"\n");
            $file->write("\tprivate_key=\"" . Utils::getUserKeyPemPath($username) . "\"\n");
            $file->write("}");
            $request = "/home/snack/interface/tools/" . $this->eapol . " -c /home/snack/interface/app/tmp/eap.conf -a".$nasname." -p1812 -s".$nassecret;
            $return = shell_exec($request);
        } elseif ($authtype == "EAP-PEAP-MSCHAPV2") {
            $file = new File(APP . 'tmp/eap.conf', true, 0644);
            $file->write("network={\n");
            $file->write("\teap=PEAP\n");
            $file->write("\teapol_flags=0\n");
            $file->write("\tkey_mgmt=IEEE8021X\n");
            $file->write("\tidentity=\"" . $username . "\"\n");
            $file->write("\tca_cert=\"" . Utils::getServerCertPath() . "\"\n");
            $file->write("\tpassword=\"" . $password . "\"\n");
            $file->write("\tphase2=\"auth=MSCHAPv2\"\n");
            $file->write("}");
            $request = "/home/snack/interface/tools/" . $this->eapol . " -c /home/snack/interface/app/tmp/eap.conf -a".$nasname." -p1812 -s".$nassecret;
            $return = shell_exec($request);
        } else {
            $results[$username]['res'] = "NA";
            $return = "";
        }
        return $return;
    }
        
    public function checkProblem(&$results, $file="notifications.txt") {
        $file = new File(APP . 'tmp/'.$file, false, 0644);
        if ($file->exists()) {
            $tmp = $file->read(false, 'rb', false);
            if ($tmp) {
                $lines = explode("\n", $tmp);
                $arr = array();
                foreach ($lines as $line) {
                    if (preg_match('/\[(.*)\]\s+\[(.*)\]\s+(.*)/', $line, $matches)) {
                        $arr['date'] = $matches[1];
                        $arr['type'] = $matches[2];
                        $arr['msg'] = $matches[3];
                        $results[] = $arr;
                    }
                }
            }
        }
    }

    public function getElasticClusterHealth() {
        $arr = array();
        $url = 'http://localhost:9200/_cluster/health?pretty';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);                                                                    
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        $data_decode = json_decode($data, true);
        //debug($data_decode);
        return $data_decode;
    }

    public function checkUpdates() {
        $return = shell_exec("sudo apt-get update > /dev/null 2> /dev/null && apt-cache show snack | grep Version | cut -d' ' -f2");
        $file = new File(APP.'tmp/updates', true, 0644);
        $file->write($return);
        $file->close();
    }

    public function createReports() {
        $this->errors=0;
        $values = preg_grep("/Issuer: C=FR, ST=France, O=B.H. Consulting, CN=/", file(Utils::getServerCertPath()));
        foreach ( $values as $val ) {
            if( preg_match('/\Issuer:.*CN=(.*)/', $val, $matches)) {
                continue;
            }
        }

        $this->domain = $matches[1];
        $str = "<h2>SNACK Report</h2>";
        $str .= "<h3>Infos</h3>";
        $str .= "Name : ".$this->getName()."<br>";
        $str .= "Release : ".$this->getRelease()."<br>";
        $str .= "Version : ".$this->getVersion($this->getRelease())."<br>";
        $str .= "Version of Snack : ".$this->getVersionSnack()."<br>";
        $str .= "IP Address : ".Configure::read('Parameters.ipAddress')."<br>";
        $str .= "CA Validity : ".$this->getCAValidity()."<br>";

        /* check Services */
        $str .= "<h3>Services</h3>";
        $mysqlUptime = $this->checkService("mysqld");
        if ($mysqlUptime == -1) {
            $this->errors++;
            $str .= '<span style="color:#FF0000">';
            $str .=  "ERROR : Mysql not running<br>";
            $str .= '</span>';
        }
        else {
            $str .= "Mysql enabled for ".$mysqlUptime."<br>";
        }
        $radiusUptime = $this->checkService("freeradius");
        if ($radiusUptime == -1) {
            $this->errors++;
            $str .= '<span style="color:#FF0000">';
            $str .=  "ERROR : Freeradius not running<br>";
            $str .= '</span>';
        }
        else {
            $str .= "Freeradius enabled for ".$radiusUptime."<br>";
        }

        /* check Backups */
        $nasModel = new Nas();
        $backupModel = new Backup();
        $str .= "<h3>Backups NAS</h3>";
        $nas = $nasModel->query('select nasname,secret,backup from nas;');
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
                if ($n['nas']['backup']) {
                    $backup = $backupModel->query("select * from backups where nas='".$nasname."' order by id desc limit 1;");
                    $str .= $nasname." ";
                    if (count($backup) > 0) {
                        $datetime2 = new DateTime($backup[0]['backups']['datetime']);
                        $diff=$datetime2->diff($datetime1);
                        $years=$diff->format('%y');
                        $months=$diff->format('%m');
                        $days=$diff->format('%d');
                        if ($years > 0 or $months > 0 or $days > 3) {
                            $str .= '<span style="color:#FF0000">';
                            $str .= "WARNING : No backup  since ".$backup[0]['backups']['datetime']."<br>";
                            $str .= '</span>';
                            $this->errors++;
                        }
                        else {
                            $str .= 'Last backup : <span style="color:#00FF00">OK</span> : '.$backup[0]['backups']['datetime'].'<br>';
                        }
                    }
                    else {
                        $str .= '<span style="color:#FF0000">';
                        $str .= "No backup <br>";
                        $str .= '</span>';
                        $this->errors++;
                    }
                } else {
                    $str .= $nasname." ";
                    $str .= '<span style="color:#FFA500">';
                    $str .= 'No Monitor<br>';
                    $str .= '</span>';
                }
            }
        }

        /* check HA */
        /* add no slave */
        $str .= "<h3>High Availability</h3>";
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
                $str .= "<h4>Slave IP Address : ".$slave."</h4>";
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
                    $ip = "";
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
                                $str .= '<span style="color:#FF0000">';
                                $str .= "ERROR : No replication since ".$datetime2->format('Y-m-d H:i:s')."<br>";
                                $str .= '</span>';
                            }
                            else {
                                $str .= "Last replication OK : ".$datetime2->format('Y-m-d H:i:s')."<br>";
                            }
                            break;
                        } else {
                            $str .= '<span style="color:#FF0000">';
                            $str .= "WARNING : Replication failed :  ".$datetime2->format('Y-m-d H:i:s')."<br>";
                            $str .= '</span>';
                        }
                    }
                }
            }
            if ($found == false) {
                if ($slave != "") {
                    $str .= '<span style="color:#FF0000">';
                    $str .= "ERR : No replication for " . $slave . "<br>";
                    $str .= '</span>';
                }
            }
        }

        /* Get Failures */
        $loglineModel = new Logline();
        $res = $loglineModel->get_failures();
        $usersnbfailures = $res['usersnbfailures'];
        $users = $res['users'];
        $usernames = $res['usernames'];
        $logins =$res['logins'];
        $nb = count($usersnbfailures);
        $str .= "<h3>$nb failures of connections order by users<br></h3>";
        $str .=  "<table border='1'>";
        $str .=  "<th>" . __('User') . "</th>";
        $str .=  "<th>" . __('Nb') . "</th>";
        $str .=  "<th>" . __('Last') . "</th>";
        $str .=  "<th>" . __('Vendor') . "</th>";
        $str .=  "<th>" . __('NAS') . "</th>";
        $str .=  "<th>" . __('Port') . "</th>";
        $str .=  "<th>" . __('Why ?') . "</th>";
        //debug($users);
        /*$infos = explode(",", $this->element('formatUsersList', array(
                    'users' => $usernames
        )));*/
        $i = 0;
        
        foreach ($usersnbfailures as $key => $value) {
            $str .=  "<tr>";
            $str .=  "<td>" . $usernames[$i]['username'] . "</td>";
            $str .=  "<td>" . $value . "</td>";
            $str .=  "<td>" . $users[$logins[$i]]['last'] . "</td>";
            $str .=  "<td>" . $users[$logins[$i]]['vendor'] . "</td>";
            $str .=  "<td>" . $users[$logins[$i]]['nas'] . "</td>";
            $str .=  "<td>" . $users[$logins[$i]]['port'] . "</td>";
            $str .=  "<td>" . $users[$logins[$i]]['info'] . "</td>";
            //echo " : ".$value." tentatives";
            $i++;
            $str .=  "</tr>";
        }
        $str .=  "</table>";


        if ($this->errors > 0) {
            $subject = "[".$this->domain."][ERR] SNACK - Reports";
        }
        else {
            $subject = "[".$this->domain."][INFO] SNACK - Reports";
        }
        //debug($str);
        $this->sendMail($subject , $str);
    }

    public function sendMail($subject, $body) {
        App::uses('CakeEmail', 'Network/Email');
        $Email = new CakeEmail();
        $Email->subject($subject);
        $Email->template('default');
        if (Configure::read('Parameters.smtp_ip') != '') {
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
        try {
            $Email->send($body);
        } catch ( Exception $e ) {
            // Failure, with exception
        }  
    }

}

?>
