<?php
/**
 * RadreplyFixture
 *
 */
class RadreplyFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'radreply';

/**
 * Import
 *
 * @var array
 */
	public $import = array('model' => 'Radreply', 'connection' => 'test');

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'username' => 'Lorem ipsum dolor sit amet',
			'attribute' => 'Lorem ipsum dolor sit amet',
			'op' => '',
			'value' => 'Lorem ipsum dolor sit amet'
		),
	);

}
