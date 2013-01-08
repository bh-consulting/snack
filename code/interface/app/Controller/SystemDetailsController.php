<?php

class SystemDetailsController extends AppController
{
	public $name	= 'SystemDetails';
	public $helpers	= array('Html', 'Form');
    
	public function index()    
	{
		$this->set('hostname', $this->SystemDetail->getHostname());

		$uptimes = $this->SystemDetail->getUptimes();
		$this->set('uptime', $uptimes[0]);
		$this->set('idletime', $uptimes[1]);

		$this->set('curdate', $this->SystemDetail->getCurDate());

		$loads = $this->SystemDetail->getSystemLoad();
		$this->set('loadavg', $loads[0]);
		$this->set('tasks', $loads[1]);

		$memory = $this->SystemDetail->getMemory();
		$this->set('freemem', $memory[0]);
		$this->set('totalmem', $memory[1]);
		$this->set('usedmem', $memory[2]);

		$disk = $this->SystemDetail->getDiskSpace();
		$this->set('freedisk', $disk[0]);
		$this->set('totaldisk', $disk[1]);
		$this->set('useddisk', $disk[2]);

		$this->set('intstats', $this->SystemDetail->getInterfacesStats());
		$this->set('ints', $this->SystemDetail->getInterfaces());

		$radiusUptime = $this->SystemDetail->checkService("freeradius");
		$this->set('radiusstate', ( $radiusUptime == -1) ? "Disabled" : "Enabled for " . $radiusUptime );
		
		$mysqlUptime = $this->SystemDetail->checkService("mysqld");
		$this->set('mysqlstate', ( $mysqlUptime == -1) ? "Disabled" : "Enabled for " . $mysqlUptime );
	}

	public function refresh()
	{
		$this->redirect($this->referer());
	}
}

?>
