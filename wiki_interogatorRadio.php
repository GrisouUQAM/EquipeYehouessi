
<?php

//set_time_limit(360);
connectToDB();
$path = getcwd();
//include( $path. '/creerTableFictive.php');


$creerBaseDonnee = $path.'/creerBaseDonnee.sql';

$query = "source $creerBaseDonnee" ;	
Mysql_Query($query);
		
$user = $_GET["user"];
$wikiUrl = "http://" . $_GET["wiki"];

$firstQueryUrl = $wikiUrl 
	. "/w/api.php?action=query&list=usercontribs&format=json&uclimit=500&ucuser=" 
	. $user . "&ucnamespace=1&ucprop=ids%7Ctitle%7Ccomment";
$firstQueryContent = getQueryContent($firstQueryUrl);

$objTalk = json_decode($firstQueryContent, true);
$queryTalk = $objTalk['query'];
$userTalks = $queryTalk['usercontribs']; 
insertTalks($userTalks, $user, $wikiUrl);
$talks = getTalks($user);
if (!$talks) {
	$message  = "Invalid query: " . mysql_error() . "\n";
	$message .= "Whole query: " . $sql;
	die($message);
}
printUserTalks($talks, $user);

function getTalks($user) {		  
	  $sql = "SELECT discussionId, titre FROM discussion;";
	return mysql_query($sql);
}

function insertTalks($userTalks, $user, $wikiUrl) { //permet d'inserer dans la table discussion la liste de toutes les discussions auxquelles le user a participe
	$commentPrecedant="";
	foreach ($userTalks as $talk) {
		$fixedTitle=mysql_real_escape_string($talk['title']);		
         $tab = explode(':',$fixedTitle,2);		 
         $titreDiscussion = $tab[1] ;
	
		
		$query = "INSERT INTO grisou.discussion (discussionId, titre) VALUES (".$talk['pageid'].", '".$titreDiscussion ."')" ;		
		Mysql_Query($query);
				
		$query = "INSERT INTO grisou.user (userId, userName) VALUES (".$talk['userid'].", '".$talk['user'] ."')";
		Mysql_Query($query);

     }
     //insertRelatedUsers( $user,$wikiUrl);
}



function printUserTalks($talks, $user) {
    $listeDiscussion = "listeDiscussion";
	$listeDiscussionAChoisir =""; //A FAIRE
	$tableListe="mytable";
	$result = "<h2>Discussions auxquelles <span style='color:blue;'><i>" . $user . "</i></span> a contribu&eacute;</h2>
      <p>Cliquez sur une ligne parmis les r&eacute;sultats afin de visualiser les utilisateurs qui ont int&eacute;ragis avec <i>" . $user . " </i>dans cette discussion.<p>
      <table class='tbl_result' width='100%' >
      <tr>            
      <td class='head' width='12%'>Page ID</td>
      <td class='head'>  Titre de la discussion</td>  
      <td class='head' style='text-align:right;'>Cr&eacute;ateur</td>
      </tr></table><div id=$listeDiscussion><table id='mytable' class='tbl_result' width='100%' cellpadding='0' cellspacing='0' border='0'>";	
	
    if (!$talks) {
        echo "Impossible d'exécuter la requête dans la base : " . mysql_error();
        exit;
    }
    if (mysql_num_rows($talks) == 0) {
        echo "Aucune ligne trouvée, rien à afficher.";
        exit;
    }
    
	while($row = mysql_fetch_assoc($talks)) {
     
    	$pageId = $row["discussionId"];
		$pageTitle = $row["titre"];		
		$isTalkCreator = "Non";
				
		$result .= "<tr class='res' >";
		$result .= "<td class='res' >" . $pageId . "</td>";
		$result .= "<td class='res' > <input type='radio' class='paiement' name='paiement'>". $pageTitle . "</td>";		
		$result .= "<td class='res' style='text-align:left; padding-right:15px;'>" . $isTalkCreator . "</td>";
		$result .= "</tr>";					
		
	}
  
	$result .= " <button onclick='sendForm1()'> envoyer </button></div></table>";

	print $result;		
}

function connectToDB() {
    $hostname = "localhost";
    $database = "grisou";
    $username = "root";
    $password = "";

    $link = mysql_connect($hostname, $username, $password);
	
    if (!$link) 
   		die('La connection &agrave; la bd a &eacute;chou&eacute;: ' . mysql_error());

    $db_selected = mysql_select_db($database, $link);
	
    if (!$db_selected) 
   		die ('Impossible de s&eacute;lectionn&eacute; la bd: ' . mysql_error());
	
    // on vide les tables à chaque nouvelle connection
	mysql_query('TRUNCATE TABLE page;');
	mysql_query('TRUNCATE TABLE talk;');
	mysql_query('TRUNCATE TABLE user;');
	mysql_query('TRUNCATE TABLE comments;');
}

function getQueryContent($queryUrl) {
	return file_get_contents($queryUrl, true);
}



?>