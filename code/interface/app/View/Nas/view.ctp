<? 
$this->extend('/Common/radius_sidebar');
$this->assign('nas_active', 'active');

echo '<h1>NAS ' . $nas['Nas']['nasname'] . '</h1>';

?>
<p><strong>Short name:</strong> 
<? echo $nas['Nas']['shortname']; ?>
</p>

<p><strong>Type:</strong> 
<? echo $nas['Nas']['type']; ?>
</p>

<p><strong>Ports:</strong> 
<? echo $nas['Nas']['ports']; ?>
</p>

<p><strong>Server:</strong> 
<? echo $nas['Nas']['server']; ?>
</p>

<p><strong>Community:</strong> 
<? echo $nas['Nas']['community']; ?>
</p>

<p><strong>Description:</strong> 
<? echo $nas['Nas']['description']; ?>
</p>
