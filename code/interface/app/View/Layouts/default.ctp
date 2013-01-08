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

$cakeDescription = __d('cake_dev', 'b.h. consulting');
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
    echo $this->Html->css('bootstrap-responsive.min');
    echo $this->Html->css('bootstrap-datetimepicker.min');
    echo $this->Html->css('multi-value-ordered-selector');
    echo $this->Html->css('loglines');
    echo $this->Html->css('jquery-ui-bootstrap/jquery-ui-1.9.2.custom');
    echo $this->Html->css('jquery-ui-bootstrap/jquery.ui.1.9.2.ie');

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
                    <li class="active"><a href="/interface/radusers/">Radius</a></li>
                    <li><a href="/interface/config/">Configuration</a></li>
                    <li><a href="/interface/term/">Terminal</a></li>
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
        <p><a href="http://bh-consulting.net">b.h. consulting 2012</a></p>
    </footer>
</div>
<?php
echo $this->element('sql_dump');
echo $this->Html->script('http://code.jquery.com/jquery-latest.js');
echo $this->Html->script('jquery-ui-1.9.2.custom.min');
echo $this->Html->script('bootstrap.min');
echo $this->Html->script('bootstrapify');
echo $this->Html->script('bootstrap-datetimepicker.min');
echo $this->Html->script('loglines');
echo $this->Html->script('multi-value-ordered-selector');
?>
</script>
</body>
</html>
