<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$cakeDescription = __('SNACK');
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $cakeDescription ?>:
        <?php echo $title_for_layout; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?php
    echo $this->Html->meta('glyphicon glyphicon');
    echo $this->fetch('meta');

    echo $this->Html->css('bootstrap.min');
    echo $this->Html->css('mybootstrap');
    echo $this->Html->css('bootstrap-datetimepicker.min');
    echo $this->Html->css('doubleListsSelector');
    echo $this->Html->css('snack');
    echo $this->Html->css('jquery-ui-bootstrap/jquery-ui-1.9.2.custom');
    echo $this->Html->css('jquery-ui-bootstrap/jquery.ui.1.9.2.ie');
    echo $this->Html->css('flags.css');
    //echo $this->Html->css('jquery.terminal.css');
    echo $this->Html->css('font-awesome.min');
    echo $this->Html->css('bootstrap-toggle-buttons');
    echo $this->Html->css('sb-admin');
    echo $this->Html->css('phpGraph_style');
    echo $this->fetch('css');

    //echo $this->Html->script('snake');
?>
    <style>
        body {
            padding-top: 60px;
	}
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <?php echo $this->Html->script('html5-ie'); ?>
    <![endif]-->
</head>
<body>
    
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">    
        <!--<div class="container">    -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="glyphicon glyphicon-bar"></span>
                <span class="glyphicon glyphicon-bar"></span>
                <span class="glyphicon glyphicon-bar"></span>
            </button>
            <?php
            echo $this->Html->image('logo.png', array('height' => '45px', 'class' => 'logo', 'alt' => 'SNACK'));
            ?>
            <a class="navbar-brand" href="/"><?php echo $cakeDescription ?></a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                    <?php
                    if($this->Session->read('Auth.User')){
                    echo '<li class="' . $this->fetch('radius_active') . '">' .
                        $this->Html->link(
                            __('Radius'),
                            array(
                                'controller' => 'radusers',
                                'action' => 'index',
                            ),
                            array()
                        ) . '</li>';

                    echo '<li class="' . $this->fetch('tftp_active') . '">' .
                        $this->Html->link(
                            __('TFTP'),
                            array(
                                'controller' => 'tftp',
                                'action' => 'index',
                            ),
                            array()
                        ) . '</li>';    
                    echo '<li class="' . $this->fetch('nagios_active') . '">' .
                        $this->Html->link(
                            __('Nagios'),
                            array(
                                'controller' => 'nagios',
                                'action' => 'index',
                            ),
                            array()
                        ) . '</li>';
                    echo '<li class="' . $this->fetch('munin_active') . '">' .
                        $this->Html->link(
                            __('Munin'),
                            array(
                                'controller' => 'munin',
                                'action' => 'index',
                            ),
                            array()
                        ) . '</li>';
                    echo '<li class="' . $this->fetch('about_active') . '">' .
                        $this->Html->link(
                            __('About'),
                            array(
                                'controller' => 'about',
                                'action' => 'index',
                            ),
                            array()
                        ) . '</li>';
                    }
                    /*if(AuthComponent::user('role') == 'root'){
                        echo '<li class="' . $this->fetch('term_active') . '">' .
                            $this->Html->link(
                                __('Terminal'),
                                array(
                                    'controller' => 'terminal',
                                    'action' => 'index',
                                ),
                                array()
                            ) .
                            '</li>';
                    }*/
                ?>
            </ul>
            <ul class="nav navbar-nav pull-right">
                <?php
                echo '<li>' .
                        $this->Html->link(
                            '<div class="loading"></div>',
                            array(
                                'controller' => 'systemDetails',
                                'action' => 'notifications'
                            ),
                            array('escape' => false)
                        ) .
                        '</li>';
                $filename = APP . 'tmp/notifications.txt';
                
                if (file_exists ($filename)) {
                    $nbnotif = count(file($filename))-1;
                    if ($nbnotif == -1) {
                        $nbnotif=0;
                    }
                } else {
                    $nbnotif=0;
                }
                
                if($this->Session->read('Auth.User')){
                    ?>
                    <li class="dropdown">
                        <a id="drop" href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                            <?php
                                //echo "Admin";
                                echo $this->Session->read('Auth.User.username')." (".$this->Session->read('Auth.User.role').")";
                            ?>
                        <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="drop">
                        <?php
                            echo '<li>' .
                            $this->Html->link(
                                __('Preferences') .
                                ' <i class="glyphicon glyphicon-wrench glyphicon-white"></i>',
                                array(
                                    'controller' => 'snackusers',
                                    'action' => 'changepwd/'.$this->Session->read('Auth.User.username'),
                                ),
                                array('escape' => false)
                            ) .
                            '</li>';
                        ?>
                            <li role="presentation" class="divider"></li>
                        <?php
                            echo '<li>' .
                            $this->Html->link(
                                __('Logout') .
                                ' <i class="glyphicon glyphicon-off glyphicon-white"></i>',
                                array(
                                    'controller' => 'snackusers',
                                    'action' => 'logout'
                                ),
                                array('escape' => false)
                            ) .
                            '</li>';
                        ?>
                        </ul>
                    </li>
                    <?php
                    echo '<li>' .
                        $this->Html->link(
                            '<i class="fa fa-bell-o "></i>'.
                            ' <span class="badge pull-right">'.$nbnotif.'</span>',
                            array(
                                'controller' => 'systemDetails',
                                'action' => 'notifications'
                            ),
                            array('escape' => false)
                        ) .
                        '</li>';
                }

                ?>
            </ul>
        </div>
        <!--</div>-->

    </div>
