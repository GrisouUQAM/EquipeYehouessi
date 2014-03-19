<?php

function centraliteInter($idIntervenant, $nbNoeud) {
	$degree = numPaths($idIntervenant)/(($nbNoeud-1)*($nbNoeud-2));
	return $degree;
}

function numPaths($intervenantIdV){
	//soit $numP le nombre de chemins les plus courts entre deux noeuds
	//et $numI le nombre de chemins les plus courts entre deux noeuds par $intervenantIdV
	$numP = 0;
	$numI = 0;
	
	$query = "SELECT *  from (((select distinct debutLienId as noeuds from liens ) 
			union (select distinct finLienId as noeuds from liens))as tableNoeud);";
	$queryResult = Mysql_Query($query);
	
	while($rowI = mysql_fetch_assoc($queryResult)){
		$intervenantIdI = $rowI["noeuds"];
		while($rowJ = mysql_fetch_assoc($queryResult)){
			$intervenantIdJ = $rowJ["noeuds"];
			if(strcmp($intervenantIdI,$intervenantIdJ)!=0){
				$numP += compterPlusCourtChemins($intervenantIdI,$intervenantIdJ);
				$numI += compterPlusCourtCheminsV($intervenantIdI,$intervenantIdJ,$intervenantIdV);
			}
		}
	}
	
	$num = $numI/$numP;
	return $num;
}

function compterPlusCourtChemins($i, $j){
	//a completer
	$n = 1;
	return $n;
}

function compterPlusCourtCheminsV($i, $j, $v){
	//a completer
	$n = 1;
	return $n;
}
?>
