<?php
App::uses('Radcheck', 'Model');

/**
 * Radcheck Test Case
 *
 */
class RadcheckTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.radcheck'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Radcheck = ClassRegistry::init('Radcheck');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Radcheck);

		parent::tearDown();
	}

	public function testEmptyUsername() {
		$this->Radcheck->create(array(
			'username' => ''
		));
		$this->assertFalse($this->Radcheck->validates());
	}
}

?>