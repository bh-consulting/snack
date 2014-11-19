<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SNACK</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

  </head>

  <body>

    <div class="container">
      <div class="header">
        <nav>
          <ul class="nav nav-pills pull-right">
            <li role="presentation"><a href="index.php">About</a></li>
            <li role="documentation" class="active"><a href="documentation.php">Documentation</a></li>
            <li role="presentation"><a href="gallerie.php">Captures d'écrans</a></li>
          </ul>
        </nav>
        <h3 class="text-muted">SNACK</h3>
      </div>
      <br />
      <hr>
      
       <!-- NAS -->
      <h3>NAS</h3>
      <p style="margin-bottom: 0cm; font-style: normal">Un NAS (Network
    Access Server) est l'équipement qui va communiquer avec SNACK pour
    faire les demandes de connexion au réseau et ouvrir les ports
    correspondants (ou bloquer)si la connexion a échouée .</p>
    <p style="margin-bottom: 0cm; font-style: normal"><br/>

    </p>
    <p style="margin-bottom: 0cm; font-style: normal">Pour créer un NAS,
    c'est très simple il suffit de cliquer sur «&nbsp;Ajouter&nbsp;»
    et rentrer les 4 paramètres demandés&nbsp;:</p>
    <p style="margin-bottom: 0cm; font-style: normal"><br/>

    </p>
    <ul>
	    <li/>
    <p style="margin-bottom: 0cm; font-style: normal">Adresse IP
	    du NAS</p>
	    <li/>
    <p style="margin-bottom: 0cm; font-style: normal">Nom du NAS</p>
	    <li/>
    <p style="margin-bottom: 0cm; font-style: normal">Clé
	    partagée entre le NAS et SNACK</p>
	    <li/>
    <p style="margin-bottom: 0cm; font-style: normal">Description
	    (non obligatoire)</p>
    </ul>
    <p style="margin-bottom: 0cm; font-style: normal"><br/>

    </p>
    <p style="margin-bottom: 0cm"><span style="font-style: normal">Une
    fois le NAS créé, il est nécessaire de redémarrer le service
    </span><i>freeradius</i><span style="font-style: normal"> via le
    tableau de bord (ou Dashboard) .</span></p>
    <p style="margin-bottom: 0cm; font-style: normal"><br/>
    <img src="img/snack2_big.png" />
    <p style="margin-bottom: 0cm; font-style: normal"><br/>

    </p>
     
    <p style="margin-bottom: 0cm">En cliquant, sur «&nbsp;Backup&nbsp;»
    vous pouvez voir les différentes connexions réalisées sur le NAS
    en question et les dernières configurations sauvegardées .</p>
    <p style="margin-bottom: 0cm">Il est également possible de faire une
    comparaison entre deux sauvegardes afin de voir les différences .</p>
    <p style="margin-bottom: 0cm"><br/> 
    <img src="img/snack-backup.png" />
    <p style="margin-bottom: 0cm; font-style: normal"><br/>

    </p>
    <!-- Utilisateurs -->
    <h3>Utilisateurs</h3>
    
    <img src="img/snack1_big.png" />
    <p style="margin-bottom: 0cm; font-style: normal"><br/>

    </p>
    <p style="margin-bottom: 0cm">Pour créer un utilisateur sur SNACK,
    vous avez plusieurs possibilités .</p>
    <p style="margin-bottom: 0cm">Tout d'abord cela dépend du type
    d'utilisateur que vous souhaitez créer . En effet plusieurs types
    d'utilisateurs sont proposés&nbsp;:</p>
    <ol type="a">
	    <li/>
    <p style="margin-bottom: 0cm">Authentifié par certificat</p>
    </ol>
    <p style="margin-bottom: 0cm">	L'utilisateur peut être authentifié
    par certificat, c'est la méthode la plus efficace en terme de
    sécurité, d'autant plus si le certificat est protégé par un mot
    de passe . Cependant, il est nécessaire d'intervenir sur le poste
    pour installer le certificat du serveur ainsi que celui de
    l'utilisateur .</p>
    <p style="margin-bottom: 0cm"><br/>

    </p>
    <ol type="a" start="2">
	    <li/>
    <p style="margin-bottom: 0cm">Authentifié par identifiant et
	    mot de passe</p>
	    <p style="margin-bottom: 0cm"></p>
	    <li/>
    <p style="margin-bottom: 0cm">Authentifié par adresse MAC</p>
    </ol>
    <p style="margin-bottom: 0cm">	</p>
    <ol type="a" start="4">
	    <li/>
    <p style="margin-bottom: 0cm">Téléphone Cisco</p>
	    <p style="margin-bottom: 0cm">Un téléphone Cisco peut être
	    authentifié soit par adresse MAC, soit par identifiant et mot de
	    passe . Si l'authentification se fait par adresse MAC, l'identifiant
	    et le mot de passe à entrer sont l'adresse MAC du téléphone, si
	    l'authentification se fait par identifiant et mot de passe alors
	    l'identifiant est le nom du téléphone (SEPXXXXXX) et le mot de
	    passe celui que vous avez spécifié sur le téléphone .</p>
    </ol>
    <p style="margin-bottom: 0cm"><br/>

    </p>
    <p style="margin-bottom: 0cm">Pour chacun des utilisateurs ci-dessus,
    un certain nombre de paramètres sont communs entre eux, comme le
    numéro du Vlan, la date d'expiration etc... Pour plus de simplicité
    et d'organisateur, ceux-ci peuvent être réunis dans des groupes
    d'utilisateurs . 
    </p>
    <p style="margin-bottom: 0cm">Par exemple, un groupe d'utilisateurs
    qui correspondra à tous les utilisateurs étant dans le VLAN 12 ou
    bien un groupe d'utilisateurs qui expirera telle date.</p>
    <p style="margin-bottom: 0cm"><br/>

    </p>
    <ol type="a" start="5">
	    <li/>
    <p style="margin-bottom: 0cm">Utilisateur SNACK&nbsp;: cet
	    utilisateur aura un accès sur SNACK et des droits spécifiques pour
	    créer ou supprimer des utilisateurs . Il existe différents rôles
	    pour l'utilisateur SNACK&nbsp;:</p>
	    <ol type="a">
		    <li/>
    <p style="margin-bottom: 0cm"><i>Utilisateur&nbsp;</i>: n'a
		    aucun accès sur l'interface SNACK (par défaut pour tous les
		    autres utilisateurs)</p>
		    <li/>
    <p style="margin-bottom: 0cm"><i>Tech&nbsp;</i>: droit de vue
		    sur les utilisateurs et le téléchargement des certificats</p>
		    <li/>
    <p style="margin-bottom: 0cm"><i>Admin&nbsp;: </i><span style="font-style: normal">peut
		    voir, créer et mettre à jour des objets</span></p>
		    <li/>
    <p style="margin-bottom: 0cm"><i>Root:</i><span style="font-style: normal">peur
		    voir, créer, mettre à jour et supprimer des objets</span></p>
	    </ol>
    </ol>
    <ul>
	    <p style="margin-bottom: 0cm; font-style: normal"></p>
    </ul>
    <p style="margin-bottom: 0cm"><br/>

    </p>
    <img src="img/snack-addloginpass.png" />
    <p style="margin-bottom: 0cm"><br/>

    </p>
    <p style="margin-bottom: 0cm">L'ajout d'utilisateurs peut se faire
    manuellement (un par un) en suivant l'assistant ou bien en important
    un fichier <i>csv</i> . Deux types d'importations sont possibles&nbsp;:
    une avec tous les paramètres, en général cette importation sert à
    réimporter une configuration exportée auparavant, et une simplifiée</p>
        
    <!-- Import -->
    <p style="margin-bottom: 0cm"><br/>
    
    </p>
    <img src="img/snack-import.png" />
    <p style="margin-bottom: 0cm"><br/>
    
    </p>
    <p style="margin-bottom: 0cm">Voici un exemple d'un fichier csv pour
    l'import simplifié&nbsp;:</p>
    <p style="margin-bottom: 0cm"><br/>

    </p>

    <pre>
    username/mac,certificate,login/pwd,phone,mac,cisco,password,VLAN,Comment
    test-user,0,1,0,0,0,truc,12,com test-user
    test-phone,0,0,1,0,0,,,com test-phone
    012456789112,0,0,0,1,0,012456789112,13,com 012456789112
    </pre>
    
    <!-- Sessions -->
    <h2>Sessions</h2>
    <p style="margin-bottom: 0cm">Dans l'onglet «&nbsp;Sessions&nbsp;»,
    il est possible de consulter les différentes sessions passées ou en
    cours, qu'elle soit de type 802.1X ou bien Telnet/SSH .</p>
    <p style="margin-bottom: 0cm">Afin d'avoir une vision plus claire,
    vous pouvez filtrer les sessions pour n'afficher que les sessions
    actives .</p>
    <p style="margin-bottom: 0cm"><br/>
    
    </p>
    <img src="img/snack-sessions-filter.png" />
    <p style="margin-bottom: 0cm"><br/>
    
    </p>
    
    <!-- Logs -->
    <h2>Logs</h2>
    <p style="margin-bottom: 0cm"><font size="3" style="font-size: 12pt">Vous
    pouvez consulter les logs pour le Radius, le SNACK et également les
    différents NAS .</font></p>
    <p style="margin-bottom: 0cm"><br/>
    <p style="margin-bottom: 0cm"><br/>
    
    </p>
    <img src="img/snack-logs.png" />
    <p style="margin-bottom: 0cm"><br/>
    
    </p>
    <!-- Dashboard -->
    <h2>Tableau de bord</h2>
    <p style="margin-bottom: 0cm"><font size="3" style="font-size: 12pt">Il
    est possible de redémarrer les différents services (freeradius
    mysql et nagios) depuis l'interface web.</font></p>
    <p style="margin-bottom: 0cm"><font size="3" style="font-size: 12pt">De
    plus, dans une future mise à jour (cf la capture d'écran) il sera
    possible de mettre à jour le snack directement via l'interface .</font></p>
    <p style="margin-bottom: 0cm"><br/>
    
      <hr>
      <footer class="footer">
        <p>&copy; BH-Consulting 2014</p>
      </footer>

    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
