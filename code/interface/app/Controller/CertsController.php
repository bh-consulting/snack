<?php

class CertsController extends AppController {

    public function get_cert($user) {
        $userCert = Utils::getUserCertsPath('e');
        $this->response->file($userCert['cert']);
        return $this->response;
    }

    public function get_key($user) {
        $userCert = Utils::getUserCertsPath('e');
        $this->response->file($userCert['key']);
        return $this->response;
    }
}

?>