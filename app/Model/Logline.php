<?php
class Logline extends AppModel {
	public $useTable = false;
	//public $primaryKey = 'id';
	//public $displayField = 'msg';
	public $name = 'Logline';
    public $path = '/home/snack/logs/';
   
    public $levels = array(
		'debug' => 'Debug',
		'info' => 'Info',
		'notice' => 'Notice',
		'warn' => 'Warning',
		'err' => 'Error',
		'crit' => 'Critical',
		'alert' => 'Alert',
		'emerg' => 'Emergency'
	);

	public $validate = array(
		'datefrom' => array(
			'rule' => array('datetime', 'dmy'),
			'message' => 'Format error with date from.',
			'allowEmpty' => true,
		),
		'dateto' => array(
			'rule' => array('datetime', 'dmy'),
			'message' => 'Format error with date to.',
			'allowEmpty' => true,
		),
		'severity' => array(
            'rule' => array(
                'inList',
                array(
                    'debug', 'info', 'notice', 'warn',
                    'err', 'crit', 'alert', 'emerg'
                )
            ),
			'message' => 'Format error with severity.',
			'allowEmpty' => true,
		)
	);
    
    public function findLogs($page, $options=array(), $file="snacklog") {
        $arr = array();
        $pageSize =  Configure::read('Parameters.paginationCount');
        $loglines = array();
        $log = array();
        if (isset($options['pageSize'])) {
            $pageSize = $options['pageSize'];
        }
        
        $cmd = "/home/snack/interface/tools/scriptLogs.sh -f " . $this->path . $file . " -n " . $pageSize . " --page " . $page . " ";
        if (isset($options['facility'])) {
            $cmd .= "--facility " . $options['facility'] . " ";
        }
        if (isset($options['priority'])) {
            $cmd .= "--priority " . $options['priority'] . " ";
        }
        if (isset($options['string'])) {
            if ($options['string'] != '') {
                $cmd .= "--string '" . $options['string'] . "' ";
            }
        }
        if (isset($options['host'])) {
            if ($options['host'] != '') {
                $cmd .= "--host " . $options['host'] . " ";
            }
        }
        if (isset($options['datefrom']) && isset($options['dateto'])) {
            if ($options['datefrom'] != '' && $options['dateto'] != '') {
                $cmd .= "--between-dates " . $options['datefrom'] . " " . $options['dateto'];
            }
        }
        if (isset($options['type'])) {
            if ($options['type'] == 'voip') {
                $cmd .= "--voip ";
            }
        }
        $return = shell_exec($cmd);
        //debug($cmd);
        //debug($return);
        $infos = explode("\n", $return);
        $arr['count'] = $infos[0];
        //debug($count);
        foreach ($infos as $line) {
            if ($line != '') {
                //debug($line);
                if(preg_match('/(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2})\s+([^\s]+)\s+([^\s]+):\s+\[(local\d+)\.(debug|info|notice|warn|err|crit|alert|emerg)\]\s+(.*)/', $line, $matches)) {
                    $date = $matches[1];
                    $host = $matches[2];
                    $program = $matches[3];
                    $facility = $matches[4];
                    $priority = $matches[5];
                    $msg = $matches[6];
                    $log['Logline']['level'] = $priority;
                    $log['Logline']['datetime'] = $date;
                    $log['Logline']['host'] = $host;
                    $log['Logline']['msg'] = $msg;
                    $loglines[] = $log;
                }
            }
        }
        $arr['loglines'] = $loglines;
        return $arr;
    } 

}

?>
