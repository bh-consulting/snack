<h2><?php echo __( $title ); ?></h2>
<div class="btn-group">
	<? echo $this->Html->link('<i class="' . $icon . ' icon-white"></i> ' . h( $name ), array('action' => $editAction, $id), array('class' => 'btn btn-primary', 'escape' => false)); ?>
	<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
	<ul class="dropdown-menu">
		<li><? echo $this->Html->link('<i class="icon-edit"></i> ' . __('Edit'), array('action' => $editAction, $id), array('escape' => false)); ?></li>
                <li><? echo $this->Form->postLink('<i class="icon-remove"></i> ' . __('Delete'), array('action' => 'delete', $id), array('confirm' => __('Are you sure?'), 'escape' => false)); ?>
	</ul>
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
				echo '<dt>' . __($attr) . '</dt><dd>' . ( ( !empty($value) ) ? $value : __('Not defined') ) . '</dd>';
			}
		}
	}
	?>
	</dd>
</dl>
