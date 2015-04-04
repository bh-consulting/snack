<?php
$title = isset($title) ? $title : __('Import');
?>

<div id="confirm<?php echo $id ?>" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php
            $mainLabelOptions = array('class' => 'col-sm-4 control-label');
            echo $this->Form->create('importCsv', array('url' => $url, 'enctype' => 'multipart/form-data'));
            ?>
            <div class="col-sm-4"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3><?php echo $title; ?></h3>
            </div>
            <div class="modal-body">
                <p>
                    <?php
                    $myLabelOptions = array('text' => __('Please, select a file:'));
                    echo $this->Form->input('file', array('type' => 'file', 'label' => array_merge($mainLabelOptions, $myLabelOptions)));
                    ?>
                </p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">
                    <i class="glyphicon glyphicon-chevron-up"></i>
                    <?php echo __('Cancel') ?>
                </a>
                <?php
                echo $this->Form->button('<i class="glyphicon glyphicon-upload glyphicon-white"></i> ' . __('Upload'), array(
                    'escape' => false,
                    'class' => 'btn btn-primary',
                ));
                ?>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>
