<?php

class CertsController extends AppController {

    public function get_cert($id) {
        echo $id;
        $this->response->file(Utils::getUserCertsPath('e'));
        //Return reponse object to prevent controller from trying to render a view
        return $this->response;
    }
}

?>