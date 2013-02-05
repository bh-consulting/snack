<?php

/**
*  f
*/
class RaduserFixture extends CakeTestFixture
{
     public $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'username' => array('type' => 'string', 'length' => 255, 'null' => false),
    );

    public $records = array(
        array('id' => 1, 'username' => 'a'),
        array('id' => 2, 'username' => 'b'),
    );
}