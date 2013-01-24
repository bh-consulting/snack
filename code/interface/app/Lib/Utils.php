<?
/**
 * 
 */
class Utils {

    public static function isMAC($string) {
	return preg_match('/^(?:[[:xdigit:]]{2}([-:]?))(?:[[:xdigit:]]{2}\1){4}[[:xdigit:]]{2}$/', $string);
    }

    public static function isIP($string) {
	return preg_match('/^([[:digit:]]{1,3}.){3}[[:digit:]]{1,3}(\/[[:digit:]]{2})?$/', $string);
    }

    //TODO
    public static function restart_radius() {
    }

    //TODO
    public static function generate_certificate($username) {
	passthru(Configure::read('Parameter.scriptPath')
	    . 'createCertificate '
	    . $username
	    , $r
	);
	return $r;
    }

    //TODO
    public static function delete_certificate($username) {
    }

    public function getType($id, $nice = false) {
	$types = array(
	    array('cisco', 'Cisco'),
	    array('loginpass', 'Login / Password'),
	    array('mac', 'MAC'),
	    array('cert', 'Certificate')
	);

	if($this->isCisco($id))
	    return $types[0][$nice];
	if($this->isLoginPass($id))
	    return $types[1][$nice];
	if($this->isMAC($id))
	    return $types[2][$nice];
	if($this->isCert($id))
	    return $types[3][$nice];
	return "";
    }

    public function isCiscoCheck($id) {
	$radchecks = $this->getRadchecks($id);
	foreach ($radchecks as $r) {
	    if ($r['Radcheck']['attribute'] == 'NAS-Port-Type') {
		if($r['Radcheck']['value'] == '0'
		    || $r['Radcheck']['value'] == '5') {
		    return true;
		} 
	    }
	}
	return false;        
    }

    public function isLoginPassCheck($id) {
	$md5challenge = false;
	$nasporttype = false;

	$radchecks = $this->getRadchecks($id);
	foreach ($radchecks as $r) {
	    if ($r['Radcheck']['attribute'] == 'NAS-Port-Type') {
		if($r['Radcheck']['value'] == '15')
		    $nasporttype = true; 
	    } else if ($r['Radcheck']['attribute'] == 'EAP-Type') {
		if($r['Radcheck']['value'] == 'MD5-CHALLENGE')
		    $md5challenge = true;
	    }
	    $username = $r['Radcheck']['username'];
	}

	return $md5challenge
	    && $nasporttype
	    && ! $this->isMACAddress($username);
    }

    public function isMACCheck($id) {
	$md5challenge = false;
	$nasporttype = false;

	$radchecks = $this->getRadchecks($id);
	foreach ($radchecks as $r) {
	    if ($r['Radcheck']['attribute'] == 'NAS-Port-Type') {
		if($r['Radcheck']['value'] == '15')
		    $nasporttype = true; 
	    } else if ($r['Radcheck']['attribute'] == 'EAP-Type') {
		if($r['Radcheck']['value'] == 'MD5-CHALLENGE')
		    $md5challenge = true;
	    }
	    $username = $r['Radcheck']['username'];
	}
	return $md5challenge && $nasporttype && $this->isMACAddress($username);
    }

    public function isCertCheck($id) {
	$radchecks = $this->getRadchecks($id);
	foreach ($radchecks as $r) {
	    if ($r['Radcheck']['attribute'] == 'EAP-Type') {
		if($r['Radcheck']['value'] == 'EAP-TTLS'
		    || $r['Radcheck']['value'] == 'EAP-TLS')
		    return true; 
	    }
	}
	return false;        
    }

    public static function getISOCode($httpAcceptLanguage) {
	$langs = array(
	    'fr-FR' => 'fre',
	    'en-US' => 'eng'
	);

	foreach ($langs as $key => $value) {
	    if(explode(',', $httpAcceptLanguage)[0] == $key) {
		return $value;
	    }
	}
	return 'eng';
    }
}
?>
