<?php
App::uses('Radgroupreply', 'Model');

/**
 * Radgroupreply Test Case
 *
 */
class RadgroupreplyTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.radgroupreply'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Radgroupreply = ClassRegistry::init('Radgroupreply');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Radgroupreply);

		parent::tearDown();
	}

	public function testEmptyGroupname() {
		$this->Radgroupreply->create(array(
			'groupname' => ''
		));
		$this->assertFalse($this->Radgroupreply->validates());
	}

}
