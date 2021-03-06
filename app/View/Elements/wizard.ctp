<div id="rootwizard">
    <ul>
        <?php
        $i = 0;
        foreach($steps as $title => $content){
            $arrow = ($i != count($steps) - 1) ? ' <i class="glyphicon glyphicon-chevron-right"></i>' : '';
            echo '<li><a href="#wtab' . $i . '" data-toggle="tab" class="wtab">' . $title . $arrow . '</a></li>';
            $i++;
        }
        ?>

    </ul>

    <div class="tab-content">
        <?php
        $i = 0;
        foreach ($steps as $title => $content) {
            echo '<div class="tab-pane" id="wtab' . $i . '">';
            echo $content;
            echo '</div>';
            $i++;
        }
        ?>
        <div id="bar" class="progress progress-striped active">
            <div class="bar"></div>
        </div>

        <ul class="pager wizard">
            <li class="previous"><a href="#">&laquo; <?php echo __('Previous'); ?></a></li>
            <li class="next"><a href="#"><?php echo __('Next'); ?> &raquo;</i></a></li>

            <?php echo $finishButton; ?>
        </ul>
    </div>  
</div>
