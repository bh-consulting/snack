<div class="col-md-offset-1 col-sm-3">
    <?php
    //debug($result);
    if ($result==1) {
        echo '<div class="panel panel-success">';
    }
    if ($result==0) {
        echo '<div class="panel panel-danger">';
    }
    if ($result==-1) {
        echo '<div class="panel panel-warning">';
    }
    ?>
        <div class="panel-heading">
            <h3 id="panel-title" class="panel-title">
            <?php 
            if ($result==1) {
                echo 'Success';
            }
            if ($result==0) {
                echo 'Error';
            }
            if ($result==-1) {
                echo 'NA';
            }
            ?>
            </h3>
        </div>
        <div class="panel-body">
            <dl class="dl-horizontal">
                <dt>Username</dt>
                <dd><?php echo $username; ?></dd>
                <dt>Type</dt>
                <dd>
                <?php
                if ($type == "windowsad") {
                    echo $this->Html->image('user_password.png', array('alt' => __('Login/Pwd'), 'title' => __('Login/Pwd')));
                }
                if ($type == "phone") {
                    echo $this->Html->image('phone.png', array('alt' => __('Phone'), 'title' => __('Phone')));
                }
                if ($type == "loginpass") {
                    echo $this->Html->image('user_password.png', array('alt' => __('Login/Pwd'), 'title' => __('Login/Pwd')));
                }
                if ($type == "cert") {
                    echo $this->Html->image('certificate.png', array('alt' => __('Certificate'), 'title' => __('Certificate')));
                }
                if ($type == "mac") {
                    echo $this->Html->image('mac.png', array('alt' => __('MAC'), 'title' => __('MAC')));
                }
                if ($type == "cisco") {
                    echo $this->Html->image('cisco.png', array('alt' => __('Cisco'), 'title' => __('Cisco')));
                }
                ?>
                </dd>
                <dt>AuthType</dt>
                <dd><?php echo $authtype; ?></dd>
            </dl>
        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="toggleBlock" onclick="toggleBlock(this)">
        <?php echo $this->Html->link(__('Show Logs'), '#') ?>
        <i class="glyphicon glyphicon-chevron-down"></i>
    </div>


    <div style="display:none">
        <pre class="well"><?php echo trim($log) ?></pre>
    </div>

    <?php
    unset($username);
    unset($password);

    unset($log);

    ?>
</div>