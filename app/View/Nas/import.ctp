<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

foreach ($results as $key => $result) {
    if ($result == 1) {
        echo '<div class="alert alert-success" role="alert">'.$key." Saved".'</div>';
    }
    else {
        echo '<div class="alert alert-danger" role="alert">'.$key." not saved".'</div>';
    }
}
?>