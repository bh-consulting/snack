<?
if(AuthComponent::user('role') != 'tech'){
?>
    <ul style="z-index:100;" class="nav list-group bs-sidenav affix mainmenu">
	<li class="<?php echo $this->fetch('users_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="glyphicon glyphicon-user"></i> ' . __('Users'),
    array(
        'controller' => 'radusers',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
	</li>
	<li class="<?php echo $this->fetch('groups_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="glyphicon glyphicon-list"></i> ' . __('Groups'),
    array(
        'controller' => 'radgroups',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
	</li>
	<li class="<?php echo $this->fetch('nas_active'); ?>">
<?php

if($nasunwritten)
    $iconnas = '<i class="glyphicon glyphicon-hdd glyphicon-red" title="' . __('There is at least one NAS not synchronized with the starting configuration.') . '"></i>';
else
    $iconnas = '<i class="glyphicon glyphicon-hdd glyphicon-green" title="' . __('All NAS seem synchronized with the starting configuration.') . '"></i>';

echo $this->Html->link(
    $iconnas . ' ' . __('NAS'),
    array(
        'controller' => 'nas',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
	</li>
	<li class="<?php echo $this->fetch('session_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="glyphicon glyphicon-ok"></i> ' . __('Sessions'),
    array(
        'controller' => 'radaccts',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
	</li>
	<li class="<?php echo $this->fetch('logs_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="glyphicon glyphicon-list-alt"></i> ' . __('Logs'),
    array(
        'controller' => 'loglines',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
	</li>
    <!--<li class="<?php echo $this->fetch('reporting_active'); ?>">
<?php
/*echo $this->Html->link(
    '<i class="glyphicon glyphicon-th-large"></i> ' . __('Reporting'),
    array(
        'controller' => 'reporting',
        'action' => 'index',
    ),
    array('escape' => false)
);*/
?>
    </li>-->
	<li class="<?php echo $this->fetch('dashboard_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="glyphicon glyphicon-dashboard"></i> ' . __('Server dashboard'),
    array(
        'controller' => 'systemDetails',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
	</li>
<?php if(AuthComponent::user('role') == 'root'){ ?>
	<li class="<?php echo $this->fetch('param_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="glyphicon glyphicon-wrench"></i> ' . __('Server parameters'),
    array(
        'controller' => 'parameters',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
    </li>
    <?php } ?>
    <li class="<?php echo $this->fetch('help_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="glyphicon glyphicon-question-sign"></i> ' . __('Help'),
    array(
        'controller' => 'help',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
    </li>
    </ul>
<div id="content" class="content">
    <?php } ?>
<?php echo $this->Session->flash(); ?>
<?php echo $this->fetch('content'); ?>
<?php
if(AuthComponent::user('role') != 'tech'){
    echo '</div>';
}
?>
