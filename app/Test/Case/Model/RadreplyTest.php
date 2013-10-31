<?php
App::uses('Radreply', 'Model');

/**
 * Radreply Test Case
 *
 */
class RadreplyTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.radreply'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Radreply = ClassRegistry::init('Radreply');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Radreply);

		parent::tearDown();
	}

	public function testEmptyUsername(){
		$this->Radreply->create(array(
			'username' => ''
		));
		$this->assertFalse($this->Radreply->validates());
	}

}
