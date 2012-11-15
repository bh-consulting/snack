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
        <th>Type</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    </thead>

    <tbody>
    <? foreach ($radusers as $rad): ?>
    <tr>
        <td>
            <? echo $this->Html->link($rad['Raduser']['username'],
            array('controller' => 'Radusers', 'action' => 'view', $rad['Raduser']['id'])); ?>
        </td>
        <td>
            <? echo $rad['Raduser']['comment']; ?>
        </td>
        <td>
            <? echo $rad['Raduser']['type']; ?>
        </td>
        <td>
            <i class="icon-edit"></i>
            <? echo $this->Html->link('Edit', array('action' => 'edit', $rad['Raduser']['id'])); ?>

        </td>
        <td>
            <i class="icon-remove"></i>
            <? echo $this->Form->postLink('Delete', array('action' => 'delete', $rad['Raduser']['id']),
            array('confirm' => 'Are you sure?')); ?>
        </td>
    </tr>
        <? endforeach; ?>
    <? unset($rad); ?>
    </tbody>
</table>
