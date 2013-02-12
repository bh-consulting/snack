<?php

class Backup extends AppModel {
	public $useTable = 'backups';
	public $primaryKey = 'id';
	public $displayField = 'commit';
	public $name = 'Backup';
}

?>
