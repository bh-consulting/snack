<?php

App::uses('Sanitize', 'Utility');

class LoglinesController extends AppController {
    public $helpers = array('Html', 'Form');
    public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));
    public $components = array(
        'Filters' => array('model' => 'Logline'),
        'MultipleAction' => array('model' => 'Logline', 'name' => 'logline'),
    );

    public function index() {
        $this->MultipleAction->process(
            array(
                'success' => array(
                    'delete' => __('Log lines have been removed.')
                ),
                'failed' => array(
                    'delete' => __('Unable to delete log lines.')
                ),
                'warning' => __('Please, select at least one log line !'),
            )
        );

        $this->Filters->addSliderConstraint(array(
            'fields' => 'level', 
            'input' => 'level',
            'default' => 'info',
            'options' => $this->Logline->levels,
        ));

        $this->Filters->addDatesConstraint(array(
            'fields' => 'datetime', 
            'from' => 'datefrom',
            'to' => 'dateto',
        ));

        $this->Filters->addStringConstraint(array(
            'fields' => 'msg',
            'input' => 'text',
        ));

        $this->Filters->paginate();
    }
}
?>
