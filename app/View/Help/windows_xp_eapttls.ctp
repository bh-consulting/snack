<?php
$this->extend('/Common/help_tabs');
$this->assign('help_windowsxp_active', 'active');
?>
<h2><?php echo __('User/Password with Certificate Server');?></h2>

<h4><?php echo __('Installation of root certificat');?></h4>
<?php 
echo __("Pour installer le certificat racine, ouvrer Démarrer puis cliquez sur 'Exécuter' et lancez 'certmgr.msc");
echo "<br><br>";
echo $this->Html->image('help/windows/certmgr.PNG', array('alt' => 'Cert Manager'));
echo "<br><br>";
echo __("Double-cliquez ensuite sur les 'Authorités de certification racines de confiance' et faite un clic droit sur le sous-dossier 'Certificats', puis lancez 'toutes les tâches', 'Importer...'.");
echo "<br><br>";
echo $this->Html->image('help/windows/importCacert.PNG', array('alt' => 'importCacert'));
echo "<br><br>";
echo __("Cliquez suivant sur le premier panneau proposé. Puis sélectonnez votre fichier grâce au bouton 'Parcourir...");
echo "<br><br>";
echo $this->Html->image('help/windows/importCacertFile.PNG', array('alt' => 'importCacertFile'));
echo "<br><br>";
echo __("Cliquez sur suivant. Le panneau suivant vous demande de configurer le magasin dans lequel le certificat sera importé. Ici, le bon magasin est déjà choisi (Authorité de certification racines de confiance). Cliquez donc sur OK.");
echo "<br><br>";
echo $this->Html->image('help/windows/importCacertStorage.PNG', array('alt' => 'importCacertStorage'));
echo "<br><br>";
echo __("Cliquez sur suivant. Le dernier panneau permet de terminer l'installation. Cliquez donc sur Terminer.");
echo "<br><br>";
echo $this->Html->image('help/windows/importCacertFinish.PNG', array('alt' => 'importCacertFinish'));
echo "<br><br>";
echo __("Un message d'alerte s'ouvre donc pour vous demander confirmation de l'import d'un nouveau certificat racine. Vous pouvez d'ailleurs voir apparaître le nom de l'autorité de certification. (Ici, PoilCorp).");
echo "<br><br>";
echo $this->Html->image('help/windows/importCacertAuthorize.PNG', array('alt' => 'importCacertAuthorize'));
echo "<br><br>";
echo __("Authorisez cet import ('oui').
Puis validez et quittez toutes les fenêtres ouvertes.");
?>

<br><br>

<h4><?php echo __('Configuration');?></h4>
<?php
echo __("Allez dans le panneau des connexions.");
echo "<br><br>";
echo $this->Html->image('help/windows/connections.PNG', array('alt' => 'Connections'));
echo "<br><br>";
echo __("Puis effectuez un clic droit sur la connexion que vous utilisez pour accéder au reseaux, et ouvrez les propriétés de la connexion.");
echo "<br><br>";
echo $this->Html->image('help/windows/connectionProperties.PNG', array('alt' => 'Connection Properties'));
echo "<br><br>";
echo __("Choisissez alors 'EAP protégé (PEAP)' dans l'onglet 'Authentification'.");
echo "<br><br>";
echo $this->Html->image('help/windows/peap.PNG', array('alt' => 'PEAP'));
echo "<br><br>";
echo __("Cliquez ensuite sur le bouton 'Paramètres...' à coté de l'option PEAP et cochez votre authorité de certification dans la liste présentée.");
echo "<br><br>";
echo $this->Html->image('help/windows/peapParams.PNG', array('alt' => 'PEAP'));
echo "<br><br>";
echo __("Cliquez ensuite sur le bouton 'Configurer...' à coté du champs présentant la méthode d'authentification (Mot de passe sécurité (EAP MSCHAP Version 2)).");
echo __("Décochez alors l'utilisation du nom de session, à moins que celui-ci soit effectivement celui utilisé par votre administrateur lors de la création du certificat.");
echo "<br><br>";
echo $this->Html->image('help/windows/peapParamsConfig.PNG', array('alt' => 'PEAP'));
echo "<br><br>";
echo __("Puis validez et fermez toutes les fenêtres ouvertes.
Connectez-vous physiquement au reseau.\\
Une info-bulle windows doit apparaître, vous signifiant que l'accès au réseaux requiert des informations supplémentaires.");
echo __("Après avoir cliqué sur l'info-bulle, il suffit de renseigner ses identifiants.");
echo "<br><br>";
echo $this->Html->image('help/windows/credentials.PNG', array('alt' => 'credentials'));
echo "<br><br>";
echo __("Après avoir validé, la connexion est établie.");
?>
