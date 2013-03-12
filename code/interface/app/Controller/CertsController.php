<?php

class CertsController extends AppController {

    public function get_public($user) {
        $userCert = Utils::getUserCertsPath($user);
        $this->response->file($userCert['public']);
        return $this->response;
    }

    public function get_key($user) {
        $userCert = Utils::getUserCertsPath($user);
        $this->response->file($userCert['key']);
        return $this->response;
    }
}

?>