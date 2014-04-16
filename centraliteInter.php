<?php
function centraliteInter($idIntervenant, $nbNoeud) {
	if(numPaths($idIntervenant) <= 0){
		return 0;
	} else {
		$degree = numPaths($idIntervenant)/(($nbNoeud-1)*($nbNoeud-2));
		return $degree;
	}
}

function numPaths($intervenantIdV){
	//soit $numP le nombre de chemins les plus courts entre deux noeuds
	//et $numI le nombre de chemins les plus courts entre deux noeuds par $intervenantIdV
	$numP = 0;
	$numI = 0;
	
	$query = "SELECT *  from (((select distinct debutLienId as noeuds from liens ) 
			union (select distinct finLienId as noeuds from liens))as tableNoeud);";
	$queryResult = Mysql_Query($query);
	$reseau = creerGraphe();
	$v = numRowV($intervenantIdV);
	
	
	for($i = 0; $i < mysql_num_rows($queryResult); $i++){
		for($j = 0; $j < mysql_num_rows($queryResult); $j++){
			if($i != $j){
				$numP += $reseau->get_pathNum($i, $j);
				$numI += ($reseau->get_pathNum($i, $v) + $reseau->get_pathNum($v, $j));
			}
		}
	}
	
	$num = $numP/$numI;
	return $num;
}
?>
