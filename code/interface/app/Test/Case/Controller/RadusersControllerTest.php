<?php

/**
* Test class for RaduserController
* return => {vars, contents, view, result}
*/

class RadusersControllerTest extends ControllerTestCase {
    public $fixtures = array('app.raduser');
    public $dropTables = false;

    public function setUp(){
        parent::setUp();
        $this->autoMock = true;
        $this->dropTables = false;
    }

    public function testIndex() {
        $this->testAction('/radusers/index');
        $this->assertTrue(true);
        // TODO
    }

    public function testAddLoginPass(){
        $data = array(
            'Raduser' => array(
                'username' => 'bob',
                'passwd' => 'lol',
                'confirm_password' => 'lol',
                'expiration_date' => '',
                'simultaneous_use' => '',
            )
        );
        $this->testAction(
            '/radusers/add_loginpass',
            array('data' => $data, 'method' => 'post')
        );

        $expected = array(
            'Raduser' => array(
                'username' => 'bob',
                'admin' => false,
                'comment' => null,
                'is_cisco' => false,
                'is_loginpass' => true,
                'is_cert' => false,
                'is_mac' => false,
                'cert_path' => null,
                'id' => 1,
            )
        );

        //$expected2 = array('bob', 'Cleartext-Password', ':=', 'lol');
        $result = $this->controller->Raduser->findByUsername('bob');
        debug($result);
        $this->assertEquals($expected, $result);
    }
}

?>