<?php

/**
* Test class for RaduserController
* return => {vars, contents, view, result}
*/

class RadusersControllerTest extends ControllerTestCase {
    public $fixture = array('app.raduser');

    public function setUp(){
        $Radusers = $this->generate('Radusers', array(
            'models' => array(
                'Raduser'
            ),
        ));
    }

    public function testIndex() {
        $this->testAction('/radusers/index');
        $this->assertTrue(true);
    }

    public function testAddLoginPassTtls(){
        $data = array(
            'Raduser' => array(
                'username' => 'bob',
                'ttls' => 1,
                'passwd' => 'lol',
                'confirm_password' => 'lol',
                'expiration_date' => '',
                'simultaneous_use'
            )
        );
        $this->testAction(
            '/radusers/add_loginpass',
            array('data' => $data, 'method' => 'post')
        );
        $expected1 = array(
            array('Raduser' => array(
                'username' => 'bob',
                'is_loginpass' => 1
            ))
        );

        //$expected2 = array('bob', 'Cleartext-Password', ':=', 'lol');
        $result = $Radusers->findAll();
        $this->assertEquals($expected1, $result);
    }
}

?>