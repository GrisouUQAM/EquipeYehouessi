<?php

 function centraliteInter($idIntervenant, $nbNoeud) {
    
 
// Obtention des nombre de liens au noeud i
$numLink_column = Mysql_Query("SELECT count(debutLienId) AS totalLink FROM liens WHERE debutLienId = '".$idIntervenant."' OR finLienId = '".$idIntervenant."'");
$numLink = mysql_fetch_assoc($numLink_column);

// Obtention du nombre total de noeuds
$numNode_column = Mysql_Query("SELECT count(lienId) AS totalNode FROM liens");
$numNode = mysql_fetch_assoc($numNode_column);



// calcul de la centralite
$degree = $numLink['totalLink']/($numNode['totalNode']-1);


return $degree;

//A FAIRE DEMAIN ABSOLUMENT ET VOIR CE QUE CA DONNE
 //return 0;
}
?>
