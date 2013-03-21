<?php

class SystemDetail extends AppModel {
	/* Gets the uptime of a service or -1 if the service is down. */
	function checkService( $service ) {
		$result = Utils::shell( "ps -e -o comm,etime | grep " . $service . " | tail -1");

        if (!empty($result['msg'][0])
            && preg_match(
                "#.*?(?:(?:([0-9]{2})-)?(?:([0-9]{2}):))?([0-9]{2}):([0-9]{2})#",
                $result['msg'][0],
                $m
            )
        ) {
            return Utils::formatTime($m[1], $m[2], $m[3], $m[4]);
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
		$readfile = Utils::readFile( "/proc/sys/kernel/hostname" );

		return $readfile[0]; /* Hostname */
	}

	/* Gets the system uptime and idletime. */
	public function getUptimes() {
		$content	= Utils::readFile( "/proc/uptime" );
		$result		= explode( " ", $content[0] );
		$result[0]	= Utils::secondToTime( $result[0] ); /* Up Time */
		$result[1]	= Utils::secondToTime( $result[1] ); /* Idle Time */

		return $result;
	}

	/* Gets System Load Average. */
	function getSystemLoad() {
		$content	= Utils::readFile( "/proc/loadavg" );
		$tmp		= explode(" ", $content[0]);
		$result[0]	= $tmp[0] . " " . $tmp[1] . " " . $tmp[2]; /* Load average */
		$tmp		= Utils::shell("top -b -n1 | grep \"Tasks:\"");
		$result[1]	= substr( $tmp['msg'][0], strpos($tmp['msg'][0], ":") + 2 ); /* Tasks */
	
		return $result;
	}

	/* Gets Memory Total&Free */
	function getMemory() {
		$content 	= Utils::readFile( "/proc/meminfo" );
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
		$content = array_slice( Utils::readFile( "/proc/net/dev" ), 2);

		foreach( $content as $key => &$value){
			$value		= preg_split( "/\s+/", $value );
			$value[0]	= substr( $value[0], 0, strpos( $value[0], ":") );
		}

		return $content;
	}

	/* Gets all network devices details */
	function getInterfaces() {
		$content = Utils::shell( "/sbin/ip a" );
		$n = 0;
		$nV4 = 0;
		$nV6 = 0;

		foreach( $content['msg'] as $line){
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
}

?>
