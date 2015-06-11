<?php
$this->extend('/Common/help_tabs');
$this->assign('help_windowsxp_active', 'active');
?>
<h2><?php echo __('Certificates');?></h2>
<b>
ATTENTION : Si vous voulez que les certificats soient accessibles depuis toutes les sessions utilisateurs, il est nécessaire d'installer les certificat dans "la configuration ordiateur" via la console mmc.exe

</b>
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

<h4><?php echo __('Installation of client certificat');?></h4>
<?php 
echo __("Pour installer le certificat client au format p12, il vous suffit de double-cliquer dessus. Le même assistant que celui utilisé pour l'installation du certificat racine apparait.\\
Cliquez sur suivant lors du premier panneau. Cliquez sur suivant sur le panneau suivant (Le chemin du fichier est déjà bon).<br>
Sur le panneau suivant, un mot de passe vous est demandé. Laissez le champs vide et cliquez sur suivant");
echo "<br><br>";
echo $this->Html->image('help/windows/importClient.PNG', array('alt' => 'importClient'));
echo "<br><br>";
echo __("Cliquez ensuite sur suivant une nouvelle fois. La selection automatique du magasin est en effet adaptée pour ce cas.");
echo "<br><br>";
echo $this->Html->image('help/windows/importClient2.PNG', array('alt' => 'importClient2'));
echo "<br><br>";
echo __("Cliquez enfin sur Terminer");
echo "<br><br>";
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
echo __("Choisissez alors 'Carte à puce ou autre certificat' dans l'onglet 'Authentification'.");
echo "<br><br>";
echo $this->Html->image('help/windows/tls.PNG', array('width'=>'960px', 'alt' => 'TLS'));
echo "<br><br>";
echo __("Cliquez ensuite sur le bouton 'Paramètres...'");
echo "<br><br>";
echo $this->Html->image('help/windows/tlsParams.PNG', array('width'=>'960px', 'alt' => 'PEAP'));
echo "<br><br>";
echo __("Selectionnez le certificat correspondant.");
echo "<br>";
echo __("Après avoir validé, la connexion est établie.");
?>


