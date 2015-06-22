<?php
$this->extend('/Common/logs_tabs');
$this->assign('naslogs_active', 'active');
//$this->passedArgs['file']

if (count($listnas)>1) {
    echo "<br/>";
    $mainLabelOptions = array('class' => 'label-inline control-label');
    $url="";
    if (count($this->request->query) > 0) {
        $url=$url."?";
    }
    foreach($this->request->query as $key=>$value) {
        $url=$url.$key."=".$value."&";
    }
    if (count($this->request->query) > 0) {
        $url = substr($url, 0, -1);
    }
    if (isset($this->passedArgs['file'])) {
        $action = 'choosenas/'.$this->passedArgs['file'].$url;
    } else {
        $action = 'choosenas'.$url;
    }

    echo $this->Form->create('Loglines', array(
        'action' => $action,
        'novalidate' => true, 
        'autocomplete' => 'off',
        'class' => 'form-inline',
        'inputDefaults' => array(
            'div' => 'form-group',
            'class' => 'form-control'
        ),
    ));

    $myLabelOptions = array('text' => __('NAS'));
    echo  $this->Form->input('choosenas', array(
        'label' => array_merge($mainLabelOptions, $myLabelOptions),//__('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NAS &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'),
        'options' => $listnas,
        'selected' => isset($this->passedArgs['host']) ? $this->passedArgs['host'] : 0,
        'empty' => false,
    ));
    $options = array(
        'label' => __('Update'),
        'div' => array(
            'class' => 'form-group',
        ),
        'class' => 'btn btn-primary',
        'before' => '<div class="col-sm-offset-1 col-sm-2">',
        'after' => '</div>'
    );
    echo $this->Form->end($options);
}
echo $this->element('logs_element', array(
    'controller' => 'nas_logs',
    'program' => 'snack',
));

?>
