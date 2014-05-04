<?php
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
class SnackCheckUpdatesShell extends AppShell {
    public function main() {
        $return = shell_exec("sudo apt-get update && apt-get upgrade snack -s");
        if(preg_match('/([0-9]*) upgraded, ([0-9]*) newly installed, ([0-9]*) to remove and ([0-9]*) not upgraded/', $return, $matches)) {
            $file = new File(APP.'tmp/updates', true, 0644);
            $file->write("upgraded $matches[1]\nnewly installed $matches[2]\ntoremove $matches[3]\nupgraded $matches[4]");
        }
        elseif(preg_match('/([0-9]*) mis Ã|  jour, ([0-9]*) nouvellement installés, ([0-9]*) Ã|  enlever et ([0-9]*) non mis Ã|  jour/', $return, $matches)) {
            $file = new File(APP.'tmp/updates', true, 0644);
            $file->write("upgraded $matches[1]\nnewly installed $matches[2]\ntoremove $matches[3]\nupgraded $matches[4]");
        }
    }
}
?>
