<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_ad_active', 'active');

echo $this->Form->create('Parameter', array('action' => 'edit_ad_group'));
?>
<h4><?php echo __('ActiveDirectory Group configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php 
echo $this->Form->input('adgroupsync', array(
    'options' => $adgroups,
    //'disabled' => true,
    'empty' => false,
    'selected' => array_search($adgroup, $adgroups),
    'label' => __('Group Sync'),
    ));
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