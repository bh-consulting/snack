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

        if ( $USER_NAME != "snack" ) {
            $user = $this->Raduser->find('first', array(
                'fields' => array('Raduser.id', 'Raduser.username', 'Raduser.is_cisco'),
                'conditions' => array('Raduser.username' => $USER_NAME, 'Raduser.is_cisco' => 1)
            ));
            if (count($user) > 0) {
                return 0;
            }
        }

        if ($ACCT_STATUS_TYPE == "") {
            $ACCT_STATUS_TYPE = "auto";
        }
        if ($NAS_IP_ADDRESS != "" && $NAS_IP_ADDRESS != "127.0.0.1") {
            $this->Nas->backupOneNas($NAS_IP_ADDRESS, $ACCT_STATUS_TYPE, $USER_NAME);
        }
        else if ($NAS_IP_ADDRESS != "127.0.0.1") {
            $this->Nas->backupAllNas($ACCT_STATUS_TYPE, "system");
        }
    }
}
?>