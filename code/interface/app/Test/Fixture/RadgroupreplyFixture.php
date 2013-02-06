<?php
/**
 * RadgroupreplyFixture
 *
 */
class RadgroupreplyFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'radgroupreply';

/**
 * Import
 *
 * @var array
 */
	public $import = array('model' => 'Radgroupreply', 'connection' => 'test');

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'groupname' => 'Lorem ipsum dolor sit amet',
			'attribute' => 'Lorem ipsum dolor sit amet',
			'op' => '',
			'value' => 'Lorem ipsum dolor sit amet'
		),
	);

}
