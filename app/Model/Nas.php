<?php

App::uses('Utils', 'Lib');
App::import('model','Backup');

class Nas extends AppModel {
	public $useTable = 'nas';
	public $primaryKey = 'id';
	public $displayField = 'nasname';
	public $name = 'Nas';
    public $actsAs = array('Validation');
	public $validationDomain = 'validation';

	public $validate = array(
		'nasname' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have to type the NAS IP.',
				'allowEmpty' => false,
                'required' => true,
			),
		'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'IP already in the database.',
			),
		'ipFormat' => array(
				'rule' => 'isIPFormat',
				'message' => 'This is not an IP address format.',
			),
		),
        'shortname' => array(
            'notEmpty' => array(
                'rule' => 'notempty',
                'message' => 'You have to type the NAS short name.',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
		'secret' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You have to type the NAS secret.',
				'allowEmpty' => false,
                'required' => true,
			),
		),
        'backup' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'You have to enter if the nas must be backuped or not',
                'required' => true,
            ),
        ),
        'login' => array(
		),
        'password' => array(
        ),
        'confirm_password' => array(
            'rule' => array('equalValues', 'password'),
            'allowEmpty' => true,
            'message' =>
                'Please re-enter your password twice so that the values match'
        ),
        'enablepassword' => array(
        ),
        'confirm_enablepassword' => array(
            'rule' => array('equalValues', 'enablepassword'),
            'allowEmpty' => true,
            'message' =>
                'Please re-enter your password twice so that the values match'
        ),
	);

	public function isIPFormat($field=array()) {
		$value = array_shift($field);
		if(Utils::isIP($value)) { 
			return true; 
		}
		return false; 
	}

    public function beforeSave($options = array()) {
        $key = Configure::read('Security.snackkey');
        //debug(strlen($this->data['Nas']['password']));
        if (isset($this->data['Nas']['password']) && strlen($this->data['Nas']['password'])!=128) {
            $secret = Security::encrypt($this->data['Nas']['password'], $key);
            $secret64Enc = base64_encode($secret);
            $this->data['Nas']['password'] =  $secret64Enc;
        }
        if (isset($this->data['Nas']['enablepassword']) && strlen($this->data['Nas']['enablepassword'])!=128) {  
            $secret = Security::encrypt($this->data['Nas']['enablepassword'], $key);
            $secret64Enc = base64_encode($secret);
            $this->data['Nas']['enablepassword'] =  $secret64Enc;
        }
        if (isset($this->data['Nas']['confirm_password'])) {  
            unset($this->data['Nas']['confirm_password']);  
        }  
        return true;  
    }

    public function backupOneNas($NAS_IP_ADDRESS, $ACCT_STATUS_TYPE, $USER_NAME) {
        $key = Configure::read('Security.snackkey');
        $nas = $this->find('first', array(
            'conditions' => array('Nas.nasname' => $NAS_IP_ADDRESS)));
        if (($nas['Nas']['login'] != "") && ($nas['Nas']['password'] != "")) {
            $secret64Enc = $nas['Nas']['password'];
            $secret64Dec = base64_decode($secret64Enc);
            $password = Security::decrypt($secret64Dec,$key);

            if ($nas['Nas']['enablepassword'] != "") {
                $secret64Enc = $nas['Nas']['enablepassword'];
                $secret64Dec = base64_decode($secret64Enc);
                $enablepassword = Security::decrypt($secret64Dec,$key);
            }
            if (($nas['Nas']['enablepassword'] == "") || ($enablepassword == "")) {
                $enablepassword = $password;
            }
            if ($password != "") {
                if ($this->backupNas($nas['Nas']['nasname'], $nas['Nas']['login'], $password, $enablepassword, $ACCT_STATUS_TYPE, $USER_NAME)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function backupAllNas($ACCT_STATUS_TYPE, $USER_NAME) {
        $key = Configure::read('Security.snackkey');
        $nas = $this->find('all');
        foreach ($nas as $n) {
            if ($n['Nas']['nasname'] != "127.0.0.1") {
                if (($n['Nas']['login'] != "") && ($n['Nas']['password'] != "")) {
                    $secret64Enc = $n['Nas']['password'];
                    $secret64Dec = base64_decode($secret64Enc);
                    $password = Security::decrypt($secret64Dec,$key);

                    if ($n['Nas']['enablepassword'] != "") {
                        $secret64Enc = $n['Nas']['enablepassword'];
                        $secret64Dec = base64_decode($secret64Enc);
                        $enablepassword = Security::decrypt($secret64Dec,$key);
                    }
                    if (($n['Nas']['enablepassword'] == "") || ($enablepassword == "")) {
                        $enablepassword = $password;
                    }
                    if ($password != "") {
                        $this->backupNas($n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword, $ACCT_STATUS_TYPE, $USER_NAME);
                    }
                }
            }
        }
        return true;
    }

    public function backupNas($host, $login, $password, $enablepassword, $ACCT_STATUS_TYPE, $USER_NAME) {
        $cmd="/home/snack/scripts/backup.sh $host $login $password $enablepassword";
        //echo $cmd."\n";
        $return = shell_exec($cmd);
        $resu = explode("\n", $return);
        foreach ($resu as $res) {
            $infos = explode("=", $res);
            if ($infos[0] == "result") {
                $result=$infos[1];
            }
            if ($infos[0] == "commit") {
                $commit=$infos[1];
            }
            if ($infos[0] == "backuptype") {
                $backuptype=$infos[1];
            }
        }
        if ($result == "success") {
            $now = new DateTime('NOW');
            $data = Array
                (
                    "Backup" => Array
                    (
                        "commit" => $commit,
                        "datetime" => $now->format("Y-m-d H:i:s"),
                        "nas" => $host,
                        "action" => $ACCT_STATUS_TYPE,
                        "users" => $USER_NAME,
                    )
                );
            #print_r($data);
            $backup = new Backup();
            $backup->create();
            if($backup->save($data)) {
                if (!$this->updateAll(
                    array("Nas.backuptype" => "'$backuptype'"),
                    array("Nas.nasname =" => $host))) {
                        return false;
                }
                return true;
            } else {
                return false;
            }
        }
    }

    public function getInfosNas($nasname, $login, $password, $enablepassword) {
        $results = array();
        $cmd="/home/snack/interface/tools/command.sh $nasname serial $login $password $enablepassword";
        $return = trim(shell_exec($cmd));
        $results['serial'] = $return;
        $cmd="/home/snack/interface/tools/command.sh $nasname version $login $password $enablepassword";
        $return = trim(shell_exec($cmd));
        $results['version'] = $return;
        $cmd="/home/snack/interface/tools/command.sh $nasname model $login $password $enablepassword";
        $return = trim(shell_exec($cmd));
        $results['model'] = $return;
        $cmd="/home/snack/interface/tools/command.sh $nasname image $login $password $enablepassword";
        $return = trim(shell_exec($cmd));
        $results['image'] = $return;
        return $results;
    }

    public function getInfosAllNas() {
        $key = Configure::read('Security.snackkey');
        $nas = $this->find('all');
        foreach ($nas as $n) {
            if ($n['Nas']['nasname'] != "127.0.0.1") {
                if (($n['Nas']['login'] != "") && ($n['Nas']['password'] != "")) {
                    $password = $this->getPassword($n['Nas']['password']);
                    if ($n['Nas']['enablepassword'] != "") {
                        $enablepassword = $this->getPassword($n['Nas']['enablepassword']);
                    }
                    if (($n['Nas']['enablepassword'] == "") || ($enablepassword == "")) {
                        $enablepassword = $password;
                    }
                    if ($password != "") {
                        $results = $this->getInfosNas($n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword);
                        $n['Nas']['version'] = $results['version'];
                        $n['Nas']['image'] = $results['image'];
                        $n['Nas']['serialnumber'] = $results['serial'];
                        $n['Nas']['model'] = $results['model'];
                        $this->save($n);
                    }
                }
            }
        }
    }

    public function getInfosNasAAA($nasname, $login, $password, $enablepassword) {
        $results = array();
        $cmd="/home/snack/interface/tools/command.sh $nasname aaasrv $login $password $enablepassword";
        $return = trim(shell_exec($cmd));
        if (preg_match_all('/RADIUS: .* host (\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $return, $matches)) {
            $results[] = $matches[1];
        }
        return $results;
    }

    public function getInfosAllNasAAA() {
        $key = Configure::read('Security.snackkey');
        $nas = $this->find('all');
        $results = array();
        foreach ($nas as $n) {
            if ($n['Nas']['nasname'] != "127.0.0.1") {
                if (($n['Nas']['login'] != "") && ($n['Nas']['password'] != "")) {
                    $password = $this->getPassword($n['Nas']['password']);
                    if ($n['Nas']['enablepassword'] != "") {
                        $enablepassword = $this->getPassword($n['Nas']['enablepassword']);
                    }
                    if (($n['Nas']['enablepassword'] == "") || ($enablepassword == "")) {
                        $enablepassword = $password;
                    }
                    if ($password != "") {
                        $results[$n['Nas']['nasname']] = $this->getInfosNasAAA($n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword);
                    }
                }
            }
        }
        return $results;
    }

    public function testAAA($nasname, $login, $password, $enablepassword) {
        $results = array();
        $radius1=Configure::read('Parameters.ipAddress');
        $radius2=Configure::read('Parameters.slave_ip_to_monitor');
        $times = 3;
        while($times > 0) {
            $cmd="/home/snack/interface/tools/command.sh $nasname testaaasrv $login $password $enablepassword \"test aaa group radius server $radius1 $login $password legacy\"";
            $return = trim(shell_exec($cmd));
            if (preg_match('/User (.*)/', $return, $matches)) {
                $results[$radius1]=true;
                break;
            }
            $times--;
        }
        if (!isset($results[$radius1])) {
            $results[$radius1]=false;
        }
        if (isset($radius2)) {
            if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $radius2, $matches)) {
                while($times > 0) {
                    $cmd="/home/snack/interface/tools/command.sh $nasname testaaasrv $login $password $enablepassword \"test aaa group radius server $radius2 $login $password legacy\"";
                    $return = trim(shell_exec($cmd));
                    if (preg_match('/User (.*)/', $return, $matches)) {
                        $results[$radius2]=true;
                        break;
                    }
                    $times--;
                }
                if (!isset($results[$radius2])) {
                    $results[$radius2]=false;
                }
            }
        }
        return $results;
    }

    public function testAllNasAAA() {
        
        $nas = $this->find('all');
        $results = array();
        foreach ($nas as $n) {
            if ($n['Nas']['nasname'] != "127.0.0.1") {
                if (($n['Nas']['login'] != "") && ($n['Nas']['password'] != "")) {
                    $password = $this->getPassword($n['Nas']['password']);
                    if ($n['Nas']['enablepassword'] != "") {
                        $enablepassword = $this->getPassword($n['Nas']['enablepassword']);
                    }
                    if (($n['Nas']['enablepassword'] == "") || ($enablepassword == "")) {
                        $enablepassword = $password;
                    }
                    if ($password != "") {
                        $results[$n['Nas']['nasname']] = $this->testAAA($n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword);
                    }
                }
            }
        }
        return $results;
    }

    public function getPassword($secret) {
        $key = Configure::read('Security.snackkey');
        $secret64Dec = base64_decode($secret);
        $password = Security::decrypt($secret64Dec,$key);
        return $password;
    }

    public function getInfosNasClock($nasname, $login, $password, $enablepassword) {
        $results = array();
        $cmd="/home/snack/interface/tools/command.sh $nasname clock $login $password $enablepassword";
        $return = trim(shell_exec($cmd));
        if (preg_match('/(.*\s+\d{4})(Connection)*/', $return, $matches)) {
            return $matches[1];
        }
    }

    public function getInfosAllNasClock() {
        $key = Configure::read('Security.snackkey');
        $nas = $this->find('all');
        $results = array();
        foreach ($nas as $n) {
            if ($n['Nas']['nasname'] != "127.0.0.1") {
                if (($n['Nas']['login'] != "") && ($n['Nas']['password'] != "")) {
                    $password = $this->getPassword($n['Nas']['password']);
                    if ($n['Nas']['enablepassword'] != "") {
                        $enablepassword = $this->getPassword($n['Nas']['enablepassword']);
                    }
                    if (($n['Nas']['enablepassword'] == "") || ($enablepassword == "")) {
                        $enablepassword = $password;
                    }
                    if ($password != "") {
                        $results[$n['Nas']['nasname']] = $this->getInfosNasClock($n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword);
                    }
                }
            }
        }
        return $results;
    }

    public function getInfosNasCDP($nasname, $login, $password, $enablepassword) {
        $results = array();
        $cmd="/home/snack/interface/tools/command.sh $nasname cdp $login $password $enablepassword";
        $return = shell_exec($cmd);
        $infos = explode("\n", $return);
        //debug($infos);
        //$BEGINS = false;
        $results = array();
        $res = array();
        foreach($infos as $line) {
            $info = trim($line);
            if (preg_match('/-------------------------/', $info, $matches)) {
                if (count($res) > 0) {
                    $results[] = $res;
                    $res = array();
                }
            } else {
                $res[] = $info;
            }
        }
        if (count($res) > 0) {
            $results[] = $res;
        }
        //debug($results);
        $listNAS = array();
        $id = 0;
        foreach($results as $result) {
            foreach ($result as $info) {
                if (preg_match('/Device\s+ID\s*:\s*(.*)/', $info, $matches)) {
                    $infos = explode(".", $matches[1]);
                    $listNAS[$id]['hostname'] = $infos[0];
                }
                if (preg_match('/IP\s+address\s*:\s+(.*)/',$info, $matches)) {
                    if ($listNAS[$id]['hostname'] != "") {
                        $listNAS[$id]['ipaddress'] = $matches[1];
                    }
                }
                if (preg_match('/Platform\s*:\s+(.*),\s*Capabilities:\s*(.*)/',$info, $matches)) {
                    if ($listNAS[$id]['hostname'] != "") {
                        $listNAS[$id]['platform'] = $matches[1];
                        $listNAS[$id]['capabilities'] = $matches[2];
                    }
                }
                if (preg_match('/Version\s*(.*),/',$info, $matches)) {
                    if ($listNAS[$id]['hostname'] != "") {
                        $listNAS[$id]['version'] = $matches[1];
                    }
                }
                if (preg_match('/CCM:(.*)/',$info, $matches)) {
                    if ($listNAS[$id]['hostname'] != "") {
                        $listNAS[$id]['version'] = $matches[1];
                    }
                }
                if (preg_match('/Product Version:\s*(.*)\s+/',$info, $matches)) {
                    if ($listNAS[$id]['hostname'] != "") {
                        $listNAS[$id]['version'] = $matches[1];
                    }
                }
                if (preg_match('/(SCCP.*)/',$info, $matches)) {
                    if ($listNAS[$id]['hostname'] != "") {
                        $listNAS[$id]['version'] = $matches[1];
                    }
                }
                if (preg_match('/(SIP.*)/',$info, $matches)) {
                    if ($listNAS[$id]['hostname'] != "") {
                        $listNAS[$id]['version'] = $matches[1];
                    }
                }
                if (preg_match('/Product Version:\s*(.*)\s+/',$info, $matches)) {
                    if ($listNAS[$id]['hostname'] != "") {
                        $listNAS[$id]['version'] = $matches[1];
                    }
                }
                if (preg_match('/Interface\s*:\s*(.*),\s+Port\s+ID\s*\(outgoing port\):\s+(.*)/',$info, $matches)) {
                    if ($listNAS[$id]['hostname'] != "") {
                        $tmp = str_replace("GigabitEthernet", "Gi", $matches[1]);
                        $tmp2 = str_replace("FastEthernet", "Fa", $tmp);
                        $localinterface = str_replace("Ethernet", "Eth", $tmp2);
                        $tmp = str_replace("GigabitEthernet", "Gi", $matches[2]);
                        $tmp2 = str_replace("FastEthernet", "Fa", $tmp);
                        $remoteinterface = str_replace("Ethernet", "Eth", $tmp2);
                        $listNAS[$id]['localinterface'] = $localinterface;
                        $listNAS[$id]['remoteinterface'] = $remoteinterface;
                    }
                }
            }
            $id++;
        }
        return $listNAS;
    }

    
    public function createGraph($listNAS, $hosttodisplay="All") {
        $string="graph network {\noverlap = false;\n";
        $string .= "bgcolor = transparent\n";
        $path = WWW_ROOT.'img/tmp/';
        $file = new File($path.'network.dot', true, 0644);
        $listshapes = array();
        foreach ($listNAS as $hostname=>$listneigh) {
            foreach($listneigh as $neigh) {
                if (!preg_match('/"'.$neigh['hostname'].'"'.'--'.'"'.$hostname.'"/', $string, $matches)) {
                    if ($hosttodisplay=="All") {
                        $string .= '"'.$hostname.'"'.'--'.'"'.$neigh['hostname'].'" ';
                        $string .= '[fontsize=9 headlabel = "        '.$neigh['remoteinterface'].'", taillabel = "'.$neigh['localinterface'].'        "]'."\n";
                    } else {
                        if (!preg_match('/Phone/', $neigh['capabilities'], $matches)) {
                            $string .= '"'.$hostname.'"'.'--'.'"'.$neigh['hostname'].'" ';
                            $string .= '[fontsize=9 headlabel = "        '.$neigh['remoteinterface'].'", taillabel = "'.$neigh['localinterface'].'        "]'."\n";
                        }
                    }
                }
                if ($hosttodisplay=="All" || !preg_match('/Phone/', $neigh['capabilities'], $matches) || !preg_match('/ATA/', $neigh['platform'], $matches)) {
                    if (!array_key_exists($neigh['hostname'], $listshapes)) {
                        if (preg_match('/Host/', $neigh['capabilities'], $matches)) { 
                            $listshapes[$neigh['hostname']] = "Host";
                        }
                        if (preg_match('/Phone/', $neigh['capabilities'], $matches)) {
                            $listshapes[$neigh['hostname']] = "Phone";
                        }
                        if (preg_match('/ATA/', $neigh['platform'], $matches)) {
                            $listshapes[$neigh['hostname']] = "Phone";
                        }
                        if (preg_match('/Switch/', $neigh['capabilities'], $matches)) {
                            if (preg_match('/VMware ESX/', $neigh['platform'], $matches)) {
                                $listshapes[$neigh['hostname']] = "Host";
                            } else {
                                $listshapes[$neigh['hostname']] = "Switch";
                            }
                        }
                        if (preg_match('/Trans-Bridge/', $neigh['capabilities'], $matches)) { 
                            $listshapes[$neigh['hostname']] = "Wifi";
                        }
                    }
                }
            }
        }
        foreach ($listshapes as $hostname=>$type) {
            if ($hosttodisplay=="All") {
                if ($type == "Phone") {
                    $string .= '"'.$hostname.'" [shape=none, image="'.$path.'ipphone.png", label="'.$hostname.'"]'."\n";
                }
            }
            if ($type == "Switch") {
                $string .= '"'.$hostname.'" [shape=none, image="'.$path.'switch.png", label="'.$hostname.'"]'."\n";
            }
            if ($type == "Wifi") {
                $string .= '"'.$hostname.'" [shape=none, image="'.$path.'wifi.png", label="'.$hostname.'"]'."\n";
            }
            if ($type == "Host") {
                $string .= '"'.$hostname.'" [shape=none, image="'.$path.'server.png", label="'.$hostname.'"]'."\n";
            }
        }
        $string .= "}";
        $file->write($string);
        $cmd = "neato -Tpng -O ".WWW_ROOT."img/tmp/network.dot -o ".WWW_ROOT."img/tmp/network";
        //debug($cmd);
        shell_exec($cmd);
        //debug($string);
    }

    public function getInfosAllNasCDP() {
        $key = Configure::read('Security.snackkey');
        $nas = $this->find('all');
        $results = array();
        foreach ($nas as $n) {
            if ($n['Nas']['nasname'] != "127.0.0.1") {
                if (($n['Nas']['login'] != "") && ($n['Nas']['password'] != "")) {
                    $password = $this->getPassword($n['Nas']['password']);
                    if ($n['Nas']['enablepassword'] != "") {
                        $enablepassword = $this->getPassword($n['Nas']['enablepassword']);
                    }
                    if (($n['Nas']['enablepassword'] == "") || ($enablepassword == "")) {
                        $enablepassword = $password;
                    }
                    if ($password != "") {
                        $results[$n['Nas']['shortname']] = $this->getInfosNasCDP($n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword);
                    }
                }
            }
        }
        $this->createGraph($results);
        return $results;
    }


    /* Search MAC Address */
    public function getMacNas($nasname, $login, $password, $enablepassword, $macaddress) {
        $results = array();
        $cmd="/home/snack/interface/tools/command.sh $nasname mac $login $password $enablepassword $macaddress";
        $return = trim(shell_exec($cmd));
        $infos = explode("\n", $return);
        foreach($infos as $info) {
            $res = array();
            if (preg_match('/\s*(\d+)\s+'.$macaddress.'\s+(\w+)\s+(.*)/', $info, $matches)) {
                $res['vlan'] = $matches[1];
                $res['status'] = $matches[2];
                $res['interface'] = $matches[3];
            }
            $results[] = $res;
        }
        return $results;
    }

    public function getMacAllNas($macaddress) {
        $key = Configure::read('Security.snackkey');
        $nas = $this->find('all');
        $results = array();
        foreach ($nas as $n) {
            if ($n['Nas']['nasname'] != "127.0.0.1") {
                if (($n['Nas']['login'] != "") && ($n['Nas']['password'] != "")) {
                    $password = $this->getPassword($n['Nas']['password']);
                    if ($n['Nas']['enablepassword'] != "") {
                        $enablepassword = $this->getPassword($n['Nas']['enablepassword']);
                    }
                    if (($n['Nas']['enablepassword'] == "") || ($enablepassword == "")) {
                        $enablepassword = $password;
                    }
                    if ($password != "") {
                        $results[$n['Nas']['nasname']] = $this->getMacNas($n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword, $macaddress);
                    }
                }
            }
        }
        return $results;
    }

}
?>
