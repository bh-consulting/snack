<?php
$this->assign('about_active', 'active');
?>
<div id="content" class="main content">

<?php echo $this->Session->flash(); ?>
<?php echo $this->fetch('content'); ?>


<p>

<?php echo $this->Html->image('logo.png', array('height' => '160px', 'class' => 'logo', 'alt' => 'SNACK')) ?>
<h2>Secure Network Access Control for Kids</h2><br />    
<strong id="snackcopy">version
<?php
$file = new File(APP . 'VERSION.txt', false);
$tmp = "";
if ($file->exists()) {
$tmp = $file->read(false, 'rb', false);
echo $tmp;
}
?>
<br /><br /><br /><br /><br />    
<strong><u><?php echo __('Authors:') ?></u><br /></strong> 
Nicolas BOUGET<br />
<a href="http://julien.guepin.fr">Julien GUÉPIN</a><br />
Marc PINHÈDE<br />
<a href="http://julien.vaubourg.com">Julien VAUBOURG</a><br />
<a href="http://blog.guigeek.org">Guillaume ROCHE</a>
<br /><br />
<em>Copyright <a href="http://www.gnu.org/licenses/">GPL v3</a> &copy; 2015 <a href="http://www.bh-consulting.net">b.h. consulting</a></em>