<? 
$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');
?>
<h1>Groups</h1>
<p>
    <?php echo $this->Html->link('Add a group',
    array('controller' => 'radgroups', 'action' => 'add'),
    array('class' => 'btn')); ?>
</p>

<? if(!empty($radgroups)){ ?>
<table class="table">
    <thead>
    <tr>
        <th>Groupname</th>
        <th>Comment</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    </thead>

    <tbody>
    <? foreach ($radgroups as $g): ?>
    <tr>
        <td>
            <? echo $this->Html->link($g['Radgroup']['groupname'],
            array('controller' => 'Radgroups', 'action' => 'view', $g['Radgroup']['id'])); ?>
        </td>
        <td>
            <? echo $g['Radgroup']['comment']; ?>
        </td>
        <td>
            <i class="icon-edit"></i>
            <? echo $this->Html->link('Edit', array('action' => 'edit', $g['Radgroup']['id'])); ?>

        </td>
        <td>
            <i class="icon-remove"></i>
            <? echo $this->Form->postLink('Delete', array('action' => 'delete', $g['Radgroup']['id']),
            array('confirm' => 'Are you sure?')); ?>
        </td>
    </tr>
        <? endforeach; ?>
    <? unset($g); ?>
    </tbody>
</table>
<? } ?>