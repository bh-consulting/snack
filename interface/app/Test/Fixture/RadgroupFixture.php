<?php
/**
 * RadgroupFixture
 *
 */
class RadgroupFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'radgroup';

/**
 * Import
 *
 * @var array
 */
	public $import = array('model' => 'Radgroup');

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'groupname' => 'a',
			'cert_path' => 'Lorem ipsum dolor sit amet',
			'comment' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'is_cisco' => 1,
			'is_loginpass' => 1,
			'is_cert' => 1,
			'is_mac' => 1
		),
	);

}
