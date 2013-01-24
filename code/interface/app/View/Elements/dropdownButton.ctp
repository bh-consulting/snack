<div class="btn-group">

<?php
$color = (isset($color)) ? $color : 'icon-white';
$icon = (isset($icon)
    && !empty($icon)) ? '<i class="' . $icon . ' ' . $color . '"></i> ' : '';
$class = (isset($class)) ? $class : 'btn-primary';

if (isset($buttonCount) && $buttonCount == 1) {
?>
    <a	class="btn <?php echo $class; ?> dropdown-toggle"
	data-toggle="dropdown" href="#">
<?php
    echo $icon . h($title);
?>
	<span class="caret"></span>
    </a>
<?php
} else {
    echo $this->Html->link(
	$icon . h( $title ),
	$linkOptions,
	array('class' => 'btn ' . $class, 'escape' => false)
    );
?>
    <a class="btn <?php echo $class; ?> dropdown-toggle" data-toggle="dropdown">
	<span class="caret"></span>
    </a>
<?php
}
?>

    <ul class="dropdown-menu">
<?php
foreach ($items as $key=>$item) {
    if (is_array($item)) {
	echo '<li class="dropdown-submenu">'
	    . '<a tabindex="-1" href="#">' . $key . '</a>'
	    . '<ul class="dropdown-menu">';

	foreach ($item as $subItem) {
	    echo '<li>' . $subItem . '</li>';
	}

	echo '</ul>';
    } else {
	echo '<li>' . $item . '</i>';
    }
}
?>
    </ul>
</div>
