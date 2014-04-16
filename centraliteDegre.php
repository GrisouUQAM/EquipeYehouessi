<?php
 
// Algorithm de la centralite de degre: ki/(N-1), ou ki est le nombre de liens au noeud i et N est le nombre total de noeuds
function centraliteDegre($idIntervenant,$nbNoeud){
	// Obtention des nombre de liens au noeud i
	$numLink_column = Mysql_Query("SELECT count(debutLienId) AS totalLink FROM liens WHERE debutLienId = '".$idIntervenant."' OR finLienId = '".$idIntervenant."'");
	$numLink = mysql_fetch_assoc($numLink_column);

	// calcul de la centralite
	$degree = $numLink['totalLink']/($nbNoeud-1);

	//echo $degree."<br>";
	return $degree;
}


?>
