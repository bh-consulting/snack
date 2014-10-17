<?php
class HelpController extends AppController {
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
