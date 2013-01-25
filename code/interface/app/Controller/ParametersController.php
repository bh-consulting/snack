<?php
Configure::load('parameters');

class ParametersController extends AppController {
    public $name = 'Parameters';
    public $helpers = array('Html', 'Form');

    public function index() {
	$this->viewParameters();
    }

    public function edit() {
	if($this->request->is('post')){
	    print_r($this->request->data);
	    foreach($this->getDefinitions() as $key => $label) {
		if (($key == "scriptsPath" || $key == "certsPath")
		    && substr($this->request->data[$key], -1) == '/') {
			$this->request->data[$key] = substr(
			    $this->request->data[$key],
			    0,
			    strlen($this->request->data[$key])-1
			);
		    }

		Configure::write(
		    'Parameters.' . $key,
		    $this->request->data[$key]
		);
	    }
	    Configure::dump(
		'parameters.php',
		'default',
		array('Parameters')
	    );

	    $this->Session->setFlash(
		__('Parameters have been updated.'),
		'flash_success'
	    );
	    $this->redirect(array('action' => 'index'));
	}

	$this->viewParameters();
    }

    private function getDefinitions() {
	return array(
	    'contactEmail' => __('Contact email'),
	    'scriptsPath' => __('Scripts path'),
	    'certsPath' => __('Certificates path'),
	    'countryName' => __('Country'),
	    'stateOrProvinceName' => __('State or province'),
	    'localityName' => __('Locality'),
	    'organizationName' => __('Organization'),
	);
    }

    private function viewParameters() {
	foreach ($this->getDefinitions() as $key => $label) {
	    $values = array(
		'id' => $key,
		'label' => $label,
		'value' => Configure::read('Parameters.' . $key),
	    );

	    $this->set($key, $values);
	}
    }
}

?>
