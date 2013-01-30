<?php

class SystemDetail extends AppModel
{
	/* Reads a file. */
	public function readFile( $fileName ) {
		$result = file( $fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

		foreach( $result as &$value)
			$value = trim( $value );

		return $result;
	}

	/* Executes a command. */
	public function execCmd( $cmd ) {
		exec( $cmd, $result );

		foreach( $result as &$value)
			$value = trim( $value );

		return $result;
	}

	/* Converts seconds to days, hours, minutes, seconds. */
	public function convertSecTime( $time ){
		$seconds	= $time%60;
		$minutes	= floor($time/60)%60;
		$hours		= floor($time/3600)%24;
		$days		= floor($time/86400);

		$result =	$days		. (($days > 1) ? __(" days ") : __(" day "));
		$result .=	$hours		. (($hours > 1) ? __(" hours ") : __(" hour "));
		$result .=	$minutes	. (($minutes > 1) ? __(" minutes ") : __(" minute "));
		$result .=	$seconds	. (($seconds > 1) ? __(" seconds") : __(" second"));

		return $result;
	}

	/* Gets the uptime of a service or -1 if the service is down. */
	function checkService( $service ) {
		$result = $this->execCmd( "ps -e -o comm,etimes | grep " . $service . " | tail -1" );
		
		if (!empty($result)) {
			$ps = preg_split( "#\s#", $result[0], NULL, PREG_SPLIT_NO_EMPTY );
			return $this->convertSecTime($ps[1]);
		}

		return -1;
	}

	/* Gets the current date. */
	function getCurDate() {
		$format_en = "F j, Y, g:i a e P";
		$format_fr = "l jS F Y, H:i:s e P";
		return date($format_fr);
	}

	/* Gets the system hostname. */
	public function getHostname() {
		$readfile = $this->readFile( "/proc/sys/kernel/hostname" );

		return $readfile[0]; /* Hostname */
	}

	/* Gets the system uptime and idletime. */
	public function getUptimes() {
		$content	= $this->readFile( "/proc/uptime" );
		$result		= explode( " ", $content[0] );
		$result[0]	= $this->convertSecTime( $result[0] ); /* Up Time */
		$result[1]	= $this->convertSecTime( $result[1] ); /* Idle Time */

		return $result;
	}

	/* Gets System Load Average. */
	function getSystemLoad() {
		$content	= $this->readFile( "/proc/loadavg" );
		$tmp		= explode(" ", $content[0]);
		$result[0]	= $tmp[0] . " " . $tmp[1] . " " . $tmp[2]; 		/* Load average */
		$tmp		= $this->execCmd( "top -b -n1 | grep \"Tasks:\"" );
		$result[1]	= substr( $tmp[0], strpos($tmp[0], ":") + 2 );		/* Tasks */
	
		return $result;
	}

	/* Gets Memory Total&Free */
	function getMemory() {
		$content 	= $this->readFile( "/proc/meminfo" );
		$result[0]	= substr( $content[1], strpos($content[1], ":") + 2 );	/* Total memory */
		$result[1]	= substr( $content[0], strpos($content[0], ":") + 2 );	/* Free memory */
		$result[2]	= ($result[1] - $result[0]) . " kB";			/* Used disk */
	
		return $result;
	}

	/* Gets Disk Space */
	function getDiskSpace() {
		$result[0] = disk_free_space("/") . " kB"; 	/* Free space */
		$result[1] = disk_total_space("/") . " kB"; 	/* Total space */
		$result[2] = ($result[1] - $result[0]) . " kB";	/* Used space */

		return $result;
	}

	/* Gets all network devices statistics */
	function getInterfacesStats() {
		$content = array_slice( $this->readFile( "/proc/net/dev" ), 2);

		foreach( $content as $key => &$value){
			$value		= preg_split( "/\s+/", $value );
			$value[0]	= substr( $value[0], 0, strpos( $value[0], ":") );
		}

		return $content;
	}

	/* Gets all network devices details */
	function getInterfaces() {
		$content	= $this->execCmd( "/sbin/ip a" );
		$n		= 0;
		$nV4		= 0;
		$nV6		= 0;

		foreach( $content as $line){
			if( preg_match("#^([0-9]+): (.*)?:#i", $line, $match)) {
				$n = $match[1];
				$nV4 = 0;
				$nV6 = 0;
				$result[$n]['name'] = $match[2];
			}elseif( preg_match("#link\/(ether|loopback) ([0-9a-f:]+)#i", $line, $match)) {
				$result[$n]['mac'] = $match[2];
			}elseif( preg_match("#inet ([0-9\.\/]+)#i", $line, $match)) {
				$result[$n]['ipv4'][$nV4] = $match[1];
				++$nV4;
			}elseif( preg_match("#inet6 ([0-9a-f\:\/]+)#i", $line, $match)) {
				$result[$n]['ipv6'][$nV6] = $match[1];
				++$nV6;
			}
		}

		return $result;
	}

	/* Gets mysql server uptime */
	function getMysqldUptime()
	{
		$content = $this->execCmd( "/sbin/ip a" );
	}
}
?>
