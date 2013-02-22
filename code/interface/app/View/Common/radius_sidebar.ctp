<?
if(AuthComponent::user('role') != 'tech'){
?>
    <ul class="nav nav-list bs-sidenav affix mainmenu">
	<li class="<?php echo $this->fetch('users_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="icon-user"></i> ' . __('Users')
    . '<i class="icon-chevron-right"></i>',
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
    '<i class="icon-list"></i> ' . __('Groups')
    . '<i class="icon-chevron-right"></i>',
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
    '<i class="icon-hdd"></i> ' . __('NAS')
    . '<i class="icon-chevron-right"></i>',
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
    '<i class="icon-ok"></i> ' . __('Sessions')
    . '<i class="icon-chevron-right"></i>',
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
    '<i class="icon-list-alt"></i> ' . __('Logs')
    . '<i class="icon-chevron-right"></i>',
    array(
        'controller' => 'loglines',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
	</li>
	<li class="<?php echo $this->fetch('dashboard_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="icon-th-large"></i> ' . __('Server dashboard')
    . '<i class="icon-chevron-right"></i>',
    array(
        'controller' => 'systemDetails',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
	</li>
	<li class="<?php echo $this->fetch('param_active'); ?>">
<?php
echo $this->Html->link(
    '<i class="icon-wrench"></i> ' . __('Server parameters')
    . '<i class="icon-chevron-right"></i>',
    array(
        'controller' => 'parameters',
        'action' => 'index',
    ),
    array('escape' => false)
);
?>
	</li>
    </ul>
    <? } ?>
<div id="content" class="content">
<?php echo $this->Session->flash(); ?>
<?php echo $this->fetch('content'); ?>
</div>
