<?php

session_start();
require_once 'connectionDB.php';
connectToDB();
videTables();

$user = $_GET["user"];
$_SESSION['user'] = $user;

$wikiUrl = "http://" . $_GET["wiki"];
$_SESSION['wikiUrl'] = $wikiUrl;

$firstQueryUrl = $wikiUrl 
	. "/w/api.php?action=query&list=usercontribs&format=json&uclimit=500&ucuser=" 
	. $user . "&ucnamespace=1&ucprop=title|ids";
$firstQueryContent = getQueryContent($firstQueryUrl);

$objTalk = json_decode($firstQueryContent, true);
$queryTalk = $objTalk['query'];
$userTalks = $queryTalk['usercontribs']; 
insertTalks($userTalks);
$talks = getTalks($user);
if (!$talks) {
    $message  = "Invalid query: " . mysql_error() . "\n";
    die($message);
}
printUserTalks($talks, $user);

function getTalks($user) {		  
    $sql = "SELECT discussionId, titre FROM discussion;";
    return mysql_query($sql);
}

function insertTalks($userTalks) {
	
    $nbTalks = sizeof($userTalks);
    for($i=0;$i<$nbTalks;$i++) {
        $talk = $userTalks[$i];
	$fixedTitle=($talk['title']);		
        $tab = explode(':',$fixedTitle,2);		 
        $titreDiscussion = $tab[1] ;
         
         //if( aParticipeDiscussion($talk['pageid'],$userName,$wikiUrl,$titreDiscussion)) {        
	    $query = "INSERT INTO grisou.discussion (discussionId, titre) VALUES (".$talk['pageid'].", '".$titreDiscussion ."')" ;		
	    Mysql_Query($query);
        //}	      		
	$query = "INSERT INTO grisou.user (userId, userName) VALUES (".$talk['userid'].", '".$talk['user'] ."')";
	Mysql_Query($query);
        $zero=0;
        
        $userId = $talk['userid'];
        $query = "insert into grisou.intervenants(intervenantId,intervenantName,intervenantAuteurArticle) values(".$userId.", '".$talk['user'] ."',".$zero.");";
        Mysql_Query($query);
    }     
}

function printUserTalks($talks, $user) {
    $listeDiscussion = "listeDiscussion";
	
    $result = "<h2>Discussions auxquelles <span style='color:blue;'>" . $user . "</span> a contribu&eacute;</h2>
    <p style='color:blue;'>Cliquez sur une ligne pour choisir une discussion.<p>
    <table class='tbl_result' width='100%' >
    <tr>            
    <td class='head' width='20%'>Page ID</td>
    <td class='head' width='60%'>Titre de la discussion</td>  
    <td class='head' width='20%' style='text-align:left;'>Cr&eacute;ateur</td>
    </tr></table><div id=$listeDiscussion><table class='tbl_result' width='100%'>";	
		
    if (!$talks) {
        echo "Impossible d'executer la requete dans la base : " . mysql_error();
        exit;
    }
    if (mysql_num_rows($talks) == 0) {
        echo "Aucune ligne trouvee, rien a afficher.";
        exit;
    }    
    while($row = mysql_fetch_assoc($talks)) {
        $envoi = '"'."envoiDiscussion(this)".'"';
    	$pageId = $row["discussionId"];
        $pageTitle = $row["titre"];		
        $isTalkCreator = " ";
			
	$result .= "<tr class='res' onclick = $envoi>";               
	$result .= "<td class='res' width='20%' >" . $pageId . "</td>";
	$result .= "<td class='res' width='60%' >" . $pageTitle . "</td>";		
	$result .= "<td class='res' width='20%' style='text-align:left;'>" . $isTalkCreator . "</td>";
	$result .= "</tr>";	
    }
  
    $result .= "</table></div>";
    print $result;		
}

function videTables() {    
    // on vide les tables a chaque nouvelle connection
    mysql_query('TRUNCATE TABLE discussion;');
    mysql_query('TRUNCATE TABLE user;');
    mysql_query('TRUNCATE TABLE intervenants;');
    mysql_query('TRUNCATE TABLE liens;');
    mysql_query('TRUNCATE TABLE centralites;');    
        
}

function getQueryContent($queryUrl) {
    return file_get_contents($queryUrl, true);
}


function aParticipeDiscussion($pageIdDiscus,$userName,$wikiUrl,$titreDiscussion) {  
    $trouve = 0;       
    $resultat=array(); 
    $nuArchive=0;
    $archiveValide=1;
    $pageId=0;
    
    while(($archiveValide==1) && ($trouve == 0)){
        if($nuArchive==0){
            $pageId = $pageIdDiscus; 
        }else {
            $pageId = $resultat[1];
        }
        $urlNbSection= $wikiUrl."/w/api.php?action=parse&pageid=".$pageId."&format=json";    
                        
        $nbSection = file_get_contents($urlNbSection); 
        $objTalk = json_decode($nbSection, true);
        $parser = $objTalk['parse'];
        $links = $parser['links'];
        $userRelation = serialize($links);
        $utilisateur1 = "User:".$userName;  //a adapter pour la langue mettre utilisateur a la place ou discussion utilisateur
        $utilisateur2 = "User talk:".$userName;
        $utilisateur3 = "User:".ucfirst($userName);
        $utilisateur4 = "User talk:".ucfirst($userName);
        if((strpos($userRelation,$utilisateur1)=== FALSE)&& (strpos($userRelation,$utilisateur2)=== FALSE)&& (strpos($userRelation,$utilisateur3)=== FALSE)&& (strpos($userRelation,$utilisateur4)=== FALSE)){
        } else{
            $trouve = 1;
        }   
        $nuArchive=$nuArchive+1;
        $resultat=valideArchive($wikiUrl,$nuArchive,$titreDiscussion);
        $archiveValide = $resultat[0];
    }
    return $trouve;
}


function valideArchive($wikiUrl,$nuArchive,$titreDiscussion){
    $resultat=array();
    
    $pageIdent=" ";
    $nameDiscussion=urlencode($titreDiscussion);
     $urlarchive = $wikiUrl."/w/api.php?action=query&titles=Talk:".$nameDiscussion."/Archive_".$nuArchive."&format=json";
     
    $archiveExiste = file_get_contents($urlarchive, true); 
    $objTalk = json_decode($archiveExiste, true);
    $stringObjetTalk = serialize($objTalk);
    if(strpos($stringObjetTalk,"missing")===FALSE){
        $query = $objTalk['query'];
        $page = $query['pages'];
        foreach ($page as $value) {
            $pageIdent = $value['pageid'];
        }
        $resultat[0]=1;
        $resultat[1]=$pageIdent;
    } else{
        $resultat[0]=0;
        $resultat[1]="nothing";
    }
    return $resultat;
}

?>