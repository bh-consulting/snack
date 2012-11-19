<? 
$this->extend('/Common/radius_sidebar'); 
$this->assign('nas_active', 'active');
?>

<h1>NAS</h1>
<p>
    <?php echo $this->Html->link('Add a NAS',
    array('controller' => 'nas', 'action' => 'add'),
    array('class' => 'btn')); ?>
</p>
<table class="table">
    <thead>
    <tr>
        <th>Id</th>
        <th>Nas name</th>
        <th>Short name</th>
        <th>Type</th>
        <th>Ports</th>
        <th>Secret</th>
        <th>Server</th>
        <th>Community</th>
        <th>Description</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($nas as $n): ?>
    <tr>
        <td><?php echo $n['Nas']['id']; ?></td>
        <td>
            <?php echo $this->Html->link($rad['Nas']['nasname'],
            array('controller' => 'nas', 'action' => 'view', $rad['Nas']['id'])); ?>
        </td>
        <td><?php echo $rad['Nas']['shortname']; ?></td>
        <td><?php echo $rad['Nas']['type']; ?></td>
        <td><?php echo $rad['Nas']['ports']; ?></td>
        <td><?php echo $rad['Nas']['secret']; ?></td>
        <td><?php echo $rad['Nas']['server']; ?></td>
        <td><?php echo $rad['Nas']['community']; ?></td>
        <td><?php echo $rad['Nas']['description']; ?></td>
        <td>
            <i class="icon-edit"></i>
            <?php echo $this->Html->link('Edit', array('action' => 'edit', $rad['nas']['id'])); ?>
        </td>
        <td>
            <?php echo $this->Form->postLink('Delete', array('action' => 'delete', $rad['Nas']['id']),
            array('confirm' => 'Are you sure?')); ?>
        </td>
    </tr>
        <?php endforeach; ?>
    <?php unset($rad); ?>
    </tbody>
</table>
