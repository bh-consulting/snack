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
<? if(!empty($nas)){ ?>
<table class="table">
    <thead>
    <tr>
        <th>Nas name</th>
        <th>Short name</th>
        <th>Type</th>
        <th>Ports</th>
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
        <td>
            <?php echo $this->Html->link($n['Nas']['nasname'],
            array('controller' => 'nas', 'action' => 'view', $n['Nas']['id'])); ?>
        </td>
        <td><?php echo $n['Nas']['shortname']; ?></td>
        <td><?php echo $n['Nas']['type']; ?></td>
        <td><?php echo $n['Nas']['ports']; ?></td>
        <td><?php echo $n['Nas']['server']; ?></td>
        <td><?php echo $n['Nas']['community']; ?></td>
        <td><?php echo $n['Nas']['description']; ?></td>
        <td>
            <i class="icon-edit"></i>
            <?php echo $this->Html->link('Edit', array('action' => 'edit', $n['Nas']['id'])); ?>
        </td>
        <td>
						<i class="icon-trash"></i>
            <?php echo $this->Form->postLink('Delete', array('action' => 'delete', $n['Nas']['id']),
            array('confirm' => 'Are you sure?')); ?>
        </td>
    </tr>
        <?php endforeach; ?>
    <?php unset($n); ?>
    </tbody>
</table>
<? } else { ?>
<p>You don't have any NAS yet!</p>
<? } ?>
