<?php
App::import('Model', 'Nas');

class NasComponent extends Component {

    public function __construct($collection, $params) {
    }

    public function extendNasByIP($ip) {
	$nas = new Nas();
	$nas = $nas->findByNasname($ip);
	
	if(empty($nas)) {
	    $device = array(
		'id' => -1,
		'name' => $ip,
		'ip' => $ip
	    );
	} else {
	    $device = array(
		'id' => $nas['Nas']['id'],
		'name' => $nas['Nas']['shortname'],
		'ip' => $nas['Nas']['nasname']
	    );
	}

	return $device;
    }
}

?>
