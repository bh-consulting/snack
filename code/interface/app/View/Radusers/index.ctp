<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo $this->Form->create('MultiSelection', array('class' => 'form-inline'));
$actionButton = $this->Html->div('btn-group',
	$this->Form->button('Action <span class="caret"></span>', array('escape' => false, 'class' => 'btn btn-primary dropdown-toggle', 'data-toggle' => 'dropdown')) .
	$this->Html->nestedList(array(
		$this->Html->link('Delete', '#', array('onClick' => "$('#selectionAction').attr('value', 'delete');$('#MultiSelectionIndexForm').submit();")),
		$this->Html->link('Export', '#', array('onClick' => "$('#selectionAction').attr('value', 'export');$('#MultiSelectionIndexForm').submit();"))
		), array('class' => 'dropdown-menu', 'escape' => false))
);

?>

<h1><? echo __('Users'); ?></h1>
<p>
    <?php echo $actionButton; ?>
    <div class="btn-group">
        <button class="btn btn-info dropdown-toggle" data-toggle="dropdown">
        <? echo __('Add user'); ?>
        <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a href="/interface/radusers/add_cisco"><? echo __('Cisco'); ?></a></li>
            <li><a href="/interface/radusers/add_loginpass"><? echo __('Login / Password'); ?></a></li>
            <li><a href="/interface/radusers/add_cert"><? echo __('Certificate'); ?></a></li>
            <li><a href="/interface/radusers/add_mac"><? echo __('MAC address'); ?></a></li>
            <li><a href="/interface/radusers/add_csv"><? echo __('Upload CSV'); ?></a></li>
        </ul>
    </div>
</p>

<? 
$columns = array(
    'id' => __('ID'),
    'username' => __('Username'),
    'comment' => __('Comment'),
    'ntype' => __('Type')
);

?>

<table class="table">
    <thead>
    <tr>
	<th width="10px"><?php echo $this->Form->select('All', array('all' => ''), array('class' => 'checkbox rangeAll', 'multiple' => 'checkbox', 'hiddenField' => false)); ?></th>
        <?
        foreach($columns as $field => $text){
            $sort = preg_match("#$field$#", $this->Paginator->sortKey()) ?  $this->Html->tag('i', '', array('class' => $sortIcons[$this->Paginator->sortDir()])) : '';

            echo "<th>";
            echo $this->Paginator->sort($field, "$text $sort", array('escape' => false));
            echo "</th>";
        }
        ?>
        <th><? echo __('Edit'); ?></th>
        <th><? echo __('Delete'); ?></th>
    </tr>
    </thead>

    <tbody>
    <?
    echo $this->Form->postLink(''); //TODO sinon le premier lien supprimer ne focntionne pas... (depuis ajout des checkbox)

    if(!empty($radusers)){
        foreach ($radusers as $rad): ?>
        <tr>
	    <td><?php echo $this->Form->select('users', array($rad['Raduser']['id'] => ''), array('class' => 'checkbox range', 'multiple' => 'checkbox', 'hiddenField' => false)); ?></td>

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
                <? echo $this->Html->link(__('Edit'), array('action' => 'edit_' . $rad['Raduser']['type'], $rad['Raduser']['id'])); ?>

            </td>
            <td>
		<i class="icon-remove"></i>
                <? echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $rad['Raduser']['id']), array('confirm' => __('Are you sure?'))); ?>
            </td>
        </tr>
        <? endforeach;
    } else {
        ?>
        <tr>
            <td colspan="6"><? echo __('No users yet'); ?>.</td>
        </tr>
    <?
    }
    unset($rad);
    ?>
    </tbody>
</table>
<?
echo $actionButton;
echo $this->Form->end(array('id' => 'selectionAction', 'name' => 'action', 'type' => 'hidden'));
echo $this->element('paginator_footer');
?>
