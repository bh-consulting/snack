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
        echo $this->Form->create('SystemDetails', array('action' => 'testAD'));
        echo $this->Form->input('username', array('label' => __('Username')));
        echo $this->Form->input('password', array('label' => __('Password'), 'type' => 'password'));
        

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