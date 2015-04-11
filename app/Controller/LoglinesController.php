<?php

App::uses('Sanitize', 'Utility');
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
class LoglinesController extends AppController {
    public $helpers = array('Html', 'Form');
    public $paginate = array('maxLimit' => 2000, 'limit' => 10, 'order' => array('id' => 'desc'));
    public $components = array(
        'Filters' => array('model' => 'Logline'),
    );

    public function isAuthorized($user) {
        
        if($user['role'] === 'admin' && $this->action === 'index'){
            return true;
        }

        return parent::isAuthorized($user);
    }
    
    public function init() {
        if (isset($this->passedArgs['file'])) {
            $file = $this->passedArgs['file'];
        }
        else {
            $dir = new Folder('/home/snack/logs');
            $files = $dir->find('snacklog.*');
            sort($files);
            $file = $files[0];
        }
        if (isset($this->passedArgs['page'])) {
            $page = $this->passedArgs['page'];
        }
        else {
            $page = 1;
        }
        return array('file' => $file, 'page' => $page);
    }
    
    public function start_time() {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $start = $time;
        return $start;
    }
    
    public function stop_time($start) {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $finish = $time;
        $total_time = round(($finish - $start), 4);
        return $total_time;
    }
    
    public function index() {
        $start = $this->start_time();
        
        $arr = $this->init();
        $constraints=array('facility' => 'local2');
        $this->defaultValues($arr['file'], $arr['page'], $constraints);
        
        $total_time = $this->stop_time($start);
        $this->set('total_time', $total_time);
    }

    public function snack_logs() {
        $start = $this->start_time();
        
        $arr = $this->init();
        $constraints=array('facility' => 'local4');
        $this->defaultValues($arr['file'], $arr['page'], $constraints);
        
        $total_time = $this->stop_time($start);
        $this->set('total_time', $total_time);
    }
    
    public function nas_logs() {
        $start = $this->start_time();

        $arr = $this->init();
        $listnas = $this->Logline->findNas($arr['file']);
        $this->set('listnas', $listnas);
        if (isset($this->passedArgs['host'])) {
            if ($this->passedArgs['host'] != 0) {
                $host = $listnas[$this->passedArgs['host']];
                $constraints = array(
                    'host' => $host
                );
            } else {
                $constraints = array();
            }
        }
        else {
            $constraints = array();
        }

        //$constraints=array();//'facility' => 'local7');
        $this->defaultValues($arr['file'], $arr['page'], $constraints);
        
        $total_time = $this->stop_time($start);
        $this->set('total_time', $total_time);
    }
    
    public function voice_logs() {
        $start = $this->start_time();
        
        $arr = $this->init();
        $pageSize =  Configure::read('Parameters.paginationCount')*2;
        $constraints=array('facility' => 'local7', 'type' => 'voip', 'pageSize' => $pageSize);
        $this->defaultValues($arr['file'], $arr['page'], $constraints);
        $this->set('file', $arr['file']);
        $total_time = $this->stop_time($start);
        $this->set('total_time', $total_time);
    }

    private function defaultValues($file, $page, $constraints){
        $pageSize =  Configure::read('Parameters.paginationCount');
        $this->Filters->addSliderConstraint(array(
            'fields' => 'level', 
            'input' => 'level',
            'default' => 'info',
            'items' => $this->Logline->levels,
        ));
        if (isset($this->params['url']['level'])) {
            if ($this->params['url']['level'] != '') {
                $constraints['priority'] = $this->params['url']['level'];
                $this->set('priority', $constraints['priority']);
            }
        }
        if (isset($this->params['url']['host'])) {
            if ($this->params['url']['host'] != '') {
                $constraints['host'] = $this->params['url']['host'];
            }
        }
        if (isset($this->params['url']['text'])) {
            if ($this->params['url']['text'] != '') {
                $constraints['string'] = $this->params['url']['text'];
            }
        }
        if (isset($this->params['url']['datefrom'])) {
            if ($this->params['url']['datefrom'] != '') {
                $date = new DateTime($this->params['url']['datefrom']);
                $constraints['datefrom'] = $date->format('Y-m-d').'T'.$date->format('H:i:s');
            }
        }
        if (isset($this->params['url']['dateto'])) {
            if ($this->params['url']['dateto'] != '') {
                $date = new DateTime($this->params['url']['dateto']);
                $constraints['dateto'] = $date->format('Y-m-d').'T'.$date->format('H:i:s');
            }
        }
        $arr = $this->Logline->findLogs($page, $constraints, $file);
        //debug($loglines);
        $count = $arr['count'];
        $loglines = $arr['loglines'];
        $this->set('page', $page);
        //$count = $this->Logline->getLineCount($file, $constraints);
        
        $totalPages = floor($count/$pageSize)+1;
        $this->set('nbResults', $count);
        $this->set('totalPages', $totalPages);
        $this->set('file', $file);
        $this->set('loglines', $loglines);
    }

