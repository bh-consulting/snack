<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
<h1>Cisco user <? echo $raduser['Raduser']['username']; ?></h1>

<p><strong>Comment: </strong>
<? echo $raduser['Raduser']['comment']; ?></p>

<p><strong>NAS Port Type: </strong>
<? foreach($radchecks as $r){
    if($r['Radcheck']['attribute'] == 'NAS-Port-Type'){
        echo $r['Radcheck']['value'];
        break;
    }
}
?>
</p>
