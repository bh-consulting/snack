<?php

class CertsController extends AppController {

    public function isAuthorized($user) {
        
        if (in_array($user['role'], array('tech', 'admin'))
            && in_array($this->action, array(
                'get_public', 'get_key',
            ))
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
    public function get_cert($user, $file_type) {
        $userCert = Utils::getUserCertsPath($user);
        $file = $userCert[$file_type];
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

    public function get_public($user) {
        $this->get_cert($user, 'public');
    }

    public function get_key($user) {
        $this->get_cert($user, 'key');
    }
}

?>