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

echo $this->element('modalDelete', array(
    'id'   => $id,
    'link' => $this->Form->postLink(
    	    '<i class="icon-remove icon-white"></i> ' . __('Delete'),
    	    array('action' => 'delete', $id),
    	    array(
    		'escape' => false,
    		'class' => 'btn btn-primary btn-danger'
    	    )
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
