<div id="confirm<?php echo $id ?>" class="modal hide fade">
    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3><?php echo __('Delete') ?></h3>
    </div>
    <div class="modal-body">
	<p><?php echo __('Are you sure?') ?></p>
    </div>
    <div class="modal-footer">
	<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">
	    <i class="icon-chevron-up"></i>
	    <?php echo __('Cancel') ?>
	</a>
	<?php echo $link ?>
    </div>
</div>
