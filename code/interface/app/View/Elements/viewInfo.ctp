<h2><?php echo $title; ?></h2>
<?php
echo $this->element('dropdownButton', array(
	'buttonCount' => 2,
	'title' => h($name),
	'icon' => $icon,
	'linkOptions' => array('action' => $editAction, $id),
	'items' => array(
		$this->Html->link(
			'<i class="icon-edit"></i> ' . __('Edit'),
			array('action' => $editAction, $id),
			array('escape' => false)
		),
		$this->Html->link(
			'<i class="icon-remove"></i> ' . __('Delete'),
			"#confirm$id",
			array(
			    'escape' => false,
			    'data-toggle' => 'modal'
			)
		)
	)
));
?>

<div id="confirm<?php echo $id ?>" class="modal hide fade">
    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3><?php echo __('Delete') ?></h3>
    </div>
    <div class="modal-body">
	<p><?php echo __('Are you sure?') ?></p>
    </div>
    <div class="modal-footer">
	<a href="#" class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel') ?></a>
	<?php echo $this->Form->postLink(
			__('Delete'),
			array('action' => 'delete', $id),
			array(
			    'escape' => false,
			    'class' => 'btn btn-primary'
			)
		) ?>
    </div>
</div>

<dl class="well dl-horizontal">
	<?php
	foreach($attributes as $attr => $value) {
		if( in_array( $attr, $showedAttr ) ) {
			if( is_array( $value ) ) {
				echo '<dt>' . __($attr) . '</dt><dd>';
				if( empty( $value ) ) {
					echo __('Not defined');
				} else {
					foreach($value as $item) {
						echo '<span class="label label-info">' . $item . '</span> ';
					}
				}
				echo '</dd>';
			} else {
				echo '<dt>' . __($attr) . '</dt>'
					. '<dd>' . ( ( !empty($value) ) ? $value : __('Not defined') ) . '</dd>';
			}
		}
	}
	?>
	</dd>
</dl>