<?php
//debug($this->Session->read());
//debug(AuthComponent::user('username'));

if (Configure::read('Parameters.role')=="slave") {
    echo '<div class="alert alert-danger">';
    echo '<br>';
    echo '<br><center><h2>';
    echo __('Warning this is a SLAVE node : all changement will be not saved');
    echo '</h2></center>';
    echo '</div>';
}
?>
<div class="loading_from_sidebar">
    
</div>
<!--<div class="container">-->
    <div class="bhbody">
    <? echo $this->fetch('content'); ?>
    <hr/>
    </div>
    
    <div class="col-sm-offset-1 col-sm-11">
    <span class="flags">
    <? echo $this->Html->link($this->Html->image('blank.gif', array('class' => 'flag flag-fr', 'alt' => __('French'))), array('controller' => 'app', 'action' => 'changeLang', 'fra'), array('escape' => false)); ?>
    <? echo $this->Html->link($this->Html->image('blank.gif', array('class' => 'flag flag-us', 'alt' => __('English'))), array('controller' => 'app', 'action' => 'changeLang', 'eng'), array('escape' => false)); ?>
    </span>
    <br/>
<?php
if (Configure::read('debug')>0) {
    debug("Memory ".memory_get_usage());
    ?>
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                    <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">Requêtes
                        </a>
                    </h4>
                </div>       
                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                        <?php pr($this->request);?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingTwo">
                    <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">SQL Dump
                        </a>
                    </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                    <div class="panel-body">
                        <?php echo $this->element('sql_dump');?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php 
}


//echo $this->element('sql_dump');
echo $this->Html->script('jquery.min');
echo $this->Html->script('jquery-ui.min');
echo $this->Html->script('bootstrap.min');
//echo $this->Html->script('bootstrapify');
echo $this->Html->script('jquery.toggle.buttons');
echo $this->Html->script('bootstrap-datetimepicker');
echo $this->Html->script('bootstrap-datetimepicker.fr');
echo $this->Html->script('doubleListsSelector');
echo $this->Html->script('backups');
echo $this->Html->script('checkboxRangeSelection');
echo $this->Html->script('jquery.bootstrap.wizard');
echo $this->Html->script('snack');
echo $this->fetch('script');
?>
</body>
</html>
