<? 
$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');
?>
<h1>Groups</h1>
<p>
    <?php echo $this->Html->link('Add a group',
    array('controller' => 'groups', 'action' => 'add'),
    array('class' => 'btn')); ?>
</p>

<? if(!empty($groups)){ ?>
<table class="table">
    <thead>
    <tr>
        <th>Groupname</th>
        <th>Comment</th>
        <th>Type</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    </thead>

    <tbody>
    <? foreach ($groups as $g): ?>
    <tr>
        <td>
            <? echo $this->Html->link($rad['Raduser']['username'],
            array('controller' => 'Radusers', 'action' => 'view_' . $rad['Raduser']['type'], $rad['Raduser']['id'])); ?>
        </td>
        <td>
            <? echo $rad['Raduser']['comment']; ?>
        </td>
        <td>
            <? echo $rad['Raduser']['ntype']; ?>
        </td>
        <td>
            <i class="icon-edit"></i>
            <? echo $this->Html->link('Edit', array('action' => 'edit_' . $rad['Raduser']['type'], $rad['Raduser']['id'])); ?>

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
<? } ?>