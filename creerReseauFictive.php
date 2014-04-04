<?php





function creerReseauFictif($nbIntervenantFictif) {
    //creerTableNeccessaire();
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
                        $discusId=0; //pageId de la discussion
			if(($lienCree[$intervenantAleatoire] == 0)){ 
			     $query = "insert into grisou.liens(lienId,discussionId,debutLienId,debutLien,finLienId,finLien,poids,noSection,noArchive) values(".$j.",".$discusId.",".$i.",".$concat1.",".$intervenantAleatoire.",".$concat2.",".$un.",0,0);";
		         Mysql_Query($query);
				 $j= $j+1;
			}
			 $lienCree[$intervenantAleatoire] = 1; 
		 }	    
		 
	}
}







?>