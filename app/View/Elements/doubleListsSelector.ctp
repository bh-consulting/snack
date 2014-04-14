<?php
if (!empty($contents)) {
?>
<div class="lists-wrapper">
	<div class="pull-left list-container">
	<div class="list-title"><?php echo $leftTitle; ?></div>
		<ul id="left" class="well connectedList sortList" subClass="label label-info">
		<?php
		foreach( $contents as $key => $value )
			if( !in_array( $value, $selectedContents) ) {
                if (isset($comments)) {
                    echo '<li id="' . $key . '" class="label">' . $value . ' ' .$comments[$key] . '</li>';
                }
                else {
                    echo '<li id="' . $key . '" class="label">' . $value . '</li>';
                }
            }
				
		?>
		</ul>
	</div>
	<div class="pull-left list-container">
	<div class="list-title"><?php echo $rightTitle; ?></div>
		<ul id="right" class="well connectedList sortList" subClass="label label-warning">
		<?php
		foreach( $selectedContents as $value )
                echo '<li id="' . array_search( $value, $contents ) . '" class="label">' . $value . '</li>';
		?>
		</ul>
	</div>
</div>
<?php
} 
?>
