<?php
$this->assign('about_active', 'active');
?>
<div id="content" class="main content">

<?php echo $this->Session->flash(); ?>
<?php echo $this->fetch('content'); ?>
<h2>Secure Network Access Control for Kids</h2><br />
<strong><?php echo __('Authors:') ?><br /></strong> 
Nicolas BOUGET<br />
<a href="http://julien.guepin.fr">Julien GUÉPIN</a><br />
Marc PINHÈDE<br />
<a href="http://julien.vaubourg.com">Julien VAUBOURG</a><br />
<a href="http://blog.guigeek.org">Guillaume ROCHE</a>
<br /><br />
<em>Copyright <a href="http://www.gnu.org/licenses/">GPL v3</a> &copy; 2013 <a href="http://www.bh-consulting.net">b.h. consulting</a></em>