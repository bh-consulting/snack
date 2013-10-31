<?php
/**
 * RadcheckFixture
 *
 */
class RadcheckFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'radcheck';

/**
 * Import
 *
 * @var array
 */
	public $import = array('model' => 'Radcheck', 'connection' => 'test');

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

?>