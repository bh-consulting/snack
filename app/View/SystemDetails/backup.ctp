<?php
$this->extend('/Common/systemdetails_tabs');
$this->assign('systemdetails_backup_active', 'active');


$columns = array(
    'name' => array(
        'text' => __('Name'),
        'fit' => true,
    ),
    'delete' => array(
        'name' => 'name',
        'text' => __('Delete'),
        'fit' => true,
    ),
);
?>
<?php
$nextpage=$page+1;
$prevpage=$page-1;
if ($page == 1) {
    $link=$this->Html->tag(
            "li", $this->Html->link('&larr; Prev', array('page' => $prevpage), array('escape' => false)),  array('class' => 'previous disabled')
    );
    $link.=$this->Html->tag(
            "li", $this->Html->link($page." / ".$totalPages, '#'), array('class' => 'previous'));
    $link.=$this->Html->tag(
            "li", $this->Html->link('Next &rarr;', array('page' => $nextpage), array('escape' => false)), array('class' => 'next')
    );
}
elseif ($page==$totalPages) {
    $link=$this->Html->tag(
            "li", $this->Html->link('&larr; Prev', array('page' => $prevpage), array('escape' => false)),  array('class' => 'previous')
    );
    $link.=$this->Html->tag(
            "li", $this->Html->link($page." / ".$totalPages, '#'), array('class' => 'previous'));
    $link.=$this->Html->tag(
            "li", $this->Html->link('Next &rarr;', array('page' => $nextpage), array('escape' => false)), array('class' => 'next disabled')
    );
}
else {
    $link=$this->Html->tag(
            "li", $this->Html->link('&larr; Prev', array('page' => $prevpage), array('escape' => false)),  array('class' => 'previous')
    );
    $link.=$this->Html->tag(
            "li", $this->Html->link($page." / ".$totalPages, '#'), array('class' => 'previous'));
    $link.=$this->Html->tag(
            "li", $this->Html->link('Next &rarr;', array('page' => $nextpage), array('escape' => false)), array('class' => 'next')
    );
}
echo $this->Html->tag(
    'ul',
    $link,
    array('class' => 'pager', 'style' => 'float:left;')
);
?>

<br>
<table class="table">
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
                    case 'delete':
                        echo h($info['text']);
                        break;
                    case 'name':
                        echo h($info['text']);
                        break;
                }

                echo '</th>';
            }
            ?>
        </tr>
    </thead>

    <tbody>
        <?php
        if (!empty($listfiles)) {
            foreach ($listfiles as $file) {
                echo '<tr>';

                foreach ($columns as $field => $info) {
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
                        case 'name':
                            echo $this->Html->link(
                                    $file[$field], '/conf/' . h($file[$field]), array('class' => 'button', 'target' => '_blank')
                            );
                            break;
                        case 'delete':
                            echo $this->Html->link(
                                    '<i class="glyphicon glyphicon-trash glyphicon-white"></i> ', 
                                    array(
                                        'controller' => 'SystemDetails',
                                        'action' => 'delete_backup',
                                        $file['name'],
                                    ),
                                    array('escape' => false)
                            );
                            break;
                        default:
                            echo h($file[$field]);
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
                    echo __('No backup found.');
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<?php
$nextpage=$page+1;
$prevpage=$page-1;
if ($page == 1) {
    $link=$this->Html->tag(
            "li", $this->Html->link('&larr; Prev', array('page' => $prevpage), array('escape' => false)),  array('class' => 'previous disabled')
    );
    $link.=$this->Html->tag(
            "li", $this->Html->link($page." / ".$totalPages, '#'), array('class' => 'previous'));
    $link.=$this->Html->tag(
            "li", $this->Html->link('Next &rarr;', array('page' => $nextpage), array('escape' => false)), array('class' => 'next')
    );
}
elseif ($page==$totalPages) {
    $link=$this->Html->tag(
            "li", $this->Html->link('&larr; Prev', array('page' => $prevpage), array('escape' => false)),  array('class' => 'previous')
    );
    $link.=$this->Html->tag(
            "li", $this->Html->link($page." / ".$totalPages, '#'), array('class' => 'previous'));
    $link.=$this->Html->tag(
            "li", $this->Html->link('Next &rarr;', array('page' => $nextpage), array('escape' => false)), array('class' => 'next disabled')
    );
}
else {
    $link=$this->Html->tag(
            "li", $this->Html->link('&larr; Prev', array('page' => $prevpage), array('escape' => false)),  array('class' => 'previous')
    );
    $link.=$this->Html->tag(
            "li", $this->Html->link($page." / ".$totalPages, '#'), array('class' => 'previous'));
    $link.=$this->Html->tag(
            "li", $this->Html->link('Next &rarr;', array('page' => $nextpage), array('escape' => false)), array('class' => 'next')
    );
}
echo $this->Html->tag(
    'ul',
    $link,
    array('class' => 'pager', 'style' => 'float:left;')
);

unset($listfiles);
?>