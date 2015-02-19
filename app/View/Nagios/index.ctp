<?php
$this->assign('nagios_active', 'active');

$login = "nagiosadmin";
$password=Configure::read('Parameters.nagios_password');
$host=Configure::read('Parameters.nagios_ip');
if (isset($password) && isset($host)) {
    $opts = array('http' =>
      array(
        'method'  => 'GET',//GET | POST
        'header'  => "Content-Type: text/html\r\n".
        "Authorization: Basic ".base64_encode($login.':'.$password)."\r\n",
        'timeout' => 300
      )
    );
                           
    $context  = stream_context_create($opts);

    if (isset($_GET['page'])) {
        $page=$_GET['page'];
    } else {
        $page="status";
    }

    if ($_SERVER['REQUEST_URI']=="/nagios") {
        $options="hostgroup=all&style=overview";
    } else {
        $options = substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'],"?"));
    }

    $url = "http://".$host."/cgi-bin/nagios3/".$page.".cgi?".$options;

    $cgi = file_get_contents($url, false, $context, -1, 9000000);

    if($page=="trends") //récupération des fonctions javascript
    {
    $javascript = substr($cgi,strpos($cgi,"<SCRIPT"),strpos($cgi,"</head>")-strpos($cgi,"<SCRIPT"));
    $cgi = substr($cgi,strpos($cgi,"<table border=0")); //suppression de l'en-tête
    $cgi=$javascript.$cgi; //rajout des fonctions javascripts
    }
    else {
         $cgi2 = substr($cgi,strpos($cgi,"<table")); //suppression de l'en-tête
        if ($cgi2 != false) {
            $cgi=$cgi2;
        }
    }
    $cgi2 = substr($cgi,0,strpos($cgi,"</body>")); //suppression de la fin
    if ($cgi2 != false) {
        $cgi=$cgi2;
    }

    $cgi = '<LINK REL="stylesheet" TYPE="text/css" HREF="/nagios3/stylesheets/'.$page.'.css"><td valign="top" align="center">'.$cgi;

    $cgi = str_replace("status.cgi?","nagios?page=status&p=309&",$cgi); //modification les liens
    $cgi = str_replace("extinfo.cgi?","nagios?page=extinfo&p=309&",$cgi);
    $cgi = str_replace("history.cgi?","nagios?page=history&p=309&",$cgi);
    $cgi = str_replace("histogram.cgi?","nagios?page=histogram&p=309&",$cgi);
    $cgi = str_replace("avail.cgi?","nagios?page=avail&p=309&",$cgi);
    $cgi = str_replace("notifications.cgi?","nagios?page=notifications&p=309&",$cgi);
    $cgi = str_replace("href='trends.cgi?","href='nagios?page=trends&p=309&",$cgi);
    $cgi = str_replace("HREF='trends.cgi?","href='nagios?page=trends&p=309&",$cgi);
    $cgi = str_replace("SRC='trends.cgi?","src='/nagios/cgi-bin/trends.cgi?",$cgi);
    $cgi = str_replace("src='trends.cgi?","src='/nagios/cgi-bin/trends.cgi?",$cgi);
    echo $cgi;
} else {
    echo '<div class="alert alert-warning" role="alert">Need to complete parameters for Nagios : ';
    echo $this->Html->link(
        __('Click Here'), array('controller' => 'parameters', 'action' => 'nagios'), array('escape' => false)
    );
    echo '</div>';
}
?>