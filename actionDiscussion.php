<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//http://en.wikipedia.org/w/api.php */



//$nomDiscus = $_GET["nomDiscussion"]; 
$pageIdDiscus=$_POST["nomDiscussion"];
$user = $_SESSION['user'];
$wikiUrl = $_SESSION['wikiUrl'];

require_once 'centraliteDegre.php';
require_once 'centraliteInter.php';
require_once 'centraliteProxi.php';
require_once 'connectionDB.php';

$listeUsersQuery = $wikiUrl."/w/api.php?action=query&prop=contributors&format=json&pclimit=500&ucnamespace=1&pageids=".$pageIdDiscus;
$listeUsers = file_get_contents($listeUsersQuery, true); //getQueryContent($listeUsersQuery);
$objTalk = json_decode($listeUsers, true);
$queryUsers = $objTalk['query'];
$queryPages = $queryUsers['pages'];
$queryPageId = $queryPages[$pageIdDiscus];
$queryContributors = $queryPageId['contributors'];
connectToDB();
//etablitReseau($queryContributors, $user, $pageIdDiscus);


$query = "select intervenantId from intervenants where intervenantName='".$user."';";
$ligne = mysql_query($query);
$id = Mysql_fetch_array($ligne);   
$intervenantId = $id["intervenantId"];  
//$centraliteDegre=  degreeCentrality($intervenantId);

 
$inter = "I1";
$centraliteDegre=  degreeCentrality($inter);
$centraliteInter=centraliteInter($intervenantId);
$centraliteProxi=centraliteProxi($intervenantId);



$query = "select titre from discussion where discussionId=".$pageIdDiscus.";";
$ligne = mysql_query($query);
$titre = Mysql_fetch_array($ligne);   
$nomDiscussion = $titre["titre"];   
    
$query = "insert into grisou.centralites (userId,centraliteDegre,centraliteInter,centraliteProxi) values(".$intervenantId.",".$centraliteDegre.",".$centraliteInter.",".$centraliteProxi.");";
Mysql_Query($query);

$resultCentralite=htmlResultCentralite($nomDiscussion,$centraliteDegre, $centraliteInter, $centraliteProxi);
print $resultCentralite;



function htmlResultCentralite($nomDiscussion,$centraliteDegre, $centraliteInter, $centraliteProxi) {
    $resultCentralite="<h2 class='btn-rouge'><b> Centralit&eacute;s pour la discussion ".$nomDiscussion." </b></h2>
    <table class='tbl_result' width='100%'>
     <tr>
   <td class='head' width='20%' >Centralit&eacute de d&eacutegr&eacute</td>
     <td class='head' width='20%' >Centralit&eacute; de d'interm&eacute;diarit&eacute;</td>
     <td class='head' width='20%' >Centralit&eacute; de proximit&eacute;</td>
     </tr>
   <tr>
  <td >".$centraliteDegre."</td>
  <td>".$centraliteInter."</td>
   <td>".$centraliteProxi."</td>
  </tr>
   </table>";
    return $resultCentralite;
}

function etablitReseau($contributors, $user, $pageIdDiscus ) { //permet d'inserer dans la table discussion la liste de toutes les discussions auxquelles le user a participe
	
       $i=0;     
	foreach ($contributors as $contributor) {        
            $userId=$contributor['userid'];
        	$userName=$contributor['name'];      
	
        $un= 1;
        $zero=0;   
        if($userName != $user) {
           $userNameChar ='"'.$userName.'"' ;
           $userChar='"'.$user.'"' ;
            $query = "insert into grisou.intervenants(intervenantId,intervenantName,intervenantAuteurArticle) values(".$userId.",".$userNameChar.",".$zero.");";
          	Mysql_Query($query);
            $query = "insert into grisou.liens(lienId,discussionId,debutLien,finLien,poids) values(".$i.",".$pageIdDiscus.",".$userChar.",".$userNameChar.",".$un.");";
	    Mysql_Query($query);
	    $i=$i+1;
        }

        }

}


/*function lienTotal() {
    $NBDISCUSSMAX = 50;
    $NBUSERSMAX = 50;
    
    $nbDiscus = 0;
    $nbUser = 0;
    i=0;
    userDepart=cequieaetemissurleformulaire;
    limite=ok;
    WHILE(  limite==ok)' {
        
            getListeDiscussion(userDepart))//select listeDiscussion where user=userDepart  modifier la table discussio pour mette le userId et leuserNAE
    for(discussion)
        $chaqueuser= getListeUser(discussion) faire tout ca avec des fetch
        for(chaqueUser)
        ajouter au fur et a mesure les nouveaux users
            link(userDepart,chaqueUser,discuss)
    je quitte les deux bcoules(je pourrai mettre une limite pour chaque discussion et une limite pour chaque user à prendre par discussion0
    checke la limite si limite non atteint 
    i=i+1;
    userDepart=fetch[i] je prends le user 1 de la tables des user ainsi de suite
    le probleme en faissant ca c'est que je fausse les calculs de centralite car l'aurai toujours un nombre constant
        ou je vais carrement prendre des nombres random faire tout ca d'ici vendredi c'est tout'
        si je fais le reseau par discussio il faut que je trouve une facon de ne pas mettre tout
        le monde en contact et
    
     

            
                    
    if($userName != $user) {
           $userNameChar ='"'.$userName.'"' ;
           $userChar='"'.$user.'"' ;
            $query = "insert into grisou.intervenants(intervenantId,intervenantName,intervenantAuteurArticle) values(".$userId.",".$userNameChar.",".$zero.");";
          	Mysql_Query($query);
            $query = "insert into grisou.liens(lienId,discussionId,debutLien,finLien,poids) values(".$i.",".$pageIdDiscus.",".$userChar.",".$userNameChar.",".$un.");";
	    Mysql_Query($query);
	    $i=$i+1;
        }
    
}
function getPageId() {
	$sql = "SELECT pageId
	  FROM comments
	  GROUP BY pageId
	  ORDER BY pageId";


	return mysql_query($sql);
}

$pages=getPageId();
	while($pageIdRecord = Mysql_fetch_array($pages))*/



?>
