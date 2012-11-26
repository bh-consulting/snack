<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
<h1>Login / Password user <? echo h($raduser['Raduser']['username']); ?></h1>

<p><strong>Comment: </strong>
<? echo $raduser['Raduser']['comment']; ?></p>

