<?php
App::uses('Sanitize', 'Utility');
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');

class ReportsController extends AppController {
    
    public $paginate = array('maxLimit' => 10, 'limit' => 10, 'order' => array('id' => 'desc'));
    public $uses = array('Logline', 'Radacct', 'Raduser');
    public $components = array('Mpdf.Mpdf', 'Users');

    public function errorsfromradius_reports() {
        $list = array(
            __('Daily'),
            __('Weekly'),
            __('Monthly'),
            __('All'),
        );
        if (isset($this->request->data['Reports']['choosedate'])) {
            $id = $this->request->data['Reports']['choosedate'];
            $date= $list[$id];
        } else {
            $date = "Daily";
            $id = 0;
        }
        $this->set('list', $list);
        $this->users_snack_login($date);
        $this->get_failures_by_users($date);
        $this->get_failures_by_nas($date);
        $this->set('id', $id);
    }

    public function errorsfromnas_reports() {
        $list = array(
            __('Daily'),
            __('Weekly'),
            __('Monthly'),
            __('All'),
        );
        if (isset($this->request->data['Reports']['choosedate'])) {
            $id = $this->request->data['Reports']['choosedate'];
            $date= $list[$id];
        } else {
            $date = "Daily";
            $id = 0;
        }
        $this->set('list', $list);
        $this->get_errors_from_nas($date);
        $this->get_warnings_from_nas($date);
        $this->set('id', $id);
    }

    public function sessions_reports() {
        $users = array();
        $usernames = $this->Radacct->query('select distinct username from radacct;');
        $sessions = array();
        foreach ($usernames as $username) {
            $session = $this->Radacct->query('select * from radacct where username="'.$username['radacct']['username'].'" order by radacctid desc limit 1;');
            $users[$session[0]['radacct']['radacctid']] =
                $this->Users->extendUsers($username['radacct']['username']);
            $session[0]['radacct']['duration'] = Utils::formatDate(
                array(
                    $session[0]['radacct']['acctstarttime'],
                    $session[0]['radacct']['acctstoptime'],
                ),
                'durdisplay'
            ); 

            if(is_null($session[0]['radacct']['acctstoptime'])) {
                $session[0]['radacct']['durationsec'] = Utils::formatDate(
                    array(
                    $session[0]['radacct']['acctstarttime'],
                    $session[0]['radacct']['acctstoptime'],
                    ),
                    'durdisplaysec'
                ); 
            } else
                $session[0]['radacct']['durationsec'] = -1;
            $sessions[] = $session[0];
        }
        $sessionsbydatetime = array();
        foreach ($sessions as $key => $value){
            $sessionsbydatetime[] = strtotime($value['radacct']['acctstarttime']);
        }
        array_multisort($sessionsbydatetime, SORT_DESC, $sessions);
        $this->set('sessions', $sessions);
        $this->set('users', $users);
    }

    public function exportpdf() {
        // initializing mPDF
        $this->Mpdf->init();

        // setting filename of output pdf file
        $this->Mpdf->setFilename('file.pdf');

        // setting output to I, D, F, S
        $this->Mpdf->setOutput('I');

        // you can call any mPDF method via component, for example:
        $this->Mpdf->SetWatermarkText("default");
    }
    
    public function get_errors_from_nas($date) {
        $res = $this->Logline->get_errors_from_NAS($date);
        $err = $res['err'];
        $lasts = $res['lasts'];
        $this->set('err', $err);
        $this->set('lasts', $lasts);
    }
    
    public function get_warnings_from_nas($date) {
        $res = $this->Logline->get_warnings_from_NAS($date);
        $warn = $res['err'];
        $warnlasts = $res['lasts'];
        $this->set('warn', $warn);
        $this->set('warnlasts', $warnlasts);
    }
    
    public function users_snack_login($date) {
        if ($date == "Daily") {
            $start = date('Y-m-d')."T00:00:00";
            $end = date('Y-m-d')."T".date('h:i:s');
        }
        if ($date == "Weekly") {
            $start = date('Y-m-d', strtotime( '-7 days' ))."T00:00:00";
            $end = date('Y-m-d')."T00:00:00";
        }
        if ($date == "Monthly") {
            $start = date('Y-m-d', strtotime( '-31 days' ))."T00:00:00";
            $end = date('Y-m-d')."T00:00:00";
        }
        
        if ($date == "All") {
            $constraints=array('facility' => 'local4', 'string' => 'logged in', 'pageSize' => '100000');
        } else {
            $constraints=array('facility' => 'local4', 'string' => 'logged in', 'pageSize' => '100000', 'datefrom' => $start, 'dateto' => $end);
        }
        $page=1;
        $arr = $this->Logline->findLogs($page, $constraints);
        $logs = $arr['loglines'];
        $snack_users = array();
        foreach ($logs as $log) {
            $date = new DateTime($log['Logline']['datetime']);
            $strdate = $date->format('Y-m-d H:i:s');
            $snack_users[$strdate] = $log['Logline']['msg'];
        }
        $this->set('snack_users', $snack_users);
    }

    public function get_failures_by_users($date) {
        $res = $this->Logline->get_failures($date);
        $this->set('failures', $res['usersnbfailures']);
        $this->set('users', $res['users']);
        $this->set('logins', $res['logins']);
        $this->set('usernames', $res['usernames']);
    }

