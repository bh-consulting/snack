<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_logs_active', 'active');

$mainLabelOptions = array('class' => 'col-sm-4 control-label');
echo $this->Form->create('Parameter', array(
    'action' => 'edit_logs',
    'novalidate' => true, 
    'autocomplete' => 'off',
    'class' => 'form-horizontal',
    'inputDefaults' => array(
        'div' => 'form-group',
        'label' => array(
            'class' => $mainLabelOptions
        ),
        'between' => '<div class="col-sm-4 input-group">',
        'after'   => '</div>',
        'class' => 'form-control'
    ),
));
?>

<h4><?php echo __('Logs configuration:'); ?></h4>
<dl class="well dl-horizontal">
<?php 
echo $this->Form->input('logs_archive_date', array(
    'options' => $archive_dates,
    //'disabled' => true,
    'empty' => false,
    'selected' => array_search($archive_date, $archive_dates),
    'label' => __('Archive older than (days)'),
    ));

echo $this->Form->input('logs_delete_date', array(
    'options' => $delete_dates,
    //'disabled' => true,
    'empty' => false,
    'selected' => array_search($delete_date, $delete_dates),
    'label' => __('Delete older than (days)'),
    ));
?>
</dl>

<?php
$options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'class' => 'btn btn-primary',
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
);
echo $this->Form->end($options);
?>
