<?php
class SystemDetail extends AppModel {
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
    
    /* Test users */
    function tests_users($username, $nasname, $nassecret, &$results, $comment = "") {        
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
            if ($radcheck['radcheck']['attribute'] == "Cleartext-Password") {
                $password = $radcheck['radcheck']['value'];
            }
            if ($radcheck['radcheck']['attribute'] == "NAS-Port-Type") {
                $nasporttype = $radcheck['radcheck']['value'];
            }
        }
        if ($tls == 0 && $ttls == 0) {
            $nasports = explode("|", $nasporttype);
            if (count($nasports) > 0) {
                $nasporttype = $nasports[0];
                $request = '( echo "User-Name = \"' . $username . '\""; echo "Cleartext-Password = \"' . $password . '\"";  echo "NAS-Port-Type= \"' . $nasporttype . '\""; echo "EAP-Code = Response";   echo "EAP-Id = 210";   echo "EAP-Type-Identity = \"' . $username . '\"";   echo "Message-Authenticator = 0x00"; ) | radeapclient -x ' . $nasname . ' auth ' . $nassecret;
            } else {
                $request = '( echo "User-Name = \"' . $username . '\""; echo "Cleartext-Password = \"' . $password . '\"";  echo "EAP-Code = Response";   echo "EAP-Id = 210";   echo "EAP-Type-Identity = \"' . $username . '\"";   echo "Message-Authenticator = 0x00"; ) | radeapclient -x ' . $nasname . ' auth ' . $nassecret;
            }
            $return = shell_exec($request);
        } elseif ($ttls == 1) {
            $file = new File(APP . 'tmp/eap-ttls.conf', true, 0644);
            $file->write("network={\n");
            $file->write("\teap=TTLS\n");
            $file->write("\teapol_flags=0\n");
            $file->write("\tkey_mgmt=IEEE8021X\n");
            $file->write("\tidentity=\"" . $username . "\"\n");
            $file->write("\tpassword=\"" . $password . "\"\n");
            $file->write("\tphase2=\"auth=MSCHAPv2\"\n");
            $file->write("\tca_cert=\"" . Utils::getServerCertPath() . "\"\n");
            $file->write("}");
            $request = "/home/snack/interface/tools/" . $this->eapol . " -c /home/snack/interface/app/tmp/eap-ttls.conf -a".$nasname." -p1812 -s".$nassecret;
            $return = shell_exec($request);
        } elseif ($tls == 1) {
            $file = new File(APP . 'tmp/eap-tls.conf', true, 0644);
            $file->write("network={\n");
            $file->write("\teap=TLS\n");
            $file->write("\teapol_flags=0\n");
            $file->write("\tkey_mgmt=IEEE8021X\n");
            $file->write("\tidentity=\"" . $username . "\"\n");
            $file->write("\tca_cert=\"" . Utils::getServerCertPath() . "\"\n");
            $file->write("\tclient_cert=\"" . Utils::getUserCertsPemPath($username) . "\"\n");
            $file->write("\tprivate_key=\"" . Utils::getUserKeyPemPath($username) . "\"\n");
            $file->write("}");
            $request = "/home/snack/interface/tools/" . $this->eapol . " -c /home/snack/interface/app/tmp/eap-tls.conf -a".$nasname." -p1812 -s".$nassecret;
            $return = shell_exec($request);
        } else {
            $results[$username]['res'] = "NA";
            $return = "";
        }
        $results[$username]['res'] = $return;
        $results[$username]['comment'] = $comment;
    }
    
    /* Test AD users */
    function tests_usersAD($username, $password, $nasname, $nassecret, &$results, $comment = "") {        
        $return = shell_exec("getconf LONG_BIT");
        if ($return == "64\n") {
            $this->eapol = "eapol_test_64";
        } elseif ($return == "32\n") {
            $this->eapol = "eapol_test_x86";
        }
        $file = new File(APP . 'tmp/eap-ad.conf', true, 0644);
        $file->write("network={\n");
        $file->write("\teap=TTLS\n");
        $file->write("\teapol_flags=0\n");
        $file->write("\tkey_mgmt=IEEE8021X\n");
        $file->write("\tidentity=\"" . $username . "\"\n");
        $file->write("\tpassword=\"" . $password . "\"\n");
        $file->write("\tphase2=\"auth=MSCHAPv2\"\n");
        $file->write("\tca_cert=\"" . Utils::getServerCertPath() . "\"\n");
        $file->write("}");
        $request = "/home/snack/interface/tools/" . $this->eapol . " -c /home/snack/interface/app/tmp/eap-ad.conf -a".$nasname." -p1812 -s".$nassecret;
        $return = shell_exec($request);
        $results[$username]['res'] = $return;
        $results[$username]['comment'] = $comment;
    }
        
    public function checkProblem(&$results, $file="notifications.txt") {
        $file = new File(APP . 'tmp/'.$file, false, 0644);
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

?>
