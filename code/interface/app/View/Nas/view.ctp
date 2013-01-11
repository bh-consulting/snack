<? 
$this->extend('/Common/radius_sidebar');
$this->assign('nas_active', 'active');

echo '<h1>' . __('NAS') . ' ' . $nas['Nas']['nasname'] . '</h1>';

?>
<p><strong><? echo __('Short name:'); ?></strong> 
<? echo $nas['Nas']['shortname']; ?>
</p>

<p><strong><? echo __('Type:'); ?></strong> 
<? echo $nas['Nas']['type']; ?>
</p>

<p><strong><? echo __('Ports:'); ?></strong> 
<? echo $nas['Nas']['ports']; ?>
</p>

<p><strong><? echo __('Server:'); ?></strong> 
<? echo $nas['Nas']['server']; ?>
</p>

<p><strong><? echo __('Community:'); ?></strong> 
<? echo $nas['Nas']['community']; ?>
</p>

<p><strong><? echo __('Description:'); ?></strong> 
<? echo $nas['Nas']['description']; ?>
</p>
