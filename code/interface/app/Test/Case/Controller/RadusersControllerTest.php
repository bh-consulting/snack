<?php
App::uses('RadusersController', 'Controller');

/**
 * RadusersController Test Case
 *
 */
class RadusersControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.raduser'
	);

    public function setUp() {
        parent::setUp();
        $this->autoMock = true;
    }
/**
 * testIndex method
 *
 * @return void
 */
	public function testIndex() {
        $this->testAction('/radusers/index');
        $this->assertTrue(true);
	}

/**
 * testExport method
 *
 * @return void
 */
	public function testExport() {
	}

/**
 * testView method
 *
 * @return void
 */
	public function testView() {
	}

/**
 * testViewCert method
 *
 * @return void
 */
	public function testViewCert() {
	}

/**
 * testViewLoginpass method
 *
 * @return void
 */
	public function testViewLoginpass() {
	}

/**
 * testViewMac method
 *
 * @return void
 */
	public function testViewMac() {
	}

/**
 * testAddLoginpass method
 *
 * @return void
 */
	public function testAddLoginpass() {
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
            )
        );

        $result = $this->controller->Raduser->findByUsername('bob');
        unset($result['Raduser']['id']);
        $this->assertEquals($expected, $result);
	}

/**
 * testAddCert method
 *
 * @return void
 */
	public function testAddCert() {
	}

/**
 * testAddMac method
 *
 * @return void
 */
	public function testAddMac() {
	}

/**
 * testEditLoginpass method
 *
 * @return void
 */
	public function testEditLoginpass() {
	}

/**
 * testEditMac method
 *
 * @return void
 */
	public function testEditMac() {
	}

/**
 * testEditCert method
 *
 * @return void
 */
	public function testEditCert() {
	}

/**
 * testDelete method
 *
 * @return void
 */
	public function testDelete() {
	}

/**
 * testCreateCertificate method
 *
 * @return void
 */
	public function testCreateCertificate() {
	}

/**
 * testRemoveCertificate method
 *
 * @return void
 */
	public function testRemoveCertificate() {
	}

/**
 * testRenewCertificate method
 *
 * @return void
 */
	public function testRenewCertificate() {
	}

}
