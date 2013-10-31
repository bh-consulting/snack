<?php
App::uses('Radgroup', 'Model');

/**
 * Radgroup Test Case
 *
 */
class RadgroupTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.radgroup'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Radgroup = ClassRegistry::init('Radgroup');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Radgroup);

		parent::tearDown();
	}

	public function testEmptyGroupname(){
		$this->Radgroup->create(array(
			'groupname' => ''
		));

		$this->assertFalse($this->Radgroup->validates());
	}

	public function testUniqueGroupname(){
		$this->Radgroup->create(array(
			'groupname' => 'a'
		));

		$this->assertFalse($this->Radgroup->validates());
	}

}
