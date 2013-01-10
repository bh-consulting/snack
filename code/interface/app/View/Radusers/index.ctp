<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
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

<? 
$columns = array(
    'id' => 'ID',
    'username' => 'Username',
    'comment' => 'Comment',
    'ntype' => 'Type'
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
    if(!empty($radusers)){
        foreach ($radusers as $rad): ?>
        <tr>
            <td>
                <? echo $this->Html->link($rad['Raduser']['id'],
                array('controller' => 'Radusers', 'action' => 'view_' . $rad['Raduser']['type'], $rad['Raduser']['id'])); ?>
            </td>
            <td>
                <? echo $rad['Raduser']['username']; ?>
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
        <? endforeach;
    } else {
        ?>
        <tr>
            <td colspan="5">No users yet.</td>
        </tr>
    <?
    }
    unset($rad);
    ?>
    </tbody>
</table>
<? echo $this->element('paginator_footer'); ?>
