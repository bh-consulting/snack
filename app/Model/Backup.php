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
            'auto' => __('Auto'),
        );

        parent::__construct($id, $table, $ds);
    }

    public function isBackuped($nasname) {
        $backup = $this->find('all', array(
            'conditions' => array('Backup.nas =' => $nasname),
            'order' => array('id' => 'desc'),
            'limit' => 1
        ));
        $datetime1 = new DateTime();
        if (count($backup)>0) {
            $datetime2 = new DateTime($backup[0]['Backup']['datetime']);
            $diff=$datetime2->diff($datetime1);
            $years=$diff->format('%y');
            $months=$diff->format('%m');
            $days=$diff->format('%d');
            if ($years > 0 or $months > 0 or $days > 7) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function dateOfLastBackup($nasname) {
        $backup = $this->find('all', array(
            'conditions' => array('Backup.nas =' => $nasname),
            'order' => array('id' => 'desc'),
            'limit' => 1
        ));
        if (count($backup) > 0) {
            return $backup[0]['Backup']['datetime'];
        } else {
            return 0;
        }
    }
}

?>
