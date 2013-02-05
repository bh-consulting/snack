<?php

/**
*  
*/
class RaduserFixture extends CakeTestFixture {
    public $import = array('model' => 'Raduser', 'connection' => 'test');

    public $records = array(
        array(
            'id' => '1',
            'username' => 'a',
            'admin' => 0,
            'cert_path' => null,
            'comment' => null,
            'is_cisco' => 0,
            'is_loginpass' => 0,
            'is_cert' => 1,
            'is_mac' => 0
        ),
        array(
            'id' => '2',
            'username' => 'b',
            'admin' => 0,
            'cert_path' => null,
            'comment' => null,
            'is_cisco' => 0,
            'is_loginpass' => 0,
            'is_cert' => 0,
            'is_mac' => 1
        ),
        array(
            'id' => '3',
            'username' => 'c',
            'admin' => 0,
            'cert_path' => null,
            'comment' => null,
            'is_cisco' => 1,
            'is_loginpass' => 1,
            'is_cert' => 0,
            'is_mac' => 0
        ),
    );
}

?>