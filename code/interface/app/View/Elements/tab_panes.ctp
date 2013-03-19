<?php
if(!isset($active)){
    $active = 1;
}
?>

<div class="tabbable">
    <ul class="nav nav-tabs">
        <?php
        $i = 1;
        foreach($items as $title => $content){
        ?>
            <li <?php echo ($i == $active) ? 'class="active"' : ''; ?>>
                <a href="#tab<?php echo $i; ?>" data-toggle="tab">
                    <?php echo $title; ?>
               </a>
            </li>
        <?php
            $i++;
        }
        ?>
    </ul>
    <div class="tab-content">
        <?php
        $i = 1;
        foreach($items as $title => $content){
        ?>
            <div class="tab-pane <?php echo ($i == $active) ? 'active' : ''; ?>" id="tab<?php echo $i; ?>">
                <?php echo $content; ?>
            </div>
        <?php
            $i++;
        }
        ?>
    </div>
</div>
