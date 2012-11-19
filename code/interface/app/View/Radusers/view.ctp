<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
<h1><?php echo h($raduser['Raduser']['username']); ?></h1>

<p><?php echo h($raduser['Raduser']['attribute']); ?></p>
<p><?php echo h($raduser['Raduser']['op']); ?></p>
<p><?php echo h($raduser['Raduser']['value']); ?></p>

