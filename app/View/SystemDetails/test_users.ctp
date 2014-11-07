<?php
$columns = array(
    'name' => array(
        'text' => __('Name'),
        'fit' => true,
    ),
    'comment' => array(
        'text' => __('Comment'),
        'fit' => true,
    ),
    'type' => array(
        'text' => __('Type'),
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
                            if (isset($password)) {
                                echo $this->Html->link(
                                    $key,
                                    '#',
                                    array(
                                        'class' => 'button',
                                        'onclick' => 'testslogsAD("'.$key.'","'.$password.'");',
                                ));
                                /*echo $this->Html->link(
                                        $key, '#testslogs', array('class' => 'button testslog',
                                    'id' => $key,
                                    'pwd' => $password)
                                );*/
                            } else {
                                echo $this->Html->link(
                                    $key,
                                    '#',
                                    array(
                                        'class' => 'button',
                                        'onclick' => 'testslogs("'.$key.'");',
                                ));
                            }
                            break;
                        case 'comment':
                            echo $result['comment'];
                            break;
                        case 'type':
                            foreach ($usernames as $username) {
                                if ($username['raduser']['username'] == $key) {
                                    if ($username['raduser']['is_windowsad']) {
                                        echo $this->Html->image('windows.png', array('alt' => __('Login/Pwd by ActiveDirectory'), 'title' => __('Login/Pwd by ActiveDirectory')));
                                    }
                                    if ($username['raduser']['is_phone']) {
                                        echo $this->Html->image('phone.png', array('alt' => __('Phone'), 'title' => __('Phone')));
                                    }
                                    if ($username['raduser']['is_loginpass']) {
                                        echo $this->Html->image('user_password.png', array('alt' => __('Login/Pwd by ActiveDirectory'), 'title' => __('Login/Pwd by ActiveDirectory')));
                                    }
                                    if ($username['raduser']['is_cert']) {
                                        echo $this->Html->image('certificate.png', array('alt' => __('Certificate'), 'title' => __('Certificate')));
                                    }
                                    if ($username['raduser']['is_mac']) {
                                        echo $this->Html->image('mac.png', array('alt' => __('MAC'), 'title' => __('MAC')));
                                    }
                                    if ($username['raduser']['is_cisco']) {
                                        echo $this->Html->image('cisco.png', array('alt' => __('Cisco'), 'title' => __('Cisco')));
                                    }
                                }
                            }
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