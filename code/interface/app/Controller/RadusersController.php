<?php

App::import('Model', 'Radcheck');
class RadusersController extends AppController
{
    public $helpers = array('Html', 'Form');

    public function getRadchecks($id){
        $this->Raduser->id = $id;

        $Radcheck = new Radcheck;
        return $Radcheck->findAllByUsername($this->Raduser->field('username'));
    }

    public function getType($id) {
        if($this->isCisco($id))
            return "Cisco";
        if($this->isLoginPass($id))
            return "Login / Password";
        if($this->isMAC($id))
            return "MAC";
        if($this->isCert($id))
            return "Certificate";
    }

    public function isCisco($id){
        $radchecks = $this->getRadchecks($id);
        foreach ($radchecks as $r) {
            if ($r['Radcheck']['attribute'] == 'NAS-Port-Type') {
                if($r['Radcheck']['value'] == '0' || $r['Radcheck']['value'] == '5'){
                    return true;
                } 
            }
        }
        return false;        
    }

    public function isLoginPass($id) {
        $md5challenge = false;
        $nasporttype = false;

        $radchecks = $this->getRadchecks($id);
        foreach ($radchecks as $r) {
            if ($r['Radcheck']['attribute'] == 'NAS-Port-Type') {
                if($r['Radcheck']['value'] == '15')
                   $nasporttype = true; 
            } else if ($r['Radcheck']['attribute'] == 'EAP-Type') {
                if($r['Radcheck']['attribute'] == 'MD5-CHALLENGE')
                    $md5challenge = true;
            }
            $username = $r['Radcheck']['username'];
        }

        return $md5challenge && $nasporttype && ! $this->isMACAddress($username);
    }

    public function isMAC($id) {
        $md5challenge = false;
        $nasporttype = false;

        $radchecks = $this->getRadchecks($id);
        foreach ($radchecks as $r) {
            if ($r['Radcheck']['attribute'] == 'NAS-Port-Type') {
                if($r['Radcheck']['value'] == '15')
                   $nasporttype = true; 
            } else if ($r['Radcheck']['attribute'] == 'EAP-Type') {
                if($r['Radcheck']['attribute'] == 'MD5-CHALLENGE')
                    $md5challenge = true;
            }
            $username = $r['Radcheck']['username'];
        }

        return $md5challenge && $nasporttype && $this->isMACAddress($username);
    }

    public function isCert($id) {
        $radchecks = $this->getRadchecks($id);
        foreach ($radchecks as $r) {
            if ($r['Radcheck']['attribute'] == 'EAP-Type') {
                if($r['Radcheck']['value'] == 'EAP-TTLS' || $r['Radcheck']['value'] == 'EAP-TLS')
                   return true; 
            }
        }
        return false;        
    }

    public function isMACAddress($string) {
        return preg_match('/^(?:[[:xdigit:]]{2}([-:]))(?:[[:xdigit:]]{2}\1){4}[[:xdigit:]]{2}$/', $string);
    }

    public function index()
    {
        $radusers = $this->Raduser->find('all');
        foreach ($radusers as $r) {
            $r['Raduser']['type'] = 'lol';//$this->getType($r['Raduser']['id']);
        }
        $this->set('radusers', $radusers);
    }

    public function view($id = null)
    {
        $this->Raduser->id = $id;
        $this->set('raduser', $this->Raduser->read());
    }

    public function create_radcheck($username, $attribute, $op, $value){
        $data = array(
            'username' => $username,
            'attribute' => $attribute,
            'op' => $op,
            'value' => $value
        );
        $rad = new Radcheck;
        $rad->create();
        return $rad->save($data);
    }

    public function add($radchecks){
        if($this->request->is('post')){
                    
            $this->Raduser->create();
            $success = $this->Raduser->save($this->request->data);

            foreach($radchecks as $rc)
                $success = $success && $this->create_radcheck($rc[0], $rc[1], $rc[2], $rc[3]);

            if($success){
                if(array_key_exists('cert_path', $this->request->data['Raduser']))
                    $this->Session->setFlash('New user added. Certificate in ' . $this->request->data['Raduser']['cert_path']);
                else
                    $this->Session->setFlash('New user added.');

                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add user.');
            }
        }
    }

    public function add_cisco()
    {
        if ($this->request->is('post')) {
            $username = $this->request->data['Raduser']['username'];
            $radchecks = array(
                array($username,
                    'NAS-Port-Type',
                    '==',
                    $this->request->data['Raduser']['nas-port-type']
                ),
                array($username,
                    'Cleartext-Password',
                    ':=',
                    $this->request->data['Raduser']['password']
                ),
                array($username,
                    'EAP-TYPE',
                    ':=',
                    'MD5-CHALLENGE'
                )
            );

            $this->add($radchecks);

            // TODO: add a cisco user with $this->request->data['Raduser']['username'] / $this->request->data['Raduser']['password']
        }
    }

    public function add_loginpass()
    {
        if ($this->request->is('post')) {

            $username = $this->request->data['Raduser']['username'];
            $radchecks = array(
                array($username,
                    'NAS-Port-Type',
                    '==',
                    '15'
                ),
                array($username,
                    'Cleartext-Password',
                    ':=',
                    $this->request->data['Raduser']['password']
                ),
                array($username,
                    'EAP-TYPE',
                    ':=',
                    'MD5-CHALLENGE'
                )
            );
            $this->add($radchecks);
        }
    }

    public function add_mac(){
        if ($this->request->is('post')) {

            $username = $this->request->data['Raduser']['mac'];
            $this->request->data['Raduser']['username'] = $this->request->data['Raduser']['mac'];
            $radchecks = array(
                array($username,
                    'NAS-Port-Type',
                    '==',
                    '15'
                ),
                array($username,
                    'Cleartext-Password',
                    ':=',
                    $this->request->data['Raduser']['mac']
                ),
                array($username,
                    'EAP-TYPE',
                    ':=',
                    'MD5-CHALLENGE'
                )
            );
            $this->add($radchecks);
        }
    }

    public function add_cert()
    {
        if ($this->request->is('post')) {

            $username = $this->request->data['Raduser']['username'];
            $this->request->data['Raduser']['cert_path'] = '/var/www/cert/newcerts/' . $username . '.pem';
            $radchecks = array(
                array($username,
                    'NAS-Port-Type',
                    '==',
                    '15'
                ),
                array($username,
                    'EAP-TYPE',
                    ':=',
                    'EAP-TTLS'
                )
            );
            $this->add($radchecks);

            // TODO: generate a certificate
        }
    }

    public function edit($id = null)
    {
        $this->Raduser->id = $id;
        if ($this->request->is('get')) {
            $this->request->data = $this->Raduser->read();
        } else {
            if ($this->Raduser->save($this->request->data)) {
                $this->Session->setFlash('User has been updated.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to update user.');
            }
        }
    }

    public function delete($id)
    {
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        // delete matching radchecks
        $radchecks = $this->getRadchecks($id);
        $Radcheck = new Radcheck;
        foreach($radchecks as $r)
            $Radcheck->delete($r['Radcheck']['id']);

        if ($this->Raduser->delete($id)) {
            $this->Session->setFlash('The user with id:' . $id . ' has been deleted.');
            $this->redirect(array('action' => 'index'));
        }

        // TODO: delete certificate on filesystem if necessary
        
    }

    public function login()
    {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash(__('Invalid username or password, try again.'));
            }
        }
    }

    public function logout()
    {
        $this->redirect($this->Auth->logout());
    }

}

?>
