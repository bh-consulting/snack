<?php
$this->extend('/Common/systemdetails_tabs');
$this->assign('systemdetails_tests_active', 'active');
?>
<br />
<div class="panel panel-default">
  <div class="panel-heading">Local Users</div>
  <div class="panel-body">
    <?php 
        echo $this->Html->link(
            __('Launch Tests'),
            '#',
            array(
                'class' => 'btn btn-primary',
                'onclick' => 'testlocalusers();',
                'title' => __('Launch Tests'),
        ));
    ?>
      <div id="localusers"></div>
    </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">AD Users</div>
  <div class="panel-body">
    <?php
        $mainLabelOptions = array('class' => 'col-sm-1 control-label');
        echo $this->Form->create('SystemDetails', array(
            'action' => 'testAD',
            'novalidate' => true, 
            'autocomplete' => 'off',
            'class' => 'form-horizontal',
            'inputDefaults' => array(
                'div' => 'form-group',
                'label' => array(
                    'class' => $mainLabelOptions
                ),
                'between' => '<div class="col-sm-2">',
                'after'   => '</div>',
                'class' => 'form-control'
            ),
        ));

        $myLabelOptions = array('text' => __('Username'));
        echo $this->Form->input('username', array('label' => array_merge($mainLabelOptions, $myLabelOptions)));
        $myLabelOptions = array('text' => __('Password'));
        echo $this->Form->input('Password', array('label' => array_merge($mainLabelOptions, $myLabelOptions), 'type' => 'password'));
        
        echo $this->Html->link(
            __('Launch Tests'),
            '#',
            array(
                'class' => 'btn btn-primary',
                'onclick' => 'testadusers();',
                'title' => __('Launch Tests'),
        ));
    ?>
      <div id="adusers"></div>
  </div>
</div>