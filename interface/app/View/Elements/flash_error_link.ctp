<div id="flashMessage" class="alert alert-error <?php echo isset($class) ? $class : null ?>">
    <?php
        echo $message . ' ';
        echo $this->Html->link($title, $url, $style);
    ?>
</div>
