<?php
$this->extend('/Common/systemdetails_tabs');
$this->assign('systemdetails_tests_active', 'active');

$columns = array(
    'name' => array(
        'text' => __('Name'),
        'fit' => true,
    ),
    'comment' => array(
        'text' => __('Comment'),
        'fit' => true,
    ),
    'results' => array(
        'text' => __('Result'),
        'fit' => true,
    )
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
                    default:
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
        if (!empty($results)) {
            foreach ($results as $key => $result) {
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
                                    $key, '#testslogs', array('class' => 'button testslog',
                                'id' => $key)
                            );
                            break;
                        case 'comment':
                            echo $result['comment'];
                            break;
                        case 'results':
                            //debug($result['res']);
                            if (preg_match('/Received Access-Accept packet/', $result['res'], $matches)) {
                                echo '<i class="glyphicon glyphicon-ok glyphicon-white"></i> ';
                            } elseif (preg_match('/Received Access-Reject packet/', $result['res'], $matches)) {
                                echo '<i class="glyphicon glyphicon-remove glyphicon-white"></i> ';
                            } elseif (preg_match('/SUCCESS/', $result['res'], $matches)) {
                                echo '<i class="glyphicon glyphicon-ok glyphicon-white"></i> ';
                            } elseif (preg_match('/FAILURE/', $result['res'], $matches)) {
                                echo '<i class="glyphicon glyphicon-remove glyphicon-white"></i> ';
                            }
                            else {
                                echo 'NA';
                            }
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
            echo __('No Test found.');
            ?>
                </td>
            </tr>
                    <?php
                }
                ?>
    </tbody>
</table>
<?php
unset($results);
?>
<div class="testslogs" id="testslogs"></div>