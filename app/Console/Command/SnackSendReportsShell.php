<?php
App::uses('Utils', 'Lib');

class SnackSendReportsShell extends AppShell {
    public $name = 'SystemDetails';
    public $uses = array('SystemDetail', 'Raduser', 'Nas', 'Backup', 'Radacct', 'Logline');
    
    private $str="";
    private $strPB="";
    private $file;
    private $domain="";
    private $errors=0;
    
    public function main() {
        
        $this->file = new File(APP.'tmp/notifications.txt', true, 0644);

        if (Configure::read('Parameters.role') == "master") {
            $date = new DateTime('23:00');
            if ($date->format('H:i') == date('H:i')) {
                $results = $this->Nas->topology_check();
                $archivedate = Configure::read('Parameters.logs_archive_date');
                $deletedate = Configure::read('Parameters.logs_delete_date');
                if (isset($archivedate)) {
                    $cmd = "curator --logfile /home/snack/logs/curator.log close indices --time-unit days --older-than ".Configure::read('Parameters.logs_archive_date')." --timestring '%Y.%m.%d' --prefix logstash";
                    $return = shell_exec($cmd);
                }
                if (isset($deletedate)) {
                    $cmd = "curator --logfile /home/snack/logs/curator.log delete indices --time-unit days --older-than ".Configure::read('Parameters.logs_delete_date')." --timestring '%Y.%m.%d' --prefix logstash";
                    $return = shell_exec($cmd);
                }
                $this->SystemDetail->createReports();
            }
            //echo $this->str;
            //echo $this->errors;
        } else {
            $datetime1 = new DateTime();
            $listNas = $this->Logline->get_AuthReq();
            if (count($listNas)) {
                $str = "[".$datetime1->format('Y-m-d H:i')."] [ERR] Authentication detected on SLAVE from ";
                foreach ($listNas as $nas) {
                    $str .= $nas." ";
                }
                $str .= "/ CRITICAL";
                $this->file->append($str);
            }
        }
        $this->cleanDBSessions();
        /* check Problems*/
        $this->strPB .= "<h2>SNACK Problems</h2>";
        $this->strPB .= "IP Address : ".Configure::read('Parameters.ipAddress')."<br>";
        $this->checkProblem();
        $this->SystemDetail->checkUpdates();
    }

    public function cleanDBSessions() {
        $this->Radacct->query('delete from radacct where acctauthentic!="RADIUS"');
    }

    public function checkProblem() {
        $results=array();
        $resultsprec=array();
        $this->SystemDetail->checkProblem($resultsprec,"notifications-prec.txt");
        $this->SystemDetail->checkProblem($results);
        $this->errors = $this->errors + count($results);
        $olderrfound=array();
        $newerrfound=array();
        $fixfound=array();
        $found=false;
        foreach ($results as $res) {
            foreach ($resultsprec as $resprec) {
                if ($resprec['msg']==$res['msg'] && $resprec['type'] == $res['type']) {
                    $found=true;
                    $olderrfound[] = $resprec['date']." ".$resprec['type']." ".$resprec['msg'];
                }
            }
            if ($found==false) {
                $newerrfound[] = $res['date']." ".$res['type']." ".$res['msg'];
            }
        }
        $found=false;
        foreach ($resultsprec as $resprec) {
            foreach ($results as $res) {
                if ($resprec['msg']==$res['msg'] && $resprec['type'] == $res['type']) {
                    $found=true;
                }
            }
            if ($found==false) {
                $fixfound[] = $resprec['date']." ".$resprec['type']." ".$resprec['msg'];
            }
        }

        if (count($newerrfound)>0 || count($fixfound)>0) {
            if (count($fixfound)>0) {
                $subject = "[".$this->domain."][FIX] SNACK - Infos";
                $this->strPB .= "<h3>Fix Errors</h3>\n";
                foreach ($fixfound as $fix) {
                    $this->strPB .= $fix."\n";
                }
            }
            echo "\n";
            if (count($newerrfound)>0) {
                $subject = "[".$this->domain."][ERR] SNACK - Infos";
                $this->strPB .= "<h3>New Errors</h3>\n";
                foreach ($newerrfound as $err) {
                    $this->strPB .= $err."\n";
                }
            }
            echo "\n";
            if (count($olderrfound)>0) {
                $this->strPB .= "<h3>Old Errors</h3>\n";
                foreach ($olderrfound as $err) {
                    $this->strPB .= $err."\n";
                }
            }
            $this->SystemDetail->sendMail($subject, $this->strPB);
        }
    }


}
?>
