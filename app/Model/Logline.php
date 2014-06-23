<?php
class Logline extends AppModel {
	public $useTable = false;
	public $primaryKey = 'id';
	public $displayField = 'msg';
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
    
    public function find($file, $page, $options=array()) {
        $pageSize =  Configure::read('Parameters.paginationCount');
        $loglines = array();
        $log = array();
        if (isset($options['pageSize'])) {
            $pageSize = $options['pageSize'];
        }
        
        $cmd = "/home/snack/interface/tools/scriptLogs.sh -f $this->path$file -n $pageSize --page $page ";
        if (isset($options['facility'])) {
            $cmd .= "--facility ".$options['facility']." "; 
        }
        if (isset($options['type'])) {
            if ($options['type']=='voip') {
                if (isset($options['string'])) {
                    if ($options['string']!='') {
                        $cmd .= "--voip ".$options['string']." ";
                    } else {
                        $cmd .= "--voip 0 ";
                    }
                } else {
                    $cmd .= "--voip 0 ";
                }                
            } else {
                if (isset($options['string'])) {
                    if ($options['string']!='') {
                        $cmd .= "--string '".$options['string']."' ";
                    }
                }
            }
        }
        else {
            if (isset($options['string'])) {
                if ($options['string'] != '') {
                    $cmd .= "--string '" . $options['string'] . "' ";
                }
            }
        }
        if (isset($options['host'])) {
            if ($options['host']!='') {
                $cmd .= "--host ".$options['host']." ";
            }
        }
        
        if (isset($options['datefrom']) && isset($options['dateto'])) {
            if ($options['datefrom']!='' && $options['dateto']!='') {
                $cmd .= "--between-dates ".$options['datefrom']." ".$options['dateto']; 
            }
        } 
        $return = shell_exec($cmd);
        //debug($cmd);
        //debug($return);
        $infos = explode("\n", $return);
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
        return $loglines;
    }
    
    
    /*public function find_all($facility, $priority) {
        $loglines = array();
        $log = array();
        $return = shell_exec("grep $facility $this->path");
        $infos = explode("\n", $return);
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
        return $loglines;
    }*/
    
    /*public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
        return $this->find("local4", "");
    }*/
    
    public function getLineCount($file, $options=array()) {
        $cmd = "/home/snack/interface/tools/scriptLogs.sh -f $this->path$file ";
        if (isset($options['facility'])) {
            $cmd .= "--facility ".$options['facility']." "; 
        }    
        if (isset($options['type'])) {
            if ($options['type']=='voip') {
                if (isset($options['string'])) {
                    if ($options['string']!='') {
                        $cmd .= "--voip ".$options['string']." ";
                    } else {
                        $cmd .= "--voip 0 ";
                    }
                } else {
                    $cmd .= "--voip 0 ";
                }                
            } else {
                if (isset($options['string'])) {
                    if ($options['string']!='') {
                        $cmd .= "--string '".$options['string']."' ";
                    }
                }
            }
        }
        else {
            if (isset($options['string'])) {
                $cmd .= "--string '".$options['string']."' "; 
            }
            if (isset($options['host'])) {
                if ($options['host']!='') {
                    $cmd .= "--host ".$options['host']." ";
                }
            }
        }
        $cmd .= "-c";
        //debug($cmd);
        $return = shell_exec($cmd);
        return intval($return);
    }
    /*public function PaginateCount($conditions = null, $recursive = 0, $extra = array()) {
    }
        return 9000000000;
    }*/

}

?>
