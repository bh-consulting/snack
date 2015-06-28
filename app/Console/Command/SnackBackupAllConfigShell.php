<?php
App::uses('Utils', 'Lib');
App::uses('Security', 'Utility');

class SnackBackupAllConfigShell extends AppShell {
    public $uses = array('SystemDetail', 'Raduser', 'Nas', 'Backup', 'Radacct', 'Logline');

    public function main() {
        Cakelog::write('debug', 'Backup config for All Nas');
        $this->Nas->backupAllNas("Auto", "System");
    }
}
?>