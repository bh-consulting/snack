<?php 
$this->assign('term_active', 'active');
?>
<h1><?php echo __('Terminal'); ?></h1>
<div id="term" style="width: 99%;" class="terminal">
    <div class="terminal-output">
        <div style="width: 100%;"></div>
        <div class="cmd" style="width: 100%; height:300px;">
    </div>
</div>


<?php

$this->start('script');
echo $this->Html->script('jquery.terminal.min');
echo $this->Html->script('jquery.mousewheel-min');
echo $this->Html->script('myterm');
$this->end();

?>