<?php
class MultipleActionComponent extends Component {
	private $controller, $modelName, $itemName;

	public function __construct($collection, $params) {
		$this->controller = $collection->getController();
		$this->modelName = $params['model'];
		$this->itemName = strtolower($params['name']);
	}

    public function process($messages = array()) {
        if ($this->controller->request->is('post')) {
            if (isset($this->controller->request
                ->data['MultiSelection'][$this->itemName])
                && is_array($this->controller->request
                ->data['MultiSelection'][$this->itemName])
            ) {
                $success = true;
                foreach( $this->controller->request
                    ->data['MultiSelection'][$this->itemName] as $id
                ) {
                    switch( $this->controller->request->data['action'] ) {
                    case "delete":
                        $success = $success
                            && $this->controller->{$this->modelName}->delete($id);
                        if ($success) {
                            Utils::userlog(
                                __('deleted %s #%s', $this->itemName, $id)
                            );
                        } else {
                            Utils::userlog(
                                __(
                                    'error while deleting %s #%s',
                                    $this->itemName,
                                    $id
                                ),
                                'error'
                            );
                        }
                        break;
                    }
                }

                if($success){
                    switch( $this->controller->request->data['action'] ) {
                    case "delete":
                        if (isset($messages['success']['delete'])) {
                            $this->controller->Session->setFlash(
                                $messages['success']['delete'],
                                'flash_success'
                            );
                        } else {
                            $this->controller->Session->setFlash(
                                __('%s have been removed.', $this->itemName),
                                'flash_success'
                            );
                        }
                        break;
                    }
                } else {
                    switch( $this->controller->request->data['action'] ) {
                    case "delete":
                        if (isset($messages['failed']['delete'])) {
                            $this->controller->Session->setFlash(
                                $messages['failed']['delete'],
                                'flash_error'
                            );
                        } else {
                            $this->controller->Session->setFlash(
                                __('Unable to delete %s.', $this->itemName),
                                'flash_error'
                            );
                        }
                        break;
                    }
                }
            } else {
                if (isset($messages['warning'])) {
                    $this->controller->Session->setFlash(
                        $messages['warning'],
                        'flash_warning'
                    );
                } else {
                    $this->controller->Session->setFlash(
                        __('Please, select at least one %s !', $this->itemName),
                        'flash_warning'
                    );
                }
            }
        }
    }
}
?>
