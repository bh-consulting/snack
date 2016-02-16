<?php

App::uses('Utils', 'Lib');
App::import('model','Backup');

class Nas extends AppModel {
    private $git = '/home/snack/backups.git/';

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
        return $this->parseClock($return);
    }

    public function parseClock($return) {
        if (preg_match('/show clock\n*\s*(.*\s+\d{4})/m', $return, $matches)) {
            return $matches[1];
        }
    }

    public function getInfosAllNasClock() {
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
        return $this->parseCDP($return);
    }

    public function parseCDP($return) {
        $infos = explode("\n", $return);
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
            $found = false;
            foreach ($result as $info) {
                if (preg_match('/Device\s+ID:\s*(.*)/', $info, $matches)) {
                    $infos = explode(".", $matches[1]);
                    $listNAS[$id]['hostname'] = $infos[0];
                    $found = true;
                } 
                if ($found) {
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
            }
            if ($found) {
                $id++;
            }
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
                        if (!preg_match('/Phone/', $neigh['capabilities'], $matches) && !preg_match('/ATA/', $neigh['platform'], $matches)) {
                            $string .= '"'.$hostname.'"'.'--'.'"'.$neigh['hostname'].'" ';
                            $string .= '[fontsize=9 headlabel = "        '.$neigh['remoteinterface'].'", taillabel = "'.$neigh['localinterface'].'        "]'."\n";
                        }
                    }
                }
                if ($hosttodisplay=="All" || (!preg_match('/Phone/', $neigh['capabilities'], $matches) && !preg_match('/ATA/', $neigh['platform'], $matches))) {
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
        $cmd = "dot -Tpng -O ".WWW_ROOT."img/tmp/network.dot -o ".WWW_ROOT."img/tmp/network";
        //debug($cmd);
        shell_exec($cmd);
        //debug($string);
    }

    public function write_discover($results, $listNasDone) {
        $namefile = "networks.wiki";
        $file = new File($this->git.$namefile, true, 0644);
        $file->write("");
        $file->append("=== List of NAS ===\n");
        $file->append("^  Hostname  ^  IP  ^  ^  Platform  ^  Version  ^\n");
        foreach($listNasDone as $hostname=>$nas) {
            $file->append("|  ");
            $file->append($hostname);
            $file->append("  |  ");
            $file->append($nas['ipaddress'] ? $nas['ipaddress'] : "");
            $file->append("  |  ");
            $file->append($nas['platform'] ? $nas['platform'] : "");
            $file->append("  |  ");
            $file->append($nas['version'] ? $nas['version'] : "");
            $file->append("  |");
            $file->append("\n");
        }
        $file->append("\n");
        $file->append("=== List of Connections ===\n");
        $file->append("^  Hostname  ^  IP  ^  ^  Local Interface  ^  Neighbors  ^  Remote Interface  ^\n");
        foreach($results as $hostname=>$list) {
            $file->append("|  ");
            $file->append($hostname);
            $file->append("  |  ");
            $file->append("  |  ");
            $file->append("  |  ");
            $file->append("  |  ");
            $file->append("  |  ");
            $file->append("\n");
            foreach($list as $nas) {
                $file->append("|  ");
                $file->append("  |  ");
                $file->append($nas['localinterface'] ? $nas['localinterface'] : "");
                $file->append("  |  ");
                $file->append($nas['hostname'] ? $nas['hostname'] : "");
                $file->append("  |  ");
                $file->append($nas['remoteinterface'] ? $nas['remoteinterface'] : "");
                $file->append("  |");
                $file->append("\n");
            }
        }
        $file->close();
        $return = shell_exec("cd $this->git && /usr/bin/git add ".$namefile);
        $return = shell_exec("cd $this->git && /usr/bin/git commit -m AUTO-COMMIT ".$namefile);
    }

    public function show_change_topology() {
        $results = array();
        $return = shell_exec("cd $this->git && /usr/bin/git log networks.wiki");
        if (preg_match_all('/commit\s+([a-f0-9]+)\n.*\nDate:\s+(.*)/m', trim($return), $matches)) {
            $commits = $matches[1];
            $dates = $matches[2];
        }
        $i=0;
        foreach ($commits as $commit) {
            $res = array();
            if ($i+1 < count($commits)) {
                $commit2 = $commits[$i+1];
                $return = shell_exec("cd $this->git && /usr/bin/git diff --stat $commit2 $commit networks.wiki");
                if (preg_match('/\|\s+(\d+.*)/', $return, $matches)) {
                    $res['changes'] = $matches[1];
                }
            }
            $res['date'] = $dates[$i];
            $res['commit'] = $commit;
            $results[] = $res;
            $i++;
        }
        return $results;
    }

    public function getloginpassword($nasname) {
        $nas = $this->find('all');
        $results = array();
        foreach ($nas as $n) {
            if ($n['Nas']['nasname'] == $nasname) {
                $login = $n['Nas']['login'];
                $password = $this->getPassword($n['Nas']['password']);
                if ($n['Nas']['enablepassword'] != "") {
                    $enablepassword = $this->getPassword($n['Nas']['enablepassword']);
                }
                if (($n['Nas']['enablepassword'] == "") || ($enablepassword == "")) {
                    $enablepassword = $password;
                }
                if ($password != "") {
                    return array(
                        'login' => $login,
                        'password' => $password,
                        'enablepassword' => $enablepassword,
                    );
                }
            }
        }
        return 0;
    }

    public function topology_check() {
        $results = $this->getSTPAll();
        foreach($results as $nasname=>$result) {
            if (count($result['rootvlans']) > 0) {
                $nasname = $nasname;
                break;
            }
        }
        $listNasDone = array();

        $nas = $this->find('all');
        $results = array();
        $loginpwd = $this->getloginpassword($nasname);
        if (is_array($loginpwd)) {
            $login = $loginpwd['login'];
            $password = $loginpwd['password'];
            $enablepassword = $loginpwd['enablepassword'];
        }
        $cmd="/home/snack/interface/tools/command.sh $nasname hostname $login $password $enablepassword";
        $depth = 2;
        $return = shell_exec($cmd);
        $hostname = trim($return);
        $root = $hostname;
        $listNasTodo[$hostname] = $nasname;
        $listNasDone[$hostname]['ipaddress'] = $nasname;
        $i=0;
        $results = array();
        while($i<$depth) {
            if (count($listNasTodo) > 0) {
                foreach ($listNasTodo as $hostname=>$nas) {
                    $loginpwd = $this->getloginpassword($nas);
                    if (is_array($loginpwd)) {
                        $login = $loginpwd['login'];
                        $password = $loginpwd['password'];
                        $enablepassword = $loginpwd['enablepassword'];
                        $results[$hostname] = $this->getInfosNasCDP($nas, $login, $password, $enablepassword);
                        unset($listNasTodo[$hostname]);
                        foreach ($results[$hostname] as $neigh) {
                            if (!array_key_exists($neigh['hostname'], $results) && !array_key_exists($neigh['hostname'], $listNasTodo)) {
                                if (preg_match('/Switch/', $neigh['capabilities'], $matches)) {
                                    if (isset($neigh['ipaddress'])) {
                                        $listNasTodo[$neigh['hostname']] = $neigh['ipaddress'];
                                    }
                                }
                            }
                            if ((!array_key_exists($neigh['hostname'], $listNasDone)) || ($root==$neigh['hostname'])) {
                                if (isset($neigh['ipaddress'])) {
                                    $listNasDone[$neigh['hostname']]['ipaddress'] = $neigh['ipaddress'];
                                }
                                if (isset($neigh['platform'])) {
                                    $listNasDone[$neigh['hostname']]['platform'] = $neigh['platform'];
                                }
                                if (isset($neigh['capabilities'])) {
                                    $listNasDone[$neigh['hostname']]['capabilities'] = $neigh['capabilities'];
                                }
                                if (isset($neigh['version'])) {
                                    $listNasDone[$neigh['hostname']]['version'] = $neigh['version'];
                                }
                            }
                        }
                    } else {
                        unset($listNasTodo[$hostname]);
                    }               
                }
            } else {
                break;
            }
            $i++;
        }
        return array($results, $listNasDone);
    }

    public function getInfosAllNasCDP() {
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

    /* get interfaces errors */
    public function getIntfErrors($nasname, $login, $password, $enablepassword) {
        $results = array();
        $cmd="/home/snack/interface/tools/command.sh $nasname err $login $password $enablepassword";
        $return = trim(shell_exec($cmd));
        return $this->parseIntfErrors($return);
    }

    public function parseIntfErrors($return) {
        $infos = explode("\n", $return);
        $tmp=0;
        $res = array();
        foreach($infos as $info) {
            if (preg_match('/Port\s+([\w|-]+)\s+([\w|-]+)\s+([\w|-]+)\s+([\w|-]+)\s+([\w|-]+)\s+([\w|-]+)/', $info, $matches)) {
                $tmp=1;
                $titles1=$matches;
            }
            if (preg_match('/Port\s+([\w|-]+)\s+([\w|-]+)\s+([\w|-]+)\s+([\w|-]+)\s+([\w|-]+)\s+([\w|-]+)\s+([\w|-]+)/', $info, $matches)) {
                $tmp=2;
                $titles2=$matches;
            }
            if ($tmp==1) {
                if (preg_match('/([^\s]+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $info, $matches)) {
                    for ($i=2;$i<8;$i++) {
                        if ($matches[$i] != 0) {
                            $res[$matches[1]][$titles1[$i-1]] = $matches[$i];
                        }
                    }
                }
            }
            if ($tmp==2) {
                if (preg_match('/([^\s]+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $info, $matches)) {
                    for ($i=2;$i<9;$i++) {
                        if ($matches[$i] != 0) {
                            $res[$matches[1]][$titles2[$i-1]] = $matches[$i];
                        }
                    }
                }
            }
        }
        return $res;
    }

    public function getMacAllIntfErrors() {
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
                        $results[$n['Nas']['nasname']] = $this->getIntfErrors($n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword);
                    }
                }
            }
        }
        //debug($results);
        return $results;
    }

    public function getSTP($nasname, $login, $password, $enablepassword) {
        $results = array();
        $cmd="/home/snack/interface/tools/command.sh $nasname stp $login $password $enablepassword";
        $return = trim(shell_exec($cmd));
        return $this->parseSTP($return);
    }

    public function parseSTP($return) {
        $mode = "";
        $rootvlans=array();
        $intfstates=array();
        if (preg_match('/Switch is in\s+(\w+)/', $return, $matches)) {
            $mode = $matches[1];
        }
        if (!preg_match('/Root bridge for: none/', $return, $matches0)) {
            if (preg_match('/Root bridge for:\s((VLAN\d+-*,*\s*)\n*)*/m', $return, $matches)) {
                if (preg_match_all('/(VLAN\d+-*)(VLAN\d+-*)*/', $return, $matches)) {
                    $rootvlans=$matches[1];
                }
            }
        }
        if (preg_match_all('/VLAN0*(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $return, $matches)) {
            $intfstates['vlan'] = $matches[1];
            $intfstates['BLK'] = $matches[2];
            $intfstates['LIS'] = $matches[3];
            $intfstates['LRN'] = $matches[4];
            $intfstates['FWD'] = $matches[5];
            $intfstates['ACT'] = $matches[6];
        }
        $results['rootvlans'] = $rootvlans;
        $results['mode'] = $mode;
        $results['intfstates'] = $intfstates;
        return $results;
    }

    public function getSTPAll() {
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
                        $results[$n['Nas']['nasname']] = $this->getSTP($n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword);
                    }
                }
            }
        }
        return $results;
    }

    public function getENV($nasname, $login, $password, $enablepassword) {
        $results = array();
        $cmd="/home/snack/interface/tools/command.sh $nasname env $login $password $enablepassword";
        $return = trim(shell_exec($cmd));
        if (preg_match('/TEMPERATURE\s+is\s+(\w+)/', $return, $matches)) {
            $temp_status = $matches[1];
        } else {
            $temp_status = 0;
        }
        if (preg_match('/FAN\s*\d*\s*is\s+(\w+)/', $return, $matches)) {
            $fan_status = $matches[1];
        } else {
            $fan_status = 0;
        }
        if (preg_match('/Temperature Value:\s+(\d+)\s+Degree\s+Celsius/', $return, $matches)) {
            $temp_value = $matches[1];
        } else {
            $temp_value = 0;
        }
        $results['temp_status'] = $temp_status;
        $results['fan_status'] = $fan_status;
        $results['temp_value'] = $temp_value;
        return $results;
    }

    public function getENVAll() {
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
                        $results[$n['Nas']['nasname']] = $this->getENV($n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword);
                    }
                }
            }
        }
        return $results;
    }

    public function getVlans($capabilities, $nasname, $login, $password, $enablepassword) {
        if (preg_match('/Switch/', $capabilities, $matches)) {
            $cmd="/home/snack/interface/tools/command.sh $nasname vlan-switch $login $password $enablepassword";
            $return = trim(shell_exec($cmd));
        }
        else if (preg_match('/Router/', $capabilities, $matches)) {
            $cmd="/home/snack/interface/tools/command.sh $nasname vlan-router $login $password $enablepassword";
            $return = trim(shell_exec($cmd));
        }
        return $this->parseVlans($return);
    }

    public function parseVlans($return) {
        $infos = explode("\n", $return);
        $vlans = array();
        foreach($infos as $info) {
            if (preg_match_all('/(\d+)\s+(\S+)\s+active/', $return, $matches)) {
                $i=0;
                foreach($matches[1] as $idvlan) {
                    $vlans[$idvlan] = $matches[2][$i];
                    $i++;
                }
            }
        }
        return $vlans;
    }

    public function getVlansAll($infonas) {
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
                        foreach ($infonas[1] as $hostname=>$infos) {
                            if (isset($infos['ipaddress'])) {
                                if ($infos['ipaddress'] == $n['Nas']['nasname']) {
                                    $capabilities = $infos['capabilities'];
                                }
                            }
                        }
                        $results[$n['Nas']['nasname']] = $this->getVlans($capabilities, $n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword);
                    }
                }
            }
        }
        return $results;
    }


    public function getVTP($nasname, $login, $password, $enablepassword) {
        $cmd="/home/snack/interface/tools/command.sh $nasname vtp-status $login $password $enablepassword";
        $return = trim(shell_exec($cmd));
        $cmd="/home/snack/interface/tools/command.sh $nasname vtp-passwd $login $password $enablepassword";
        $return2 = trim(shell_exec($cmd));    
        $this->parseVTP($return, $return2);
    }

    public function parseVTP($return, $return2) {
        $results = array();
        if (preg_match('/VTP version running\s+:\s+(\d+)/', $return, $matches)) {
            $results['version'] = $matches[1];
        }
        if (preg_match('/VTP Domain Name\s+:\s+(\S+)/', $return, $matches)) {
            $results['domain'] = $matches[1];
        }
        if (preg_match('/VTP Pruning Mode\s+:\s+(\S+)/', $return, $matches)) {
            $results['pruning'] = $matches[1];
        }
        if (preg_match('/VTP Operating Mode\s+:\s+(\S+)/', $return, $matches)) {
            $results['vtpmode'] = $matches[1];
        }
        
        if (preg_match('/VTP Password:\s+(\S+)/', $return2, $matches)) {
            $results['password'] = $matches[1];
        }
        return $results;
    }

    public function getVTPAll() {
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
                        $results[$n['Nas']['nasname']] = $this->getVTP($n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword);
                    }
                }
            }
        }
        return $results;
    }

    public function parseNTP($return) {
        $results = array();
        if (preg_match('/show ntp status\n*\s*Clock is (\w+),\s+stratum\s+\d+,\s+reference\s+is\s+(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/m', $return, $matches)) {
            $results['status'] = $matches[1];
            $results['reference'] = $matches[2];
        }
        return $results;
    }

    public function parseENV($return, $hostname) {
        $results = array();
        $regex = "/show env all\n*(((?!$hostname).*\n*)*)/m";
        if (preg_match($regex, $return, $matches)) {
            $env = $matches[1];
        }
        $fans = array();
        if (isset($env)) {
            if (preg_match_all('/FAN\s*(.*)\s*is (.*)\n/', $env, $matches)) {
                $i=0;
                foreach ($matches[1] as $match1) {
                    if ($match1 == "") {
                        $fans[] = $matches[2][$i];
                    } else {
                        $fans[$match1] = $matches[2][$i];
                    }
                    $i++;
                }
            }
            if (preg_match('/SYSTEM TEMPERATURE\s+is\s+(\w+)/', $env, $matches)) {
                $results['systemtemp'] = $matches[1];
            } else {
                $results['systemtemp'] = "";
            }
            $infos = explode("\n", $env);
            $found = false;
            $powers = array();
            foreach ($infos as $info) {
                if (preg_match('/SW\s+PID\s+Serial#\s+Status\s+Sys\s+Pwr\s+PoE\s+Pwr\s+Watts/', $info, $matches)) {
                    $found = true;
                }
                /*if ($found) {
                    if (preg_match('/(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)\s*(\S*)/', $info, $matches2)) {
                        if (($matches2[1] != "") && (!preg_match('/^-+$/', $matches2[1], $matches3)) && ($matches2[1] != "SW")) {
                            $powers[]['SW'] = $matches2[1];
                            $powers[]['SysPwr'] = $matches2[5];
                        }
                    }
                }
                if ($info == "\n") {
                    $found = false;
                }*/
            }
        }
        //debug($powers);
        $results['fan'] = $fans;
        return $results;
    }

    public function parseIntfClear($return) {
        $results = array();
        $cols = array();
        $infos = explode("\n", $return);
        $in = false;
        foreach ($infos as $info) {
            if (preg_match('/(\S+)\s+is\s*\w*\s+\w+, line protocol is \w+/', $info, $matches)) {
                $intf = trim($matches[1]);
            }
            if (preg_match('/Last clearing of "show interface" counters (.*)\s*/', $info, $matches)) {
                $lastclear = trim($matches[1]);
                $results[$intf] = $lastclear;
            }
        }
        return $results;
    }

    public function parseIntfPack($return) {
        $results = array();
        $cols = array();
        $infos = explode("\n", $return);
        $in = false;
        foreach ($infos as $info) {
            if ($in) {
                if (preg_match('/(\S+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $info, $matches2)) {
                    $results[$matches2[1]][$cols[0]] = $matches2[2];
                    $results[$matches2[1]][$cols[1]] = $matches2[3];
                    $results[$matches2[1]][$cols[2]] = $matches2[4];
                    $results[$matches2[1]][$cols[3]] = $matches2[5];
                } else {
                    $in = false;
                }
            } else {
                if (preg_match('/Port\s+(\w{1,3}Octets)\s+(\w{1,3}UcastPkts)\s+(\w{1,3}McastPkts)\s+(\w{1,3}BcastPkts)\s*/', $info, $matches)) {
                    $in = true;
                    $cols = array($matches[1], $matches[2], $matches[3], $matches[4]);
                }
            }
        }
        return $results;
    }

    public function parseHSRP($return) {
        $results = array();
        $infos = explode("\n", $return);
        $in = false;
        foreach ($infos as $info) {
            if (!$in) {
                if (preg_match('/\s*Interface\s+Grp\s+Pri\s+P\s+State\s+Active\s+Standby\s+Virtual IP/', $info, $matches)) {
                    $in = true;
                }
            } else {
                if (preg_match('/#show/', $info, $matches)) {
                    $in = false;
                } else {
                    if (preg_match('/\s*(\w+)\s+(\d+)\s+(\d+)\s+(\w+)\s+(\w+)\s+(\w+)\s+(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\s+(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $info, $matches)) {
                        $results[] = array(
                            'intf' => $matches[1],
                            'grp' => $matches[2],
                            'pri' => $matches[3],
                            'preempt' => $matches[4],
                            'state' => $matches[5],
                            'active' => $matches[6],
                            'standby' => $matches[7],
                            'virtualip' => $matches[8],
                        );
                    }
                }
            }
        }
        return $results;
    }

    public function parseConf($return) {
        $results = array();
        $infos = explode("\n", $return);
        $in = false;
        foreach ($infos as $info) {
            if (!$in) {
                if (preg_match_all('/show run/', $info, $matches)) {
                    $in = true;
                }
            } else {
                if (preg_match_all('/#exit/', $info, $matches)) {
                    $in = false;
                } else {
                    $results[]=$info;
                }
            }
        }
        return $results;
    }

    public function audit() {
        $nas = $this->find('all');
        $results = array();
        $resultsconn = array();
        $infos = array();
        foreach ($nas as $n) {
            if ($n['Nas']['nasname'] != "127.0.0.1") {
                $nasname = $n['Nas']['nasname'];
                $loginpwd = $this->getloginpassword($nasname);
                if (is_array($loginpwd)) {
                    $login = $loginpwd['login'];
                    $password = $loginpwd['password'];
                    $enablepassword = $loginpwd['enablepassword'];
                    $cmd="/home/snack/interface/tools/audit.sh $nasname $login $password $enablepassword";
                    $return = trim(shell_exec($cmd));
                    $infos[$nasname] = $return;
                    $lines = explode("\n", $return);
                    if (preg_match('/result=(\w+);(\w+)/', $lines[count($lines)-1], $matches)) {
                        $resultsconn[$nasname]['result'] = $matches[1];
                        $resultsconn[$nasname]['type'] = $matches[2];
                    }
                }
            }
        }
        //debug($infos['10.255.1.254']);
        $results = $this->audit_topology_check($infos);
        foreach ($infos as $nasname=>$info) {
            foreach($results['listNasDone'] as $hostname=>$nas) {
                if (isset($nas['ipaddress'])) {
                    if ($nas['ipaddress'] == $nasname) {
                        break;
                    }
                }
            }
            //debug($nasname);
            $results['vtp'][$nasname] = $this->parseVTP($infos[$nasname], $infos[$nasname]);
            $results['stp'][$nasname] = $this->parseSTP($infos[$nasname]);
            $results['intferr'][$nasname] = $this->parseIntfErrors($infos[$nasname]);
            $results['clock'][$nasname] = $this->parseClock($infos[$nasname]);
            $results['vlans'][$nasname] = $this->parseVlans($infos[$nasname]);
            $results['ntp'][$nasname] = $this->parseNTP($infos[$nasname], $hostname);
            $results['env'][$nasname] = $this->parseENV($infos[$nasname], $hostname);
            $results['intfpack'][$nasname] = $this->parseIntfPack($infos[$nasname]);
            $results['intfclr'][$nasname] = $this->parseIntfClear($infos[$nasname]);
            $results['hsrp'][$nasname] = $this->parseHSRP($infos[$nasname]);
            $results['conf'][$nasname] = $this->parseConf($infos[$nasname]);
        }
        $results['results'] = $resultsconn;
        //debug($results['env']);
        return $results;
    }

    public function audit_topology_check($infos) {
        foreach($infos as $nasname=>$return) {
            $listSTP[$nasname] = $this->parseSTP($return);
        }
        
        foreach($listSTP as $nasname=>$result) {
            if (count($result['rootvlans']) > 0) {
                $rootnas = $nasname;
                break;
            }
        }
        $listNasDone = array();

        $nas = $this->find('all');
        $results = array();
        $loginpwd = $this->getloginpassword($rootnas);
        if (is_array($loginpwd)) {
            $login = $loginpwd['login'];
            $password = $loginpwd['password'];
            $enablepassword = $loginpwd['enablepassword'];
        }
        if (preg_match('/(.*) uptime is/' ,$infos[$rootnas],$matches)) {
            $hostname = trim($matches[1]);
        }
        $depth = 1000;
        $root = $hostname;
        $listNasTodo[$hostname] = $nasname;
        $listNasDone[$hostname]['ipaddress'] = $nasname;
        $i=0;
        $results = array();
        while($i<$depth) {
            if (count($listNasTodo) > 0) {
                foreach ($listNasTodo as $hostname=>$nas) {
                    if (array_key_exists($nas, $infos)) {
                        $results[$hostname] = $this->parseCDP($infos[$nas]);
                        unset($listNasTodo[$hostname]);
                        foreach ($results[$hostname] as $neigh) {
                            if (!array_key_exists($neigh['hostname'], $results) && !array_key_exists($neigh['hostname'], $listNasTodo)) {
                                if (preg_match('/Switch/', $neigh['capabilities'], $matches)) {
                                    if (isset($neigh['ipaddress'])) {
                                        $listNasTodo[$neigh['hostname']] = $neigh['ipaddress'];
                                    }
                                }
                            }
                            if ((!array_key_exists($neigh['hostname'], $listNasDone)) || ($root==$neigh['hostname'])) {
                                if (isset($neigh['ipaddress'])) {
                                    $listNasDone[$neigh['hostname']]['ipaddress'] = $neigh['ipaddress'];
                                }
                                if (isset($neigh['platform'])) {
                                    $listNasDone[$neigh['hostname']]['platform'] = $neigh['platform'];
                                }
                                if (isset($neigh['capabilities'])) {
                                    $listNasDone[$neigh['hostname']]['capabilities'] = $neigh['capabilities'];
                                }
                                if (isset($neigh['version'])) {
                                    $listNasDone[$neigh['hostname']]['version'] = $neigh['version'];
                                }
                            }
                        }
                    } else {
                        unset($listNasTodo[$hostname]);
                    }               
                }
            } else {
                break;
            }
            $i++;
        }
        return array(
            'connections' =>$results, 
            'listNasDone' => $listNasDone
        );
    }
}
?>
