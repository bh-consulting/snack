<?php
App::uses('Radgroupcheck', 'Model');

/**
 * Radgroupcheck Test Case
 *
 */
class RadgroupcheckTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.radgroupcheck'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Radgroupcheck = ClassRegistry::init('Radgroupcheck');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Radgroupcheck);

		parent::tearDown();
	}

	public function testEmptyGroupname() {
		$this->Radgroupcheck->create(array(
			'groupname' => ''
		));
		$this->assertFalse($this->Radgroupcheck->validates());
	}
}
