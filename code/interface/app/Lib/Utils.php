<?
/**
 * 
 */
Configure::load('parameters');

class Utils {
    public static function isMAC($string) {
        return preg_match(
            '/^(?:[[:xdigit:]]{2}([-:]?))(?:[[:xdigit:]]{2}\1){4}[[:xdigit:]]{2}$/',
            $string
        );
    }

    public static function isIP($string) {
        return preg_match(
            '/^([[:digit:]]{1,3}.){3}[[:digit:]]{1,3}(\/[[:digit:]]{2})?$/',
            $string
        );
    }

    public static function cleanMAC($mac) {
        $mac = str_replace(':', '', $mac);
        $mac = str_replace('-', '', $mac);

        return $mac;
    }

    public static function cleanPath(&$path) {
        if (substr($path, -1) == '/') {
            $path = substr($path, 0, strlen($path)-1);
        }
    }

    /**
     * Reformat the mac addr for a nice display
     */
    public static function formatMAC( $mac, $delimiter = ':' ) {
        return ( strlen( $mac ) == 12 ) ? 
            substr($mac, 0, 2) . $delimiter
            . substr($mac, 2, 2) . $delimiter
            . substr($mac, 4, 2) . $delimiter
            . substr($mac, 6, 2) . $delimiter
            . substr($mac, 8, 2) . $delimiter
            . substr($mac, 10, 2) : $mac;
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

    public static function getUserCertsPath($username) {
        $base = Configure::read('Parameters.certsPath') . '/' . $username;
        return array(
            'public' => $base . '_cert.pem',
            'key' => $base . '_key.pem',
        );
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