    public function get_failures_by_nas($date) {
        if ($date == "Daily") {
            $start = date('Y-m-d')."T00:00:00";
            $end = date('Y-m-d')."T".date('h:i:s');
        }
        if ($date == "Weekly") {
            $start = date('Y-m-d', strtotime( '-7 days' ))."T00:00:00";
            $end = date('Y-m-d')."T00:00:00";
        }
        if ($date == "Monthly") {
            $start = date('Y-m-d', strtotime( '-31 days' ))."T00:00:00";
            $end = date('Y-m-d')."T00:00:00";
        }
        
        if ($date == "All") {
            $constraints=array('facility' => 'local2', 'string' => 'Login incorrect', 'pageSize' => '100000');
        } else {
            $constraints=array('facility' => 'local2', 'string' => 'Login incorrect', 'pageSize' => '100000', 'datefrom' => $start, 'dateto' => $end);
        }

        $page=1;
        $arr = $this->Logline->findLogs($page, $constraints);
        $logs = $arr['loglines'];
        $nasnbfailures=array();
        $lasts=array();
        
        foreach($logs as $log) {
            if (preg_match('/^Login incorrect\s*\(*(.*)\)*: \[(.*)\/.*\] \(from client (.*) port (\d+) /', $log['Logline']['msg'], $matches)) {
                $login = $matches[2];
                $info = $matches[1];
                $nas = $matches[3];
            
                $info = explode("[", $log['Logline']['msg']);
                $info = explode("/", $info[1]);
                $info2 = explode(" ", $info[1]);
                if (array_key_exists($nas, $nasnbfailures)) {
                    $nasnbfailures[$nas] = $nasnbfailures[$nas] + 1;
                } else {
                    $nasnbfailures[$nas] = 1;
                    $lasts[$nas] = $log['Logline']['datetime'];
                }
            }
        }
        $this->set('nasfailures', $nasnbfailures);
        $this->set('naslasts', $lasts);
    }
    
    public function voice_reports_pdf($directorynumber="") {
        $constraints=array();
        //debug($this->request->data);
        if ($directorynumber != "") {
            $constraints['directorynumber'] = $directorynumber;
            $this->set('directorynumber', $constraints['directorynumber']);
        }
        $arr = $this->Logline->voiceNbCalls($constraints);
        $this->set('nbappelsstats', $arr);
        //debug($arr);
        /*$results = $this->Logline->voiceTopCalled($file);
        //debug($results);
        $this->set('resultsCalled', $results);

        $results = $this->Logline->voiceTopCalling($file);
        //debug($results);
        $this->set('resultsOutgoingCalling', $results);*/
        $G = new phpGraph();
        $graph = $G->draw($arr,array(
                //'steps' => 50,
                'height'=>300,
                'width'=>700,
                'filled'=>true,
                'tooltips'=>true,
                'diskLegends' => true,
                'diskLegendsType' => 'label',
                'type' => array(
                    '0'=>'bar',
                    '1'=>'bar',
                    '2'=>'bar',
                    '4'=>'pie',
                ),
                'stroke' => array(
                    '0'=>'red',
                    '1'=>'blue',
                    '2'=>'green'
                ),
                'legends' => array(
                    '0'=>'Total',
                    '1'=>'Calling',
                    '2'=>'Called',
                ),
                'tooltipLegend' => array(
                    'calling'=>'Sample of legend : ',
                    'called'=>'Sample of legend : ',
                ),
                'title' => 'Calls',
            )
        );
        $this->set('graph', $graph);
        $file = new File(APP.'/webroot/img/graph.svg');
        $file->write($graph);
        $this->exportpdf();
        
    }
    
    public function voice_reports() {
        $constraints=array();
        //debug($this->request->data);
        if (isset($this->request->data)) {
            if (isset($this->request->data['Reports']['directorynumber'])) {
                if ($this->request->data['Reports']['directorynumber'] != '') {
                    $constraints['directorynumber'] = $this->request->data['Reports']['directorynumber'];
                    $this->set('directorynumber', $constraints['directorynumber']);
                }
            }
        }
        $arr = $this->Logline->voiceNbCalls($constraints);
        $this->set('nbappelsstats', $arr);
        //debug($arr);
        $G = new phpGraph();
        $graph = $G->draw($arr,array(
                //'steps' => 50,
                'height'=>300,
                'width'=>700,
                'filled'=>true,
                'tooltips'=>true,
                'diskLegends' => true,
                'diskLegendsType' => 'label',
                'type' => array(
                    '0'=>'bar',
                    '1'=>'bar',
                    '2'=>'bar',
                    '4'=>'pie',
                ),
                'stroke' => array(
                    '0'=>'red',
                    '1'=>'blue',
                    '2'=>'green'
                ),
                'legends' => array(
                    '0'=>'Total',
                    '1'=>'Calling',
                    '2'=>'Called',
                ),
                'tooltipLegend' => array(
                    'calling'=>'Sample of legend : ',
                    'called'=>'Sample of legend : ',
                ),
                'title' => 'Calls',
            )
        );
        $this->set('graph', $graph);
    }

}
