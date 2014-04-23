<?php

class ProcessComponent extends Component {
    
    public function run($cmd, $output = "/dev/null", $output_error = "&1") {
        $command = "nohup $cmd > $output 2>$output_error & echo $!";
        exec($command, $op);
        $pid = (int) $op[0];
        $this->log("[$pid] $command", 'debug');
        return (int) $op[0];
    }
    
    public function running($pid) {
        $command = 'ps -p ' . $pid;
        exec($command, $op);
        if (!isset($op[1]))
            return false;
        else
            return true;
    }

    public function kill($pid) {
        $command = 'kill ' . $pid;
        exec($command);
        if ($this->running($pid) == false) {
            $this->log("SUCCESS $command", 'debug');
            return true;
        } else {
            $this->log("FAILED : $command", 'error');
            return false;
        }
    }
    
    public function mkdir($path) {
        $mkdir_cmd = "mkdir -p $path";
        exec($mkdir_cmd, $op, $ret); 
        if ($ret) {
            $this->log("FAILED : $mkdir_cmd", 'debug');
            return false;
        }
        $this->log("SUCCESS : $mkdir_cmd", 'debug');
        return true;
    }

}
