<?php
include('floyd.php');
function centraliteInter($idIntervenant, $nbNoeud) {
	if(numPaths($idIntervenant) <= 0){
		return 0;
	} else {
		$degree = numPaths($idIntervenant)/(($nbNoeud-1)*($nbNoeud-2));
		return $degree;
	}
}

function creerGraphe(){
	$nomNoeuds = array();
	$graphe = array();
	$query = "SELECT *  from (((select distinct debutLienId as noeuds from liens ) union (select distinct finLienId as noeuds from liens))as tableNoeud);";
	$queryNom = Mysql_Query($query);

	while($rowI = mysql_fetch_assoc($queryNom)){
		array_push($nomNoeuds, $rowI["noeuds"]);
	}

	for($i = 0; $i < mysql_num_rows($queryNom); $i++){
		$graphe[$i] = array();
		for($j = 0; $j < mysql_num_rows($queryNom); $j++){
			$queryPoids = Mysql_Query("SELECT * FROM liens WHERE debutLienId=".$i." AND finLienId=".$j.";");
			$queryPoidsResult = mysql_fetch_assoc($queryPoids);
			if(mysql_num_rows($queryPoids) == 0){
				$graphe[$i][$j] = 0;
			} else {
				$graphe[$i][$j] = $queryPoidsResult["poids"];
			}
		}
	}

	$fw = new FloydWarshall($graphe, $nomNoeuds);
	return $fw;
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
	
	for($i = 0; $i < mysql_num_rows($queryResult); $i++){
		for($j = 0; $j < mysql_num_rows($queryResult); $j++){
			if($i != $j){
				$numP += $reseau->get_pathNum($i, $j);
				$numI += $reseau->get_pathNum($i, $intervenantIdV) + $reseau->get_pathNum($intervenantIdV, $j);
			}
		}
	}
	
	$num = $numP/$numI;
	return $num;
	
	//PHP n'aime pas beaucoup les double boucles while avec mysql_fetch_assoc
	/*$query = "SELECT *  from (((select distinct debutLienId as noeuds from liens ) 
			union (select distinct finLienId as noeuds from liens))as tableNoeud);";
	$queryResult1 = Mysql_Query($query);
	$queryResult2 = Mysql_Query($query);
	$reseau = creerGraphe();
	
	while($rowI = mysql_fetch_assoc($queryResult)){
		$intervenantIdI = $rowI["noeuds"];
		mysql_data_seek($queryResult2, 0);
		while($rowJ = mysql_fetch_assoc($queryResult2)){
			$intervenantIdJ = $rowJ["noeuds"];
			if(strcmp($intervenantIdI,$intervenantIdJ)!=0){
				$numP = $reseau->get_pathNum($rowI["noeuds"], $rowJ["noeuds"]);
				$numI = $reseau->get_pathNum($rowI["noeuds"], $intervenantIdV) + $reseau->get_pathNum($intervenantIdV, $rowJ["noeuds"]);
			}
		}
	}*/
}

/*function numPaths($intervenantIdV){
	//soit $numP le nombre de chemins les plus courts entre deux noeuds
	//et $numI le nombre de chemins les plus courts entre deux noeuds par $intervenantIdV
	$numP = 0;
	$numI = 0;
	
	$query = "SELECT *  from (((select distinct debutLienId as noeuds from liens ) 
			union (select distinct finLienId as noeuds from liens))as tableNoeud);";
	$queryResult = Mysql_Query($query);
	$reseau = creerGraphe();
	
	while($rowI = mysql_fetch_assoc($queryResult)){
		$intervenantIdI = $rowI["noeuds"];
		while($rowJ = mysql_fetch_assoc($queryResult)){
			$intervenantIdJ = $rowJ["noeuds"];
			if(strcmp($intervenantIdI,$intervenantIdJ)!=0){
			//a completer: modifier l'algorithme de dijkstra pour chercher plus qu'un chemin court
				list($chemin, $sumDist) = dijkstra($reseau, $intervenantIdI, $intervenantIdJ);
				$numP += 1;
				$numI += compterPlusCourtCheminsV($reseau,$intervenantIdI,$intervenantIdJ,$intervenantIdV);
			}
		}
	}
	
	$num = $numI/$numP;
	return $num;
}

function compterPlusCourtCheminsV($reseau, $i, $j, $v){
	//a completer
	list($cheminV1, $sumDistV1) = dijkstra($reseau, $i, $v);
	list($cheminV2, $sumDistV2) = dijkstra($reseau, $v, $j);
	$n = 1;
	return $n;
}*/
?>
