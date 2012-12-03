<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
<h1>Certificate user <? echo h($raduser['Raduser']['username']); ?></h1>

<p><strong>Comment: </strong>
<? echo $raduser['Raduser']['comment']; ?></p>

<p><strong>Certificate path: </strong>
<? echo $raduser['Raduser']['cert_path']; ?></p>

<p><strong>EAP Type: </strong>
<? foreach($radchecks as $r){
    if($r['Radcheck']['attribute'] == 'EAP-Type'){
        echo $r['Radcheck']['value'];
        break;
    }
}
?>
</p>

