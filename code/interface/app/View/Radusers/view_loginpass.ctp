<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
<h1>Login / Password user <? echo h($raduser['Raduser']['username']); ?></h1>

<p><strong>Comment: </strong>
<? echo $raduser['Raduser']['comment']; ?></p>

<? foreach($radchecks as $r){
    if($r['Radcheck']['attribute'] == 'Simultaneous-Use')
        echo '<p><strong>Simultaneous use: </strong> ' . $r['Radcheck']['value'];
    if($r['Radcheck']['attribute'] == 'Expiration')
        echo '<p><strong>Expiration date: </strong> ' . $r['Radcheck']['value'];
}
?>
