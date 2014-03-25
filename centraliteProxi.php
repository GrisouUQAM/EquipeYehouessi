<?php
require_once 'dijkstra.php';
function centraliteProxi($intervenantIdI, $nbNoeud) {
	//echo $nbNoeud;
	
	// calcul de la centralite
	$degree = ($nbNoeud-1)/sumDist($intervenantIdI);

	return $degree;
}

function sumDist($intervenantIdI) {
	$sum = 0;
	$query="SELECT *  from (((select distinct debutLienId as noeuds from liens) 
			union (select distinct finLienId as noeuds from liens))as tableNoeud);";
	$queryResult = mysql_Query($query);
	$reseau = creerGraphe();
 
	while($row = mysql_fetch_assoc($queryResult)) {        
		$intervenantIdJ = $row["noeuds"];      
		if(strcmp($intervenantIdI,$intervenantIdJ)!=0) {
			list($chemin, $sumDist) = dijkstra($reseau, $intervenantIdI, $intervenantIdJ);
			$sum += $sumDist;
      }
	}
  
	return $sum;
}

/*
function dist($intervenantIdI,$intervenantIdJ) {
  $dist = 1; 
  //http://tel.archives-ouvertes.fr/docs/00/61/91/77/PDF/These_Nacim_Chikhi_v8.0.pdf

return $dist;
    
}
*/
?>
