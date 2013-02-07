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
    <?php
    echo $this->Html->meta('icon');

    echo $this->Html->css('bootstrap.min');
    echo $this->Html->css('mybootstrap');
    echo $this->Html->css('bootstrap-responsive.min');
    echo $this->Html->css('bootstrap-datetimepicker.min');
    echo $this->Html->css('doubleListsSelector');
    echo $this->Html->css('loglines');
    echo $this->Html->css('bhconsulting');
    echo $this->Html->css('jquery-ui-bootstrap/jquery-ui-1.9.2.custom');
    echo $this->Html->css('jquery-ui-bootstrap/jquery.ui.1.9.2.ie');
    echo $this->Html->css('flags.css');

    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>

    <style>
        body {
            padding-top: 60px;
	}
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js">
    </script>
    <![endif]-->
</head>
<body>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="/interface/"><?php echo $cakeDescription ?></a>

            <div class="nav-collapse collapse">
                <ul class="nav">
                    <?php
                    echo '<li class="active">' .
                        $this->Html->link(
                            __('Radius'),
                            array(
                                'controller' => 'radusers',
                                'action' => 'index',
                            ),
                            array()
                        ) . '</li>';

                    echo '<li>' .
                        $this->Html->link(
                            __('Configuration'),
                            array(
                                'controller' => 'configuration',
                                'action' => 'index',
                            ),
                            array()
                        ) .
                        '</li>';

                    echo '<li>' .
                        $this->Html->link(
                            __('Terminal'),
                            array(
                                'controller' => 'terminal',
                                'action' => 'index',
                            ),
                            array()
                        ) .
                        '</li>';
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="row-fluid">

    <? echo $this->fetch('content'); ?>

    </div>
    <hr/>
    <footer>
        <p>
            <a href="http://bh-consulting.net">b.h. consulting 2013</a>
            <!-- Flag sprites downloaded from http://flag-sprites.com/ (CC by-sa) -->
            <span class="flags">
                <? echo $this->Html->link($this->Html->image('blank.gif', array('class' => 'flag flag-fr', 'alt' => __('French'))), array('controller' => 'app', 'action' => 'changeLang', 'fre'), array('escape' => false)); ?>
                <? echo $this->Html->link($this->Html->image('blank.gif', array('class' => 'flag flag-us', 'alt' => __('English'))), array('controller' => 'app', 'action' => 'changeLang', 'eng'), array('escape' => false)); ?>
            </span>
        </p>
    </footer>
</div>
<?php
echo $this->element('sql_dump');
echo $this->Html->script('jquery.min');
echo $this->Html->script('jquery-ui.min');
echo $this->Html->script('bootstrap.min');
echo $this->Html->script('bootstrapify');
echo $this->Html->script('bootstrap-datetimepicker.min');
echo $this->Html->script('doubleListsSelector');
echo $this->Html->script('loglines');
echo $this->Html->script('checkboxRangeSelection');
echo $this->Html->script('bhconsulting');
?>
</script>
</body>
</html>
