<h2><?php echo $title; ?></h2>
<?php
echo $this->element('dropdownButton', array(
	'buttonCount' => 2,
	'title' => h($name),
	'icon' => 'icon-user',
	'linkOptions' => array('action' => $editAction, $id),
	'items' => array(
		$this->Html->link(
			'<i class="icon-edit"></i> ' . __('Edit'),
			array('action' => $editAction, $id),
			array('escape' => false)
		),
		$this->Form->postLink(
			'<i class="icon-remove"></i> ' . __('Delete'),
			array('action' => 'delete', $id),
			array('confirm' => __('Are you sure?'), 'escape' => false)
		),
	)
));
?>

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
