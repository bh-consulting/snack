<?php
$this->extend('/Common/parameters_tabs');
$this->assign('param_cron_active', 'active');
?>

<h4><?php echo $script; ?></h4>

<?php
echo $this->Form->create('Parameter', array('action' => 'edit_cron/'.$script), 'autocomplete' => 'off');

//debug($listcron);
if (preg_match("/^(#*)(\*\/\d+|\d+|\*)\s+(\*\/\d+|\d+|\*)\s+(\*\/\d+|\d+|\*)\s+(\*\/\d+|\d+|\*)\s+(\*\/\d+|\d+|\*)\s+(www-data|root)\s+(.*)$/", $listcron, $matches)) {
    $i = 1;
    $disabled = $matches[$i];
    $i++;
    $min = $matches[$i];
    $i++;
    $hour = $matches[$i];
    $i++;
    $day = $matches[$i];
    $i++;
    $month = $matches[$i];
    $i++;
    $dayofmonth = $matches[$i];
    $i++;
    $user = $matches[$i];
    $i++;
    $script = $matches[$i];
    $freq = "";
    $type = "";
    if (preg_match("/^\*\/(\d+)$/", $min, $matches)) {
        $type = "cronregular";
        //$freq = "Every " . $matches[1] . " minutes";
        $freq = $matches[1]."min";
    }
    if (preg_match("/^\*\/(\d+)$/", $hour, $matches)) {
        $type = "cronregular";
        //$freq = "Every " . $matches[1] . " hours";
        $freq = $matches[1]."hour";
    }
    if (preg_match("/^(\d+)$/", $hour, $matches)) {
        if (preg_match("/^(\d+)$/", $min, $matches2)) {
            $freq = "At $matches[1]:$matches2[1]";
            $type = "cronhourmin";
        }
    }
    if (preg_match("/^#+$/", $disabled, $matches)) {
        $disabled = true;
        echo $this->Form->input(
            'active',
            array(
                'type' => 'checkbox',
                'label' => __('Active'),
                'class' => 'switchbtn form-group'
            )
        );
    } else {
        $disabled = false;
        echo $this->Form->input(
            'active',
            array(
                'type' => 'checkbox',
                'label' => __('Active'),
                'checked' => 'checked',
                'class' => 'switchbtn form-control cronswitch'
            )
        );
    }
    
    if ($disabled) {
        $attr = array(
            'class' => 'crontype',
            'options' => array(
            'cronhourmin' => __('Hour/Minute'),
            'cronregular' => __('Regulier'), 
            ),
            'disabled' => true,
            'value' => $type,
            'empty' => false,
            'label' => __('Type'),
        );
    } else {
        $attr = array(
            'class' => 'crontype',
            'options' => array(
            'cronhourmin' => __('Hour/Minute'),
            'cronregular' => __('Regulier'), 
            ),
            'value' => $type,
            'empty' => false,
            'label' => __('Type'),
        );
    }
    echo $this->Form->input('crontype', $attr);
    
    if ($type == "cronregular") {
        echo '<div id="cronregular" style="display:inline">';
    } else {
        echo ' <div id="cronregular" style="display:none">';
    }
    if ($disabled) {
        $attr = array(
                'options' => array(
                '15min' => __('Every 15 min'),
                '30min' => __('Every 30 min'),
                '45min' => __('Every 45 min'),
                '1hour' => __('Every hour'),
                '2hour' => __('Every 2 hours'),
                '3hour' => __('Every 3 hours'),
            ),

            'disabled' => true,
            'value' => $freq,
            'empty' => false,
            'label' => __('Frequence')
        );
    } else {
        $attr = array(
                'options' => array(
                '15min' => __('Every 15 min'),
                '30min' => __('Every 30 min'),
                '45min' => __('Every 45 min'),
                '1hour' => __('Every hour'),
                '2hour' => __('Every 2 hours'),
                '3hour' => __('Every 3 hours'),
            ),

            //'disabled' => true,
            'value' => $freq,
            'empty' => false,
            'label' => __('Frequence')
        );
    }
    echo $this->Form->input('cronfreq', $attr);
    
    if ($type == "cronhourmin") {
        echo '</div><div id="cronhourmin" style="display:inline">';
    } else {
        echo ' <div id="cronhourmin" style="display:none">';
    }
    
    $hours = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);
    if ($disabled) {
        $attr = array(
            'options' => array(
                $hours,
            ),
            'disabled' => true,
            'value' =>  $hour,
            'empty' => false,
            'label' => __('Hour'),
        );
    } else {
        $attr = array(
            'options' => array(
                $hours,
            ),
            //'disabled' => true,
            'value' =>  $hour,
            'empty' => false,
            'label' => __('Hour'),
        );
    }
    echo $this->Form->input('cronhour', $attr);
    
    $mins = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59);        
    if ($disabled) {
        $attr = array(
            'options' => array(
                $mins,
            ),
            'disabled' => true,
            'value' =>  $min,
            'empty' => false,
            'label' => __('Min'),
        );
    } else {
        $attr = array(
            'options' => array(
                $mins,
            ),
            //'disabled' => true,
            'value' =>  $min,
            'empty' => false,
            'label' => __('Min'),
        );
    }
    echo $this->Form->input('cronmin', $attr);
    ?>
    </div>
    <?php
    
    $options = array(
    'label' => __('Update'),
    'div' => array(
        'class' => 'form-group',
    ),
    'before' => '<div class="col-sm-offset-4 col-sm-4">',
    'after' => '</div>'
    );
    echo $this->Form->end($options);
}
?>