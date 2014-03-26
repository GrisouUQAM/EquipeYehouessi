<?php
//http://rosettacode.org/wiki/Dijkstra's_algorithm
//a completer: modifier l'algorithme pour chercher plus qu'un chemin court
function dijkstra($graph_array, $source, $target) {
    $vertices = array();
    $neighbours = array();
	$sumDistance = 0;
    foreach ($graph_array as $edge) {
        array_push($vertices, $edge[0], $edge[1]);
        $neighbours[$edge[0]][] = array("end" => $edge[1], "cost" => $edge[2]);
    }
    $vertices = array_unique($vertices);
 
    foreach ($vertices as $vertex) {
        $dist[$vertex] = INF;
        $previous[$vertex] = NULL;
    }
 
    $dist[$source] = 0;
    $Q = $vertices;
    while (count($Q) > 0) {
        $min = INF;
        foreach ($Q as $vertex){
            if ($dist[$vertex] < $min) {
                $min = $dist[$vertex];
                $u = $vertex;
				$sumDistance = $min;
            }
        }
 
        $Q = array_diff($Q, array($u));
        if ($dist[$u] == INF or $u == $target) {
            break;
        }
 
        if (isset($neighbours[$u])) {
            foreach ($neighbours[$u] as $arr) {
                $alt = $dist[$u] + $arr["cost"];
                if ($alt < $dist[$arr["end"]]) {
                    $dist[$arr["end"]] = $alt;
                    $previous[$arr["end"]] = $u;
                }
            }
        }
    }
    $path = array();
    $u = $target;
    while (isset($previous[$u])) {
        array_unshift($path, $u);
        $u = $previous[$u];
    }
    array_unshift($path, $u);
    return array($path, $sumDistance);
}

function creerGraphe(){
	$graph = array();
	$query = "SELECT * FROM liens";
	$queryResult = mysql_query($query);

	while($row = mysql_fetch_assoc($queryResult)){
		array_push($graph, array($row["debutLien"], $row["finLien"], $row["poids"]));
	}

	return $graph;
}
?>