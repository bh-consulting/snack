<h4><?php echo $title; ?></h4>
<dl class="well dl-horizontal">
<?php
foreach ($fields as $text => $value) {
    echo '<dt>';
    if (!empty($text)) {
        echo $text;
    } else {
        echo __('Unknown');
    }
    echo '</dt><dd>';
    if (!empty($value)) {
        echo $value;
    } else {
        echo __('Unknown');
    }
}
?>
</dl>
