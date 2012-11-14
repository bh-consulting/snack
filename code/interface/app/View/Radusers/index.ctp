<h1>Users</h1>
<p>
    <div class="btn-group">
        <button class="btn dropdown-toggle" data-toggle="dropdown">
        Add user
        <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a href="/interface/radusers/add_cisco">Cisco</a></li>
            <li><a href="/interface/radusers/add_loginpass">Login / Password</a></li>
            <li><a href="/interface/radusers/add_cert">Certificate</a></li>
            <li><a href="/interface/radusers/add_mac">MAC address</a></li>
            <li><a href="/interface/radusers/add_csv">Upload CSV</a></li>
        </ul>
    </div>
</p>

<table class="table">
    <thead>
    <tr>
        <th>Username</th>
        <th>Comment</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($radusers as $rad): ?>
    <tr>
        <td>
            <?php echo $this->Html->link($rad['Raduser']['username'],
            array('controller' => 'Radusers', 'action' => 'view', $rad['Raduser']['username'])); ?>
        </td>
        <td>
            <?php echo $rad['Raduser']['comment']; ?>
        </td>
        <td>
            <i class="icon-edit"></i>
            <?php echo $this->Html->link('Edit', array('action' => 'edit', $rad['Raduser']['username'])); ?>

        </td>
        <td>
            <?php echo $this->Form->postLink('Delete', array('action' => 'delete', $rad['Raduser']['username']),
            array('confirm' => 'Are you sure?')); ?>
        </td>
    </tr>
        <?php endforeach; ?>
    <?php unset($rad); ?>
    </tbody>
</table>
