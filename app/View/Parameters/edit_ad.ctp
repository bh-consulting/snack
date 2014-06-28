<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_ad_active', 'active');

echo $this->Form->create('Parameter', array('action' => 'edit_ad'));
?>
<h4><?php echo __('ActiveDirectory configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php 
    if (empty($adstatus)) {
            echo __('Not set.');
        }
        elseif(preg_match("/^Joined .*/", $adstatus, $matches)) {
            echo "<center><b><font color='green'>".$adstatus."</font></b></center><br>";
        }
        else {
            echo "<center><b><font color='red'>".$adstatus."</font></b></center><br>";
        }
    echo $this->Form->input(
            'adip', array(
        'label' => __('Active Directory IP'),
            )
    );
    echo $this->Form->input(
            'addomain', array(
        'label' => __('Domain'),
            )
    );
    echo $this->Form->input(
            'adminusername', array(
        'label' => __('Admin Username'),
            )
    );
    echo $this->Form->input(
            'adminpassword', array(
        'label' => __('Admin Password'),
            )
    );
?>
</dl>

<?php
$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
);
echo $this->Form->end($options);
?>