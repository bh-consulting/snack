<?php
if(AuthComponent::user('role') != 'tech'){
?>
<div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav nav-sidebar side-nav">           
    <li class="<?php echo $this->fetch('users_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="fa fa-user fa-2x" title="' . __('Users') . '"></i>',
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
    '<i class="fa fa-group fa-2x" title="' . __('Groups') . '"></i>',
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
echo $this->Html->link(
    '<i class="fa fa-hdd-o fa-2x" title="' . __('NAS') . '"></i>',
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
    '<i class="fa fa-key fa-2x" title="' . __('Sessions') . '"></i>',
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
    '<i class="fa fa-list fa-2x" title="' . __('Logs') . '"></i>',
    array(
        'controller' => 'loglines',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
	</li>
    <li class="<?php echo $this->fetch('reports_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="fa fa-bar-chart fa-2x" title="' . __('Reports') . '"></i>',
    array(
        'controller' => 'reports',
        'action' => 'index',
    ),
    array(
        'onclick'=>'loading()',
        'escape' => false,
    )
);
?>
    </li>
	<li class="<?php echo $this->fetch('dashboard_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="fa fa-dashboard fa-2x" title="' . __('Dashboard') . '"></i>',
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
    '<i class="fa fa-wrench fa-2x" title="' . __('Params') . '"></i>',
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
    '<i class="fa fa-question-circle fa-2x" title="' . __('Help') . '"></i>',
    array(
        'controller' => 'help',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
    </li>
    </ul>
        
<!--<div id="content" class="col-sm-9 col-sm-offset-1 col-md-10 col-md-offset-1 main content">-->
<div id="content" class="main content">
    <?php } ?>
<?php echo $this->Session->flash(); ?>
<?php echo $this->fetch('content'); ?>
<?php
if(AuthComponent::user('role') != 'tech'){
    echo '</div>';
}
?>
