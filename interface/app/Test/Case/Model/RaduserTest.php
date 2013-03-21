<?php
App::uses('Raduser', 'Model');

/**
 * Raduser Test Case
 *
 */
class RaduserTest extends CakeTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'app.raduser'
	);

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->Raduser = ClassRegistry::init('Raduser');
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->Raduser);

		parent::tearDown();
	}

	public function testIdenticalValues() {
		$this->Raduser->create(array(
			'username' => 'testIdenticalValues',
			'cmp' => 'pass'
		));
		$this->assertTrue($this->Raduser->identicalFieldValues(
			array('pass'),
			'cmp'
		));

		$this->Raduser->create(array(
			'username' => 'testIdenticalValues',
			'cmp' => 'passs'
		));
		$this->assertFalse($this->Raduser->identicalFieldValues(
			array('pass'),
			'cmp'
		));
	}

	public function testIdenticalUsername() {
		$this->Raduser->create(array(
			'username' => 'a',
		));

		$this->assertFalse($this->Raduser->validates());
	}

	public function testEmptyUsername() {
		$this->Raduser->create(array(
			'username' => '',
		));

		$this->assertFalse($this->Raduser->validates());
	}

	public function testIdenticalPassword()
	{
		$this->Raduser->create(array(
			'username' => 'testIdenticalPassword',
			'passwd' => 'a',
			'confirm_password' => 'b',
		));

		$this->assertFalse($this->Raduser->validates());
	}

	/**
	 * testIsMACFormat method
	 *
	 * @return void
	 */
	public function testMACFormat() {
		$this->Raduser->create(array(
			'username' => 'testIdenticalPassword',
			'mac' => 'abc',
		));

		$this->assertFalse($this->Raduser->validates());
	}

	public function testIsMACFormat() {
		$this->assertTrue($this->Raduser->isMACFormat(
			array('11:11:11:11:11:11')
		));

		$this->assertFalse($this->Raduser->isMACFormat(
			array('notmac')
		));
	}

	/**
	 * testIsUniqueMAC method
	 *
	 * @return void
	 */
	public function testIsUniqueMAC() {
		$this->Raduser->create(array(
			'username' => '11:11:11:11:11:11',
		));

		$this->assertFalse($this->Raduser->validates());
	}

	/**
	 * testNotEmptyIfCiscoOrLoginpass method
	 *
	 * @return void
	 */
	public function testEmptyIfCiscoOrLoginpass() {
		$this->Raduser->create(array(
			'username' => 'testNotEmptyIfCiscoOrLoginpass',
			'passwd' => '',
			'is_cisco' => 1
		));

		$this->assertFalse($this->Raduser->validates());

		$this->Raduser->create(array(
			'username' => 'testNotEmptyIfCiscoOrLoginpass',
			'passwd' => '',
			'is_loginpass' => 1
		));

		$this->assertFalse($this->Raduser->validates());
	}

	public function testNotEmptyIfCiscoOrLoginpass() {
		$this->Raduser->create(array(
			'username' => 'testNotEmptyIfCiscoOrLoginpass',
			'is_cisco' => 1
		));

		$this->assertFalse($this->Raduser->notEmptyIfCiscoOrLoginpass(array('')));

		$this->Raduser->create(array(
			'username' => 'testNotEmptyIfCiscoOrLoginpass',
			'is_loginpass' => 1
		));

		$this->assertFalse($this->Raduser->notEmptyIfCiscoOrLoginpass(array('')));
	}

	public function testEmptyLocality() {
		$this->Raduser->create(array(
			'username' => 'testEmptyLocality',
			'locality' => '',
		));

		$this->assertFalse($this->Raduser->validates());
	}

	public function testEmptyOrganization() {
		$this->Raduser->create(array(
			'username' => 'testEmptyOrganization',
			'organization' => '',
		));

		$this->assertFalse($this->Raduser->validates());
	}

	public function testEmptyProvince() {
		$this->Raduser->create(array(
			'username' => 'testEmptyProvince',
			'province' => '',
		));

		$this->assertFalse($this->Raduser->validates());
	}

	public function testNumberSimultaneousUse() {
		$this->Raduser->create(array(
			'username' => 'testNumberSimultaneousUse',
			'simultaneous_use' => 'a',
		));

		$this->assertFalse($this->Raduser->validates());
	}

	public function testNumberTunnelPrivateGroupId() {
		$this->Raduser->create(array(
			'username' => 'testNumberTunnelPrivateGroupId',
			'tunnel-private-group-id' => 'e'
		));

		$this->assertFalse($this->Raduser->validates());
	}

	public function testNumberSessionTimeout() {
		$this->Raduser->create(array(
			'username' => 'testNumberSessionTimeout',
			'session-timeout' => 's'
		));

		$this->assertFalse($this->Raduser->validates());
	}
}
