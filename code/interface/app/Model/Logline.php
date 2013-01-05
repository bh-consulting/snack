<?php

class Logline extends AppModel {
	var $useTable = 'logs';
	var $primaryKey = 'id';
	var $displayField = 'msg';
	var $name = 'Logline';
}

?>
