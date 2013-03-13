<?php
switch ($action) {
case 'form':
    echo $this->Form->create($model, array('action' => 'delete'))
        . $this->Form->hidden('id')
        . $this->Form->end();
    break;
case 'link':
?>

<a href="#confirm<?php echo $id ?>" data-toggle="modal"><?php echo __('Delete') ?></a>

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
	    <i class="icon-chevron-up"></i> <?php echo __('Cancel') ?>
	</a>
	<a href="#" class="btn btn-primary btn-danger" onclick="$('#<?php echo $model ?>DeleteForm input').val('<?php echo $id ?>'); $('#<?php echo $model ?>DeleteForm').submit()">
	    <i class="icon-remove icon-white"></i> <?php echo __('Delete') ?>
	</a>
    </div>
</div>

<?php
    break;
}
?>