    public function deleteAll($program) {
        if($this->request->is('get')){
            throw new MethodNotAllowedException();
        }

        if (in_array($program, array('freeradius', 'snack'))) {
            if ($this->Logline->deleteAll(array('program' => $program))) {
                $this->Session->setFlash(
                    __('All logs for %s have been deleted.', $program),
                    'flash_success'
                );
                Utils::userlog(__('delete all logs for %s', $program));
            } else {
                $this->Session->setFlash(
                    __('Unable to delete all logs for %s.', $program),
                    'flash_error'
                );
                Utils::userlog(__('error while deleting logs for %s', $program));
            }
        }

        $this->redirect(array('action' => 'index'));
    }
    
    public function chooselogfile($page="index") {
        $home="/home/snack/logs";
        if ($this->request->is('post')) {
            if (!empty($this->request->data)) {
                $dir = new Folder('/home/snack/logs');
                $files = $dir->find('snacklog.*');
                sort($files);
                $file=$files[$this->request->data['Loglines']['chooselogfile']];
                if (preg_match('/(.*)\.tar\.bz2/', $file, $matches)) {
                    system("cd ".$home." && tar jxvf ".$file);
                    $file = $matches[1];
                }
                if (isset($this->nas)) {
                    $this->redirect(array(
                        'action' => $page,
                        'file' => $file,
                    ));
                } else {
                    $this->redirect(array(
                        'action' => $page,
                        'file' => $file,
                    ));
                }
            }
        }
    }
    
    public function choosenas($file="snacklog") {
        if ($this->request->is('post')) {
            if (!empty($this->request->data)) {
                $this->redirect(array(
                    'action' => 'nas_logs',
                    'file' => $file,
                    'host' => $this->request->data['Loglines']['choosenas'],
                ));
            }
        }
    }

    public function logelementradius($type) {
        $start = $this->start_time();
        $arr = $this->init();
        if (isset($type)) {
            if ($type == "index") { 
                $constraints=array('facility' => 'local2');
            }
            if ($type == "snack_logs") { 
                $constraints=array('facility' => 'local4');
            }
            if ($type == "nas_logs") { 
                if (isset($this->request->params['named']['host'])) {
                    if ($this->request->params['named']['host'] != 0) {
                        $listnas = $this->Logline->findNas($arr['file']);
                        $host = $listnas[$this->request->params['named']['host']];
                        $constraints=array(
                            'host' => $host
                        );
                    } else {
                        $constraints=array();//'facility' => 'local7');
                    }
                } else {
                    $constraints=array();//'facility' => 'local7');
                }
            }
            if ($type == "voice_logs") { 
                $pageSize =  Configure::read('Parameters.paginationCount')*2;
                $constraints=array('facility' => 'local7', 'type' => 'voip', 'pageSize' => $pageSize);
            }
        }
        else {
            $constraints=array('facility' => 'local2');
        }
        $this->defaultValues($arr['file'], $arr['page'], $constraints);
        
        $total_time = $this->stop_time($start);
        $this->set('total_time', $total_time);
        
        $this->layout = false;
    }
    
    public function logelementvoice($type) {
        $start = $this->start_time();

        $arr = $this->init();

        if ($type == "voice_logs") { 
            $pageSize =  Configure::read('Parameters.paginationCount')*2;
            $constraints=array('facility' => 'local7', 'type' => 'voip', 'pageSize' => $pageSize);
        }
        
        $this->defaultValues($arr['file'], $arr['page'], $constraints);
        
        $total_time = $this->stop_time($start);
        $this->set('total_time', $total_time);
        
        $this->layout = false;
    }
}
?>
