<?php
$this->extend('/Common/systemdetails_tabs');
$this->assign('systemdetails_tests_active', 'active');
?>
<br />
<div class="panel panel-default">
  <div class="panel-heading">Test Users</div>
  <div class="panel-body">
    <?php
        $mainLabelOptions = array('class' => 'col-sm-4 control-label');
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
                'between' => '<div class="col-sm-4">',
                'after'   => '</div>',
                'class' => 'form-control'
            ),
        ));

        $myLabelOptions = array('text' => __('Username'));
        echo  $this->Form->input('username', array(
            'label' => array_merge($mainLabelOptions, $myLabelOptions),
            'options' => $users,
            'selected' => array_search($user, $users),
            'empty' => false,
        ));
        $myLabelOptions = array('text' => __('Password'));
        echo $this->Form->input('Password', array('label' => array_merge($mainLabelOptions, $myLabelOptions), 'type' => 'password'));
        $myLabelOptions = array('text' => __('Auth Type'));
        echo  $this->Form->input('authtype', array(
            'label' => array_merge($mainLabelOptions, $myLabelOptions),
            'options' => array(
                'eap-md5' => __('EAP-MD5'),
                'eap-peap' => __('EAP-PEAP-MSCHAPV2'),
                'eap-ttls-pap' => __('EAP-TTLS-PAP'),
                'eap-ttls-mschap' => __('EAP-TTLS-MSCHAPV2'),
                'eap-tls' => __('EAP-TLS'),
            ),
            'empty' => false,
        ));

        echo '<div class="col-sm-4"></div>';
        echo $this->Html->link(
            __('Launch Tests'),
            '#',
            array(
                'class' => 'btn btn-primary',
                'onclick' => 'test_users();',
                'title' => __('Launch Tests'),
        ));
    ?>
    <br/><br/>
    <div id="users"></div>
  </div>
</div>
