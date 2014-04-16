<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//http://en.wikipedia.org/w/api.php */

session_start();
$pageIdDiscus = $_POST["nomDiscussion"];
$user = $_SESSION['user'];
$wikiUrl = $_SESSION['wikiUrl'];

require_once 'centraliteDegre.php';
require_once 'centraliteInter.php';
require_once 'centraliteProxi.php';
require_once 'connectionDB.php';
require_once 'floyd.php';
require_once 'fonctionsCentralite.php';

//$listeUsersQuery = $wikiUrl."/w/api.php?action=query&prop=contributors&format=json&pclimit=500&ucnamespace=1&pageids=".$pageIdDiscus;
connectToDB();

$query = "select userId from user where userName='".$user."';";
$ligne = mysql_query($query);
$id = Mysql_fetch_array($ligne);   
$intervenantId = $id["userId"];

viderTable("centralites");
viderTable("liens");
viderTable("intervenants");
echo $intervenantId;
insererUserDansTableIntervenant($user,$intervenantId);
etablitReseau($pageIdDiscus,$wikiUrl);

// Obtention du nombre total de noeuds
$nbNoeud = calculNbTotalNoeud();

$centraliteDegre = centraliteDegre($intervenantId, $nbNoeud);
$centraliteInter = centraliteInter($intervenantId, $nbNoeud);
$centraliteProxi = centraliteProxi($intervenantId, $nbNoeud);

$query = "select titre from discussion where discussionId=".$pageIdDiscus.";";
$ligne = mysql_query($query);
$titre = Mysql_fetch_array($ligne);   
$nomDiscussion = $titre["titre"];   
    
$query = "insert into grisou.centralites (userId,centraliteDegre,centraliteInter,centraliteProxi) values(".$intervenantId.",".$centraliteDegre.",".$centraliteInter.",".$centraliteProxi.");";
Mysql_Query($query);

$resultCentralite=htmlResultCentralite($nomDiscussion,$centraliteDegre, $centraliteInter, $centraliteProxi);
print $resultCentralite;


function calculNbTotalNoeud() {
     $query="SELECT count(*) as totalNode from (((select distinct debutLienId from liens ) union (select distinct finLienId from liens))as tableCommune);";
     $queryResult = Mysql_Query($query);
     $tableNbNoeud = mysql_fetch_assoc($queryResult);
     return $tableNbNoeud['totalNode'];
}

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

function etablitReseau($pageIdDiscus,$wikiUrl ) {
    $resultat=array(); 
    $nuArchive=0;
    $archiveValide=1;
    while($archiveValide==1){
        if($nuArchive==0){
            $pageId = $pageIdDiscus; 
        }else {
            $pageId = $resultat[1];
        }
        $urlNbSection= $wikiUrl."/w/api.php?action=parse&pageid=".$pageId."&prop=sections&format=json";    
        $nbSection = file_get_contents($urlNbSection, true); //getQueryContent($listeUsersQuery);
        $objTalk = json_decode($nbSection, true);
        $section = $objTalk['parse'];
        $nbSection = count($section['sections']);
        $nbSectionNum =(int)$nbSection;
    
        for ($nuSection=1; $nuSection<$nbSectionNum+1;$nuSection++) {    
            $arrayUsers = array();//ou je peux faire un unset :a la fin et une initialisation avant le debut de la section    
            $section = "&section=";
            $urlUserMemeTalk = $wikiUrl."/w/api.php?action=parse&pageid=".$pageId.$section.$nuSection."&format=json";
            $nbSection = file_get_contents($urlUserMemeTalk); //getQueryContent($listeUsersQuery);
            $objTalk = json_decode($nbSection, true);
            $parser = $objTalk['parse'];
            $links = $parser['links'];
            $nbUsers = sizeof($links);
            for($i=0;$i<$nbUsers;$i++){
                $userI=$links[$i];
                $userRelation= $userI['*'];
                if(strpos($userRelation,"User")=== FALSE){
                } else{
                    $tab = explode(':',$userRelation);		 
                    $userRelation = $tab[1] ;
                    $arrayUsers[$userRelation]=1;// ca va reecraser de sorte que je n'aurai pas de duplication
                }   
            }
            creerLiens($wikiUrl,$arrayUsers,$nuSection,$pageIdDiscus,$nuArchive);
        }
        $nuArchive=$nuArchive+1;
        $resultat=valideArchive($wikiUrl,$nuArchive,$pageIdDiscus);
        $archiveValide = $resultat[0];
    }
}

function trouverNomDiscussion($discussionId){
    $query = "select titre as titre from discussion where discussionId=".$discussionId.";";
    $queryResult = Mysql_Query($query);
    $titre = mysql_fetch_assoc($queryResult);
    return $titre['titre'];
}

