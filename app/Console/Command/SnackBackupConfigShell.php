<?php
App::uses('Utils', 'Lib');
App::uses('Security', 'Utility');

class SnackBackupConfigShell extends AppShell {
    public $uses = array('SystemDetail', 'Raduser', 'Nas', 'Backup', 'Radacct', 'Logline');

    public function main() {
        $return = shell_exec("echo \$USER_NAME | cut -d'\"' -f2");
        $infos = explode("\n", $return);
        $USER_NAME = $infos[0];
        $return = shell_exec("echo \$ACCT_STATUS_TYPE");
        $infos = explode("\n", $return);
        $ACCT_STATUS_TYPE=$infos[0];
        $return = shell_exec("echo \$NAS_IP_ADDRESS");
        $infos = explode("\n", $return);
        $NAS_IP_ADDRESS=$infos[0];
        $nas = $this->Nas->find('first', array(
            'fields' => array('Nas.nasname', 'Nas.login'),
            'conditions' => array('Nas.nasname' => $NAS_IP_ADDRESS)
        ));
        if (count($nas) > 0) {
            if ($USER_NAME == $nas['Nas']['login']) {
                CakeLog::write('debug', 'NOT Backup config for '.$USER_NAME.'@'.$infos[0]);
                return 0;
            }
        }
        if ($NAS_IP_ADDRESS != "" && $NAS_IP_ADDRESS != "127.0.0.1") {
            Cakelog::write('debug', 'Backup config '.$ACCT_STATUS_TYPE.' for '.$USER_NAME.'@'.$infos[0]);
            $this->Nas->backupOneNas($NAS_IP_ADDRESS, $ACCT_STATUS_TYPE, $USER_NAME);
        }
    }
}
?>