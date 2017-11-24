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
<!--<div class="container">-->
    <div class="installbody">
    <?php echo $this->fetch('content'); ?>
    <hr/>
    </div>
    
    <div class="col-sm-offset-1 col-sm-11">
    <span class="flags">
    <?php echo $this->Html->link($this->Html->image('blank.gif', array('class' => 'flag flag-fr', 'alt' => __('French'))), array('controller' => 'app', 'action' => 'changeLang', 'fra'), array('escape' => false)); ?>
    <?php echo $this->Html->link($this->Html->image('blank.gif', array('class' => 'flag flag-us', 'alt' => __('English'))), array('controller' => 'app', 'action' => 'changeLang', 'eng'), array('escape' => false)); ?>
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
echo $this->Html->script('snack-ca-regenerate');
echo $this->fetch('script');
?>
</body>
</html>
