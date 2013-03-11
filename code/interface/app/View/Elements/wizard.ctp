<div id="rootwizard">
    <ul>
        <?php
        $i = 0;
        foreach($steps as $title => $content){
            echo '<li><a href="#tab' . $i . '" data-toggle="tab">' . $title . '</a></li>';
            $i++;
        }
        ?>

    </ul>

    <div class="tab-content">
        <?php
        $i = 0;
        foreach ($steps as $title => $content) {
            echo '<div class="tab-pane" id="tab' . $i . '">';
            echo $content;
            echo '</div>';
            $i++;
        }
        ?>
        <div id="bar" class="progress progress-striped active">
            <div class="bar"></div>
        </div>

        <ul class="pager wizard">
            <li class="previous"><a href="#"><i class="icon-chevron-left"></i><?php echo __('Previous'); ?></a></li>
            <li class="next"><a href="#"><?php echo __('Next'); ?><i class="icon-chevron-right"></i></a></li>

            <?php echo $finishButton; ?>
        </ul>
    </div>  
</div>