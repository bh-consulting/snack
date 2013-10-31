<div id="flashMessage" class="alert alert-success <?php echo isset($class) ? $class : null ?>">
    <?php
        echo $message . ' ';
        echo $this->Html->link($title, $url, $style);
    ?>
</div>
