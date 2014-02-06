<?php
$title = isset($title) ? $title : __('Delete');
?>

<div id="confirm<?php echo $id ?>" class="modal  fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3><?php echo $title; ?></h3>
            </div>
            <div class="modal-body">
                <p><?php echo __('Are you sure?') ?></p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">
                    <i class="glyphicon glyphicon-chevron-up"></i>
                    <?php echo __('Cancel') ?>
                </a>
                <?php echo $link ?>
            </div>
        </div>
    </div>
</div>