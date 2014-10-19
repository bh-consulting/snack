<?php
$this->extend('/Common/systemdetails_tabs');
$this->assign('systemdetails_notifications_active', 'active');
//debug($results);
echo "<br>";
foreach($results as $res) {
    if ($res['type'] == "ERR") {
        $datetime = new DateTime($res['date']);
        echo '<div class="alert alert-danger" role="alert">'.$datetime->format('Y-m-d H:i:s').' '.$res['msg'].'</div>';
    }
}
?>
