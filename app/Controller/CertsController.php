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
    public function get_cert($user) {
	if($user == 'server') {
	    $file = Utils::getServerCertPath();
    } elseif($user == 'servercer') {
        $file = Utils::getServerCertCerPath();
	} else {
	    $file = Utils::getUserCertsPath($user);
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
