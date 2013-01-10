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
<? 
$columns = array(
    'id' => __('ID'),
    'nasname' => __('Name'),
    'shortname' => __('Short name'),
    'type' => __('Type'),
    'ports' => __('Ports'),
    'server' => __('Server'),
    'community' => __('Community'),
    'description' => __('Description')
);
?>

<table class="table">
    <thead>
    <tr>
        <?
        foreach($columns as $field => $text){
            $sort = preg_match("#$field$#", $this->Paginator->sortKey()) ?  $this->Html->tag('i', '', array('class' => $sortIcons[$this->Paginator->sortDir()])) : '';

            echo "<th>";
            echo $this->Paginator->sort($field, "$text $sort", array('escape' => false));
            echo "</th>";
        }
        ?>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    </thead>

    <tbody>
<?
if(!empty($nas)){
    foreach ($nas as $n): ?>
    <tr>
        <td>
            <?php echo $this->Html->link($n['Nas']['id'],
            array('controller' => 'nas', 'action' => 'view', $n['Nas']['id'])); ?>
        </td>
        <td><?php echo $n['Nas']['nasname']; ?></td>
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
        <?php endforeach;
    } else {
        ?>
        <tr>
            <td colspan="8">No NAS yet.</td>
        </tr>
    <?
    }
    unset($n);
    ?>
    </tbody>
</table>
<? echo $this->element('paginator_footer');