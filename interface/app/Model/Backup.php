<?php

class Backup extends AppModel {
	public $useTable = 'backups';
	public $primaryKey = 'id';
	public $displayField = 'commit';
	public $name = 'Backup';

    public $actions = array();

    public function __construct($id = false, $table = null, $ds = null) {
        $this->actions = array(
            'login' => __('Log in'),
            'logoff' => __('Log off'),
            'wrmem' => __('Write memory'),
            'restore' => __('Restored'),
        );

        parent::__construct($id, $table, $ds);
    }
}

?>
