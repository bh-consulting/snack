<div class="span3">
    <div class="sidebar-nav well">
        <ul class="nav nav-list">
            <li class="<? echo $this->fetch('users_active'); ?>">
                <a href="/interface/radusers/"><i class="icon-user"></i> Users</a>
            </li>
            <li class="<? echo $this->fetch('groups_active'); ?>">
                <a href="/interface/radgroups/"><i class="icon-list"></i> Groups</a>
            </li>
            <li class="<? echo $this->fetch('nas_active'); ?>">
                <a href="/interface/nas/"><i class="icon-hdd"></i> NAS</a>
            </li>
            <li class="<? echo $this->fetch('monitoring_active'); ?>">
                <a href="/interface/radaccts/"><i class="icon-ok"></i> Monitoring</a>
            </li>
            <li class="<? echo $this->fetch('logs_active'); ?>">
                <a href="/interface/loglines"><i class="icon-list-alt"></i> Logs</a>
            </li>
            <li class="<? echo $this->fetch('dashboard_active'); ?>">
                <a href="/interface/systemDetails/"><i class="icon-th-large"></i> Server dashboard</a>
            </li>
            <li class="<? echo $this->fetch('param_active'); ?>">
                <a href="#"><i class="icon-wrench"></i> Server parameters</a>
            </li>
        </ul>
    </div>
</div>
<div class="span9">
    <div id="content" class="content">

        <?php echo $this->Session->flash(); ?>

        <?php echo $this->fetch('content'); ?>
    </div>
</div>
