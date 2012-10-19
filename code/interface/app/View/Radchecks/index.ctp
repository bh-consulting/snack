<h1>Radchecks</h1>
<?php echo $this->Html->link('Add user',
    array('controller' => 'radchecks', 'action' => 'add'),
    array('class' => 'btn')); ?>
<table class="table">
    <thead>
    <tr>
        <th>Id</th>
        <th>Username</th>
        <th>Attribute</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    </thead>

    <!-- Here is where we loop through our $radchecks array, printing out radcheck info -->

    <tbody>
    <?php foreach ($radchecks as $rad): ?>
    <tr>
        <td><?php echo $rad['Radcheck']['id']; ?></td>
        <td>
            <?php echo $this->Html->link($rad['Radcheck']['username'],
            array('controller' => 'radchecks', 'action' => 'view', $rad['Radcheck']['id'])); ?>
        </td>
        <td><?php echo $rad['Radcheck']['attribute']; ?></td>
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