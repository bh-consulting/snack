<div class="lists-wrapper">
	<div class="pull-left list-container">
	<div class="list-title"><?php echo $leftTitle; ?></div>
		<ul id="groups-available" class="well connectedList sortList" subClass="label label-info">
		<?php
		foreach( $contents as $key => $value )
			if( !in_array( $key, $selectedContents) )
				echo '<li id="' . $key . '" class="label">' . $value . '</li>';
		?>
		</ul>
	</div>
	<div class="pull-left list-container">
	<div class="list-title"><?php echo $rightTitle; ?></div>
		<ul id="groups" class="well connectedList sortList" subClass="label label-warning">
		<?php
		foreach( $selectedContents as $value )
			echo '<li id="' . $value . '" class="label">' . $contents[$value] . '</li>';
		?>
		</ul>
	</div>
</div>
