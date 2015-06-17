<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');
?>

<h1><? echo __('Radusers'); ?></h1>
<div class="tabbable">
    <ul class="nav nav-tabs">
            <li class="<?php echo $this->fetch('radusers_users_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Users'),
                    array('controller' => 'radusers', 'action' => 'index')
                );
                ?>
            </li>
            <li class="<?php echo $this->fetch('radusers_revokcerts_active'); ?>">
                <?php
                echo $this->Html->link(
                    __('Revoked certificates'),
                    array('controller' => 'radusers', 'action' => 'revoked_certs')
                );
                ?>
            </li>
    </ul>
    <div class="tab-content">
        <?php echo $this->fetch('content'); ?>
    </div>
</div>
