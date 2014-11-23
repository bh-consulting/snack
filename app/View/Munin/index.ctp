<?php
$this->assign('munin_active', 'active');
$host="127.0.0.1";
$opts = array('http' =>
  array(
    'method'  => 'GET',//GET | POST
    'header'  => "Content-Type: text/html\r\n",
    'timeout' => 300
  )
);
//debug("toto");                  
$context  = stream_context_create($opts);

if (isset($_GET['page'])) {
    $page=$_GET['page'];
    $n=substr_count($page, '/');
    $level=-1;
    $murl="";
    if ($n > 0) {
        $infos_url=explode('/', $page);
    }
    foreach ($infos_url as $key=>$info) {
        if ($info != ".") {
            //debug($info);
            $level++;
            //debug($key." ".$info);
            if ($key != ($n)) {
                if ($key == 0) {
                    $murl=$info;
                } else {
                    $murl=$murl.'/'.$info;
                }
            }
        }  
    }
    //debug($murl);
    //$n=substr_count($page, '/');
    //echo $level;
    
} else {
    //$page="localdomain/localhost.localdomain/index.html";
    $page="";
    $level=0;
    
}

//debug($level);
//debug($page);

if ($_SERVER['REQUEST_URI']=="/munin") {
    $options="";
} else {
    $options ="";// substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'],"?"));
}

$url = "http://".$host."/munin0/".$page;

$cgi = file_get_contents($url, false, $context, -1, 9000000);
$cgi = preg_replace('/src="(\.\.\/)+/','src="../munin0/',$cgi);
//$cgi = preg_replace('/href="\.\/static\/style-new\.css"/', 'href="../munin0/static/style-new.css"' , $cgi);
if (!isset($murl)) {
    //debug("test0");
    $cgi = preg_replace('/href="\.\/static\/style-new\.css"/', 'href="../munin0/static/style-new.css"' , $cgi);
    $cgi = preg_replace('/a href="(.*)"/', 'a href="../munin?page=${1}"', $cgi);
}
else {
    //debug("test");
    $cgi = preg_replace('/href="(\.\.\/)+static\/style-new\.css"/', 'href="../munin0/static/style-new.css"' , $cgi);
    $cgi = preg_replace('/a href="(.*)"/', 'a href="../munin?page='.$murl.'/${1}"', $cgi);
}

if ($level==0) {
    $cgi = preg_replace('/href="\.\/static\/style-new\.css"/', 'href="../munin0/static/style-new.css"' , $cgi);
}
if ($level==2) {
    //$cgi = str_replace('src="../../','src="../munin0/',$cgi);
    
    //$cgi = preg_replace('/a href="(?!\.\.\/)(.*)"/', 'a href="../munin?page='.$murl.'/${1}"', $cgi);
    //$cgi = preg_replace('/href="(\.\.\/)+static\/style-new\.css"/', 'href="../munin0/static/style-new.css"' , $cgi);
    //$cgi = str_replace('a href="../../', 'a href="../munin?page=', $cgi);
}
/*
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
*/

/*
$cgi = str_replace('src="../../','src="../munin0/',$cgi); //modification les liens
//$cgi = str_replace('<link rel="stylesheet" href="../../', '<link rel="stylesheet" href="../munin0/', $cgi);
//$cgi = preg_replace('href=".*style-new.css"',"fd", $cgi);//'href="../munin0/static/style-new.css"', $cgi);

//$cgi = preg_replace('/href="(?!\.\.\/)(.*)"/', 'href="../munin0/${1}"', $cgi);
$cgi = preg_replace('/a href="(?!\.\.\/)(.*)"/', 'a href="../munin?page=${1}"', $cgi);
$cgi = str_replace('a href="../../', 'a href="../munin?page=', $cgi);
//$cgi = str_replace('a href="../', 'a href="../munin?page=', $cgi);


/*$cgi = str_replace("extinfo.cgi?","nagios?page=extinfo&p=309&",$cgi);
$cgi = str_replace("history.cgi?","nagios?page=history&p=309&",$cgi);
$cgi = str_replace("histogram.cgi?","nagios?page=histogram&p=309&",$cgi);
$cgi = str_replace("avail.cgi?","nagios?page=avail&p=309&",$cgi);
$cgi = str_replace("notifications.cgi?","nagios?page=notifications&p=309&",$cgi);
$cgi = str_replace("href='trends.cgi?","href='nagios?page=trends&p=309&",$cgi);
$cgi = str_replace("HREF='trends.cgi?","href='nagios?page=trends&p=309&",$cgi);
$cgi = str_replace("SRC='trends.cgi?","src='/nagios/cgi-bin/trends.cgi?",$cgi);
$cgi = str_replace("src='trends.cgi?","src='/nagios/cgi-bin/trends.cgi?",$cgi);*/
echo $cgi;
?>