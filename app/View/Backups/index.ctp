<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

$columns = array(
    'id' => array(
        'text' => __('ID'),
        'fit' => true,
        'bold' => true,
    ),
    'sync' => array(
        'text' => '<i class="glyphicon glyphicon-ok-circle" title="'
        . __('Write memory') . '"></i>',
        'fit' => true,
        'id' => 'id',
    ),
    'compare' => array(
        'text' => '<i class="glyphicon glyphicon-zoom-in"></i>',
        'fit' => true,
    ),
    'datetime' => array(
        'text' => __('When'),
    ),
    'action' => array(
        'text' => __('Why'),
        'bold' => true,
    ),
    'users' => array(
        'text' => __('Who'),
    ),
    'view' => array(
        'text' => __('View'),
        'fit' => true,
        'id' => 'id',
    ),
);
?>

<h1><?php echo __('Backups of %s (%s)', $nasShortname, $nasIP); ?></h1>

<?php
echo $this->element('filters_panel', array(
    'controller' => 'backups/index/' . $nasID,
    'inputs' => array(
        array(
            'name' => 'datefrom',
            'label' => __('From'),
            'type' => 'datetimepicker',
            'options' => array('id' => 'datefrom')
        ),
        array(
            'name' => 'dateto',
            'label' => __('To'),
            'type' => 'datetimepicker',
            'options' => array('id' => 'dateto')
        ),
        array(
            'name' => 'author',
            'label' => __('Author contains (accept regex)'),
            'options' => array('id' => 'author'),
            'autoComplete' => true,
        ),
        array(
            'name' => 'action',
            'label' => __('Action'),
            'multiple' => 'checkbox',
            'type' => 'checkgroup',
        ),
        array(
            'name' => 'writemem',
            'label' => __('Synchronization'),
            'multiple' => 'checkbox',
            'type' => 'checkgroup',
            'escape' => false,
        ),
    )
));

echo $this->Form->create('SelectDiff', array(
	'url' => array(
		'controller' => 'backups',
		'action' => 'diff',
	),
	'type' => 'get',
	'class' => 'form-inline',
));
?>

<table class="table tableBackups">
	<thead>
	    <tr>
<?php
foreach ($columns as $field => $info) {
    $colspan = ($field == 'compare') ? 2 :1;

    if (isset($info['fit']) && $info['fit']) {
        echo '<th class="fit" colspan="' . $colspan . '">';
    } else {
        echo '<th colspan="' . $colspan . '">';
    }

    switch ($field) {
    case 'view':
        echo h($info['text']);
        break;
    case 'sync':
    case 'compare':
        echo $info['text'];
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
            array('escape' => false)
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
if (!empty($backups)) {
    $n = 0;
    for($i = 0; $i < count($backups); $i++) {
        $backup = $backups[$i];
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
            case 'view':
		        echo '<i class="glyphicon glyphicon-eye-open"></i> ';
                echo $this->Html->link(
                    __('View'),
                    array(
                        'action' => 'view',
                        'controller' => 'backups',
                        $backup['Backup'][$info['id']],
                        $nasID,
                    )
                );
                break;
            case 'datetime':
                echo $backup['Backup'][$field];
                break;
            case 'users':
                echo $this->element('formatUsersList', array(
                    'users' => $users[$backup['Backup']['id']]
                ));
                break;
            case 'sync':
                if(in_array($backup['Backup'][$info['id']], $unwrittenids)) {
                    echo '<i class="glyphicon glyphicon-exclamation-sign glyphicon glyphicon-red" title="'
                        . __('Configuration NOT loaded if the device restart.')
                        . '"></i>';
                } else {
                    echo '<i class="glyphicon glyphicon-ok-sign glyphicon glyphicon-green" title="'
                        . __('Seems saved as starting configuration.')
                        . '"></i>';
                }
                break;
            case 'compare':
                if ($i != 0) {
                    echo $this->Form->radio(
                        'a',
                        array($backup['Backup']['id'] => ''),
                        array(
                            'hiddenField' => false,
                            'checked' => $i == 1,
                            'set' => $n
                        )
                    );
                }

                echo '</td>';

                if (isset($info['fit']) && $info['fit']) {
                    echo '<td class="fit">';
                } else {
                    echo '<td>';
                }

                if ($i != count($backups)-1) {
                    echo $this->Form->radio(
                        'b',
                        array($backup['Backup']['id'] => ''),
                        array(
                            'hiddenField' => false,
                            'checked' => $i == 0,
                            'set' => $n
                        )
                    );
                }
                break;
            case 'action':
                if (isset($actions[$backup['Backup'][$field]])) {
                    echo __($actions[$backup['Backup'][$field]]);
                } else {
                    echo $backup['Backup'][$field];
                }
                break;
            default:
                echo h($backup['Backup'][$field]);
                break;
            }

            echo '</td>';
        }
        echo '</tr>';
        ++$n;
    }
} else {
?>
    <tr>
        <td colspan="<?php echo count($columns); ?>" style="text-align: center">
<?php
    echo __('No backup found (');

    if (count($this->params['url']) > 0) {
        echo $this->Html->link(__('retry with no filters'), '.');
    } else {
        echo __('no filters');
    }

    echo ').';
?>
        </td>
    </tr>
<?php
}
?>
	</tbody>
</table>

<?php
echo $this->Form->button(
    '<i class="glyphicon glyphicon-zoom-in glyphicon glyphicon-white"></i> ' . __('Compare'),
    array(
	'type' => 'submit',
	'escape' => false,
	'class' => 'btn btn-primary',
    )
);

echo $this->Form->hidden('nas',
    array('value' => $nasID)
);

echo $this->Form->end();

echo $this->element('paginator_footer');
?>
