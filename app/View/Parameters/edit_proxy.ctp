<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_proxy_active', 'active');

echo $this->Form->create('Parameter', array('action' => 'edit_proxy', 'autocomplete' => 'off'));
?>

<br>

<h4><?php echo __('Proxy configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php 
echo $this->Form->input('proxy_ip', array('label' => __('Proxy IP Address')));
echo $this->Form->input('proxy_port', array('label' => __('Proxy Port')));
echo $this->Form->input('proxy_login', array('label' => __('Proxy Login')));
echo $this->Form->input('proxy_password', array('label' => __('Proxy Password'), 'type' => 'password', 'empty' => true));
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
