<?php
function centraliteProxi($intervenantIdI, $nbNoeud) {
	// calcul de la centralite
	if(sumDist($intervenantIdI) <= 0){
		return 0;
	} else {
		$degree = ($nbNoeud-1)/sumDist($intervenantIdI);
		return $degree;
	}
}

function sumDist($intervenantIdI) {
	$sum = 0;
	$query = "SELECT *  from (((select distinct debutLienId as noeuds from liens ) 
			union (select distinct finLienId as noeuds from liens))as tableNoeud);";
	$queryResult = Mysql_Query($query);
	$reseau = creerGraphe();
	$i = numRowV($intervenantIdI);
	
	for($j = 0; $j < mysql_num_rows($queryResult); $j++){
		if($i != $j){
			$sum += $reseau->get_distance($i, $j);
		}
	}
	
	return $sum;
}
?>
