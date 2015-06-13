<?php

class CertsController extends AppController {

    public function isAuthorized($user) {
        
        if (in_array($user['role'], array('tech', 'admin'))
            && $this->action == 'get_cert'
        ) {
            return true;
        }

        return parent::isAuthorized($user);
    }

    /**
     * Download the certificate of a user
     * @param  string $user user
     * @return response file to download
     */
    public function get_cert($type) {
    	if($type == 'server') {
    	    $file = Utils::getServerCertPath();
        } elseif($type == 'servercer') {
            $file = Utils::getServerCertCerPath();
    	}
        try {
            $this->response->file($file);
            return $this->response;
        } catch(NotFoundException $e){
            $this->Session->setFlash(
                __('The certificate file %s does not exist.', $file),
                'flash_error'
            );
            Utils::userlog(
                __('error while downloading cert %s', $file),
                'error'
            );
            $this->redirect(array('controller' => 'radusers', 'action' => 'index'));
        }
    }
    
    public function get_cert_user($user, $type) {
        if ($type == "p12") {
            $file = Utils::getUserCertsPath($user);
        }
        if ($type == "pem") {
            $file = Utils::getUserCertsPemPath($user);
        }
        if ($type == "key") {
            $file = Utils::getUserKeyPemPath($user);
        }
        try {
            $this->response->file($file);
            return $this->response;
        } catch(NotFoundException $e){
            $this->Session->setFlash(
                __('The certificate file %s does not exist.', $file),
                'flash_error'
            );
            Utils::userlog(
                __('error while downloading cert %s', $file),
                'error'
            );
            $this->redirect(array('controller' => 'radusers', 'action' => 'index'));
        }
    }
    
}

?>
