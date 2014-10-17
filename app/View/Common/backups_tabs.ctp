<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');
?>

<h1><?php echo __('Backups of %s (%s)', $nasShortname, $nasIP); ?></h1>

<div class="tabbable">

    <div class="tab-content">
        <?php echo $this->fetch('content'); ?>
    </div>
</div>
