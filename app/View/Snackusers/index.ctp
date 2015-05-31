<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('snackusers_active', 'active');

$columns = array(
    'username' => array(
        'text' => __('Username'),
    ),
    'created' => array(
        'text' => __('Creation date'),
    ),
    'modified' => array(
        'text' => __('Modification date'),
    ),
    'role' => array(
        'text' => __('Role'),
        'fit' => true,
        'bold' => true,
    ),
    'action' => array(
        'id' => 'id',
        'text' => __('Action'),
        'fit' => true,
   ),
);


if(AuthComponent::user('role') != 'root'){
    unset($columns['delete']);
}
if(!in_array(AuthComponent::user('role'), array('root', 'admin'))){
    unset($columns['edit']);
}

?>

<h1><?php echo __('SNACK Users'); ?></h1>
<?php
echo $this->Html->link(
    '<i class="fa fa-user-secret"></i> ' . __('Add a SNACK user'),
    array('controller' => 'snackusers', 'action' => 'add'),
    array('escape' => false, 'class' => 'btn btn-primary')
);

if(AuthComponent::user('role') == 'root'){
    echo $this->element(
        'delete_links',
        array('action' => 'form', 'model' => 'Snackuser')
    );
}
?>
<br />
<div col="col-sm-6">
<table class="table table-hover table-bordered">
    <thead>
    <tr>
<?php
foreach ($columns as $field => $info) {
    if (isset($info['fit']) && $info['fit']) {
        echo '<th class="fit">';
    } else {
        echo '<th>';
    }

    switch ($field) {
    case 'checkbox':
        echo $this->element('MultipleAction', array('action' => 'head'));
        break;
    case 'view':
    case 'edit':
    case 'delete':
        echo h($info['text']);
        break;
    default:
        $sort = '';

        if (preg_match("#$field$#", $this->Paginator->sortKey())) {
            $sort = '<i class="'
                .  $sortIcons[$this->Paginator->sortDir()]
               . '"></i>';
        }

        echo $this->Paginator->sort(
            $field,
            $info['text'] . ' '. $sort,
            array(
                'escape' => false,
                'url' => array('page' => 1),
            )
        );
        break;
    }

    echo '</th>';
}
?>
    </tr>
    </thead>

    <tbody>
<?php
if (!empty($snackusers)) {
    foreach ($snackusers as $user) {
        echo '<tr>';

        foreach ($columns as $field=>$info) {

            if (isset($info['fit']) && $info['fit']) {
                echo '<td class="fit"';
            } else {
                echo '<td';
            }
            if (isset($info['bold']) && $info['bold']) {
                echo ' style="font-weight:bold;"';
            }
            echo '>';

            switch ($field) {
            /*case 'checkbox':
                echo $this->element(
                    'MultipleAction',
                    array(
                        'action' => 'line',
                        'name' => 'users',
                        'id' => $user['Snackuser'][$info['id']]
                    )
                );
                break;*/
            case 'action':
		        //echo '<i class="glyphicon glyphicon-eye-open"></i> ';
                if (AuthComponent::user('role') === 'admin') {
                    echo '<span class="unknown" title="'
                        . __('Not allowed!')
                        . '">'
                        . '<i class="glyphicon glyphicon-edit glyphicon-red"></i> '
                        . __('Edit') . '</span>';
                } else {
                    //echo '<i class="glyphicon glyphicon-edit"></i> ';
                    echo $this->Html->link(
                        '<i class="glyphicon glyphicon-edit" data-toggle="tooltip" data-placement="top" title='.__('Edit').'></i> ',
                        array(
                            'action' => 'edit',
                            $user['Snackuser'][$info['id']]
                        ),
                        array('escape' => false)
                    );
                }
                echo $this->element(
                    'delete_links',
                    array(
                        'model' => 'Snackuser',
                        'action' => 'link',
                        'id' => $user['Snackuser'][$info['id']],
                    )
                );

                break;
            case 'role':
                if (isset($roles[$user['Snackuser'][$field]])) {
                    echo __($roles[$user['Snackuser'][$field]]);
                } else {
                    echo __($user['Snackuser'][$field]);
                }
                break;
            default:
                echo h($user['Snackuser'][$field]);
                break;
            }

            echo '</td>';
        }
        echo '</tr>';
    }
} else {
?>
    <tr>
        <td colspan="<?php echo count($columns); ?>" style="text-align: center">
<?php
    echo __('No user found.');
?>
        </td>
    </tr>
<?php
}
?>
    </tbody>
</table>
</div>
<div col="col-sm-4">
</div>
<?php
echo $this->element('paginator_footer');
unset($snackusers);
?>