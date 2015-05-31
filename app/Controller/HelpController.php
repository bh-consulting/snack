<?php
class HelpController extends AppController {

    public function isAuthorized($user) {
        
        if ($user['role'] === 'admin' && in_array($this->action, array(
                    'index', 'windows', 'android',
                    'windows_xp_eapttls', 'windows_xp_eaptls',
                ))) {
            return true;
        }

        return parent::isAuthorized($user);
    }

    public function index() {
       $file = new File('/home/snack/id_rsa.pub', false, 0644);
        $tmp="";
        if ($file->exists()) {
            $tmp=$file->read(false, 'rb', false);
            if(preg_match('/^ssh-rsa .*/', $tmp, $matches)) {
                $this->set('sshrsa', $tmp);
            }
        }
    }
    
    public function windows() {
        
    }
    
    public function android() {
        
    }
    
    public function windows_xp_eapttls() {
        
    }
    
    public function windows_xp_eaptls() {
        
    }
}
?>