function valideArchive($wikiUrl,$nuArchive,$pageIdDiscussion){
    $resultat=array();
    $nameDiscussion = trouverNomDiscussion($pageIdDiscussion);
    $pageIdent=" ";
    $nameDiscussion=urlencode($nameDiscussion);
     $urlarchive = $wikiUrl."/w/api.php?action=query&titles=Talk:".$nameDiscussion."/Archive_".$nuArchive."&format=json";
     
    $archiveExiste = file_get_contents($urlarchive, true); //getQueryContent($listeUsersQuery);
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

function viderTable($nomTable){
    $query = "TRUNCATE TABLE ".$nomTable.";";
    mysql_query($query);
}




function creerLiens($wikiUrl,$arrayUsers,$nuSection,$pageIdDiscus,$nuArchive){
    $query=array();
    foreach ($arrayUsers as $intervenantName => $intervenantId) {        
        $arrayUsers[$intervenantName] = chercherIdIntervenants($wikiUrl,$intervenantName);        
    }
    $un= 1;
    $zero=0;   
    foreach($arrayUsers as $intervenantName => $intervenantId) {
        $userNameChar ='"'.$intervenantName.'"' ;
        $userChar='"'.$intervenantId.'"' ;
        $query = "insert into grisou.intervenants(intervenantId,intervenantName,intervenantAuteurArticle) values(".$userChar.",".$userNameChar.",".$zero.");";
        Mysql_Query($query);
    }    

    $tailleUser = sizeof($arrayUsers);
    //$lastIndex = chercherLastIndexTableLiens();
    for($i=0;$i<$tailleUser ;$i++) {
        for($j=0;$j<$tailleUser ;$j++){
            $arrayCles = array_keys($arrayUsers);          
            $intervenantName1= $arrayCles[$i];  
            $intervenantName2= $arrayCles[$j];            
            $intervenantNameChar1 ='"'.$intervenantName1.'"' ;
            $intervenantNameChar2 ='"'.$intervenantName2.'"' ;
            $intervenantId1 = chercherIdintervenantsDsTable($intervenantName1);
            $intervenantId2 = chercherIdintervenantsDsTable($intervenantName2);
            $intervenantIdChar1 ='"'.$intervenantId1.'"' ;
            $intervenantIdChar2 ='"'.$intervenantId2.'"' ;
            $interAlphabetique = strcmp($intervenantName1,$intervenantName2);
            if($interAlphabetique<0){                
                //$lastIndex = $lastIndex+1;
                $query = "insert into grisou.liens(discussionId,debutLienId,debutLien,finLienId,finLien,poids,noSection,noArchive) values(".$pageIdDiscus.",".$intervenantIdChar1.",".$intervenantNameChar1.",".$intervenantIdChar2.",".$intervenantNameChar2.",".$un.",".$nuSection.",".$nuArchive.");";
                Mysql_Query($query);
            }else if($interAlphabetique>0){  
              //  $lastIndex = $lastIndex+1;
                  $query = "insert into grisou.liens(discussionId,debutLienId,debutLien,finLienId,finLien,poids,noSection,noArchive) values(".$pageIdDiscus.",".$intervenantIdChar2.",".$intervenantNameChar2.",".$intervenantIdChar1.",".$intervenantNameChar1.",".$un.",".$nuSection.",".$nuArchive.");";
                //Mysql_Query($query);           
                
            }
        }
    }
            
    
}
    
 function insererUserDansTableIntervenant($userName,$userId){
       $userNameChar ='"'.$userName.'"' ;
        $userChar='"'.$userId.'"' ;
        $zero=0;   
        $query = "insert into grisou.intervenants(intervenantId,intervenantName,intervenantAuteurArticle) values(".$userChar.",".$userNameChar.",".$zero.");";
        Mysql_Query($query);
    }
    

function chercherIdintervenantsDsTable($intervenantName){
    $intervenantNameChar1 ='"'.$intervenantName.'"' ;
    $query = "select intervenantId as userId from intervenants where intervenantName=".$intervenantNameChar1.";";
    $queryResult = Mysql_Query($query);
    $userId = mysql_fetch_assoc($queryResult);
    return $userId['userId'];      
}
 
function chercherIdIntervenants($wikiUrl,$intervenantName){
   // $intervenantName = "Dave Mckee";
    $intervenantName=urlencode($intervenantName);
    $urlUserId = $wikiUrl."/w/api.php?action=query&list=users&ususers=".$intervenantName."&format=json";
    $userId = file_get_contents($urlUserId, true); //getQueryContent($listeUsersQuery);
    $objTalk = json_decode($userId, true);
    $query = $objTalk['query'];
    $user = $query['users'];
    $userFirst=$user[0];
    $chercher = serialize($userFirst);
    $id=" ";
    if(strpos($chercher,"userid")=== FALSE){
        $id = $userFirst['name'];
    } else {
        $id = $userFirst['userid'];
    }   
    
    return $id;
}
        
function chercherLastIndexTableLiens() {      
    $query = "select MAX(lienId) as lastIndex from liens;";
    $queryResult = Mysql_Query($query);
    $lastIndex = mysql_fetch_assoc($queryResult);
    if ($lastIndex == 0) {
        return 0;
    } else {
        return $lastIndex['lastIndex']+1;
    }
}
     
function toDo() {
    /*TRAITER LE CAS DE DIVISIONS PAR ZERO DANS LE CALCUL DES CENTRALITE
   
     *ISCREATOR OU NON AUSSI */
}

?>
