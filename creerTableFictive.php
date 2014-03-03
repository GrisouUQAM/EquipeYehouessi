<?php

intervenantFictif(50);


function intervenantFictif($nbIntervenantFictif) {
    creerTableNeccessaire();
    $lienCree = array_fill ( 0 , $nbIntervenantFictif,0);
   
    for($i = 0; $i<$nbIntervenantFictif;$i++) {
	$concat = "I".$i;
	$pare='"';
	$concat = $pare.$concat.$pare;
    $zero = 0;	
         $query = "insert into grisou.intervenants(intervenantId,intervenantName,intervenantAuteurArticle) values(".$i.", ".$concat.",".$zero.");";
	     Mysql_Query($query);
    }   
    $j=0;
    for ($i=0; $i<$nbIntervenantFictif;$i++) {
	     for ($m=0; $m<$nbIntervenantFictif;$m++) {
		     $lienCree[$m] = 0;
         }
	     $lienCree[$i] = 1;		 
	     $nbLienACreer = rand(0,($nbIntervenantFictif-1));
		
         for ($k=0; $k<$nbLienACreer;$k++) {
		     $intervenantAleatoire = rand(0, ($nbIntervenantFictif-1));			
		    $un = 1;
			$concat1 = "I".$i;
			$concat2 = "I".$intervenantAleatoire;
	        $concat1 = $pare.$concat1.$pare;
			$concat2 = $pare.$concat2.$pare;
			if(($lienCree[$intervenantAleatoire] == 0)){ 
			     $query = "insert into grisou.liens(lienId,debutLien,finLien,poids) values(".$j.",".$concat1.",".$concat2.",".$un.");";
		         Mysql_Query($query);
				 $j= $j+1;
			}
			 $lienCree[$intervenantAleatoire] = 1; 
		 }	    
		 
	}
}


function creerTableNeccessaire(){
$query = "DROP TABLE IF EXISTS `intervenants`";
Mysql_Query($query);
$query = "CREATE TABLE `intervenants` (
`intervenantId` int(11) NOT NULL,
`intervenantName` varchar(100) NOT NULL,
`intervenantAuteurArticle` int(11) not null,
PRIMARY KEY (`intervenantId`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;";
Mysql_Query($query);

$query = "DROP TABLE IF EXISTS `liens`";
Mysql_Query($query);
$query = "CREATE TABLE `liens`(
`lienId` int(11) NOT NULL,
`debutLien` varchar(100) NOT NULL,
`finLien` varchar(100) NOT NULL,
`poids` int(11) NOT NULL,
PRIMARY KEY (`lienId`),
CHECK (debutLien != finLien),
check (debutLien IN (SELECT intervenantId from intervenants)) ,
check (finLien IN (SELECT intervenantId from intervenants))
)ENGINE=InnoDB DEFAULT CHARSET=latin1;"; 
Mysql_Query($query);
}

?>