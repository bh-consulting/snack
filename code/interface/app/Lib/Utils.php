<?
/**
 * 
 */
Configure::load('parameters');

class Utils {
    const NO_ERROR = 0;
    const E_RSA = 1;
    const E_CERTGEN = 2;
    const E_CERTSIGN = 3;
    const E_CRL = 4;
    const E_REVOKE = 5;
    const E_EDITUSER = 6;
    const E_ADDUSER = 7;
    const E_DELUSER = 8;

    public static function isMAC($string) {
	return preg_match('/^(?:[[:xdigit:]]{2}([-:]?))(?:[[:xdigit:]]{2}\1){4}[[:xdigit:]]{2}$/', $string);
    }

    public static function isIP($string) {
	return preg_match('/^([[:digit:]]{1,3}.){3}[[:digit:]]{1,3}(\/[[:digit:]]{2})?$/', $string);
    }

    public static function shell($command) {
	exec($command, $output, $return);

	$msg[] = __('Command:') . $command;
	$msg = array_merge($msg, $output);
	
	$result = array(
	    'code' => $return,
	    'msg' => $msg,
	);

	return $result;
    }

    //TODO
    public static function restart_radius() {
    }

    /*
     * Generate a certificate.
     * @param username - Identify the user in the certificate (Common Name)
     * 
     * @return 0 if certificate was generated, error code otherwise.
     */
    public static function generate_certificate($username) {
	$command = Configure::read('Parameters.scriptsPath')
	    . '/createCertificate '
	    . '"' . Configure::read('Parameters.certsPath') . '" '
	    . '"' . $username. '" '
	    . '"' . Configure::read('Parameters.countryName') . '" '
	    . '"' . Configure::read('Parameters.stateOrProvinceName') . '" '
	    . '"' . Configure::read('Parameters.localityName') . '" '
	    . '"' . Configure::read('Parameters.organizationName') . '" ';

	$result = Utils::shell($command);

	return $result['code'];
    }

    /*
     * Delete and revoke a certificate.
     * @param username - Identify the user in the certificate (Common Name)
     * 
     * @return 0 if certificate was removed, error code otherwise.
     */
    public static function delete_certificate($username) {
	$command = Configure::read('Parameters.scriptsPath')
	    . '/revokeClient '
	    . '"' . Configure::read('Parameters.certsPath') . '" '
	    . '"' . $username. '" ';

	$result = Utils::shell($command);

	if ($result['code'] != Utils::NO_ERROR)
	    return $result['code'];

	$command = 'rm '
	    . '"' . Configure::read('Parameters.certsPath')
	    . $username. '_key.pem " '
	    . '"' . Configure::read('Parameters.certsPath')
	    . $username. '_cert.pem " ';

	$result['code'] = Utils::shell($command);

	return $result['code'];
    }

    /*
     * Generate a new certificate and delete the previous.
     * @param username - Identify the user in the certificate (Common Name)
     * 
     * @return 0 if certificate was generated, error code otherwise.
     */
    public static function renew_certificate($username) {
	$result = Utils::delete_certificate($username);

	if (!$result) {
	    return Utils::create_certificate($username);
	} else {
	    return $result;
	}
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

    public static function getErrorMsg($errorCode) {
	switch ($errorCode) {
	case self::NO_ERROR:
	    return __('No error.');
	    break;
	case self::E_RSA:
	    return __('RSA key generation failed.');
	    break;
	case self::E_CERTGEN:
	    return __('Certificate generation failed.');
	    break;
	case self::E_CERTSIGN:
	    return __('Certificate authentification failed.');
	    break;
	case self::E_CRL:
	    return __('Revocation list update failed.');
	    break;
	case self::E_REVOKE:
	    return __('Certificate revocation failed.');
	    break;
	case self::E_EDITUSER:
	    return __('Unable to update user.');
	    break;
	case self::E_ADDUSER:
	    return __('Unable to add user.');
	    break;
	case self::E_DELUSER:
	    return __('Unable to delete user.');
	    break;
	}
    }
}
?>
