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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('bootstrap.min');
        echo $this->Html->css('bootstrap-responsive.min');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>

    <style>
        body{
            padding-top: 60px;
        }
    </style>
</head>
<body>
    <div class="navbar navbar-inverse navbar-fixed-top">
	    <div class="navbar-inner">
		<div class="container">
		    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		    </a>
		    <a class="brand" href="#"><?php echo $cakeDescription ?></a>
		    <div class="nav-collapse collapse">
			<ul class="nav">
			    <li class="active"><a href="cakeradius/">Home</a></li>
			    <li><a href="#about">About</a></li>
			    <li><a href="#contact">Contact</a></li>
			</ul>
		    </div>
		</div>
	    </div>
	</div>

	<div id="container">

		<div id="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>

        <hr />
		<footer>
			<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt' => $cakeDescription, 'border' => '0')),
					'http://www.cakephp.org/',
					array('target' => '_blank', 'escape' => false)
				);
			?>
		</footer>
	</div>
    <?php 
        echo $this->element('sql_dump');
        echo $this->Html->script('http://code.jquery.com/jquery-latest.js');
		echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('bootstrapify');
    ?>
</body>
</html>
