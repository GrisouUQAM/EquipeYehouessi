<?php

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
	$queryNoeudI = mysql_query("SELECT *  from (((select distinct debutLienId as noeuds from liens ) union (select distinct finLienId as noeuds from liens))as tableNoeud) LIMIT $i, 1;");
	$fetchNoeudI = mysql_fetch_assoc($queryNoeudI);
	$noeudI = $fetchNoeudI["noeuds"];		
	for($j = 0; $j < mysql_num_rows($queryNom); $j++){
            $queryNoeudJ = mysql_query("SELECT *  from (((select distinct debutLienId as noeuds from liens ) union (select distinct finLienId as noeuds from liens))as tableNoeud) LIMIT $j, 1;");
	    $fetchNoeudJ = mysql_fetch_assoc($queryNoeudJ);
	    $noeudJ = $fetchNoeudJ["noeuds"];		
	    $queryPoids = mysql_query("SELECT * FROM liens WHERE (debutLienId='$noeudI' AND finLienId='$noeudJ') OR (debutLienId='$noeudJ' AND finLienId='$noeudI');");
	    $fetchPoids = mysql_fetch_assoc($queryPoids);
	    if(mysql_num_rows($queryPoids)>0){
		$graphe[$i][$j] = 1;//$fetchPoids["poids"];
	    } else {
		$graphe[$i][$j] = 0;
	    }
	}
    }
    $fw = new FloydWarshall($graphe, $nomNoeuds);
    return $fw;
}

function numRowV($rowV){
    $query = "SELECT *  from (((select distinct debutLienId as noeuds from liens ) 
			union (select distinct finLienId as noeuds from liens))as tableNoeud);";
    $queryResult = Mysql_Query($query);
    $k = 0;
    while($row = mysql_fetch_assoc($queryResult)){
	if($rowV = $row["noeuds"]){
            return $k;
	}
	$k++;
    }
    return 0;
}

?>