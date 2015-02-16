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
        if ($nas['Nas']['password'] != "") {
            $secret64Enc = $nas['Nas']['password'];
            $secret64Dec = base64_decode($secret64Enc);
            $password = Security::decrypt($secret64Dec,$key);
        }
        if ($nas['Nas']['enablepassword'] != "") {
            $secret64Enc = $nas['Nas']['enablepassword'];
            $secret64Dec = base64_decode($secret64Enc);
            $enablepassword = Security::decrypt($secret64Dec,$key);
        }
        if ($nas['Nas']['password'] != "") {
            if ($this->backupNas($nas['Nas']['nasname'], $nas['Nas']['login'], $password, $enablepassword, $ACCT_STATUS_TYPE, $USER_NAME)) {
                return true;
            }
            else {
                return false;
            }
        }
    }

    public function backupAllNas($ACCT_STATUS_TYPE, $USER_NAME) {
        $key = Configure::read('Security.snackkey');
        $nas = $this->find('all');
        foreach ($nas as $n) {
            if ($n['Nas']['nasname'] != "127.0.0.1") {
                if ($n['Nas']['password'] != "") {
                    $secret64Enc = $n['Nas']['password'];
                    $secret64Dec = base64_decode($secret64Enc);
                    $password = Security::decrypt($secret64Dec,$key);
                }
                if ($n['Nas']['enablepassword'] != "") {
                    $secret64Enc = $n['Nas']['enablepassword'];
                    $secret64Dec = base64_decode($secret64Enc);
                    $enablepassword = Security::decrypt($secret64Dec,$key);
                }
                if ($n['Nas']['password'] != "") {
                    $this->backupNas($n['Nas']['nasname'], $n['Nas']['login'], $password, $enablepassword, $ACCT_STATUS_TYPE, $USER_NAME);
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
}
?>
