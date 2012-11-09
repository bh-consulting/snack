<h1>Users</h1>
<p>
    <div class="btn-group">
        <button class="btn dropdown-toggle" data-toggle="dropdown">
        Add user
        <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a href="/interface/radchecks/add_cisco">Cisco</a></li>
            <li><a href="/interface/radchecks/add_loginpass">Login / Password</a></li>
            <li><a href="/interface/radchecks/add_cert">Certificate</a></li>
            <li><a href="/interface/radchecks/add_mac">MAC address</a></li>
            <li><a href="/interface/radchecks/add_csv">Upload CSV</a></li>
        </ul>
    </div>
</p>

<table class="table">
    <thead>
    <tr>
        <th>Id</th>
        <th>Username</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($radchecks as $rad): ?>
    <tr>
        <td><?php echo $rad['Radcheck']['id']; ?></td>
        <td>
            <?php echo $this->Html->link($rad['Radcheck']['username'],
            array('controller' => 'radchecks', 'action' => 'view', $rad['Radcheck']['id'])); ?>
        </td>
        <td>
            <i class="icon-edit"></i>
            <?php echo $this->Html->link('Edit', array('action' => 'edit', $rad['Radcheck']['id'])); ?>

        </td>
        <td>
            <?php echo $this->Form->postLink('Delete', array('action' => 'delete', $rad['Radcheck']['id']),
            array('confirm' => 'Are you sure?')); ?>
        </td>
    </tr>
        <?php endforeach; ?>
    <?php unset($rad); ?>
    </tbody>
</table>
