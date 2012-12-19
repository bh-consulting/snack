<?php

class Logline extends AppModel {

	public function read() {
		return array (
			array('test1' => 'toto1', 'test2' => 'tata1' ),
			array('test1' => 'toto2', 'test2' => 'tata2' )
		);
	}
}

?>
