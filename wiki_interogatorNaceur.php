«<?php


set_time_limit(360);


$user = $_GET["user"];
$wikiUrl = "http://" . $_GET["wiki"];


$firstQueryUrl = $wikiUrl 
	. "/w/api.php?action=query&list=usercontribs&format=json&uclimit=500&ucuser=" 
	. $user . "&ucnamespace=1&ucprop=ids%7Ctitle%7Ccomment";


$firstQueryContent = getQueryContent($firstQueryUrl);


$objTalk = json_decode($firstQueryContent, true);
$queryTalk = $objTalk['query'];
$userTalks = $queryTalk['usercontribs'];


connectToDB();
insertTalks($userTalks, $user, $wikiUrl);


function insertTalks($userTalks, $user, $wikiUrl) {
	$commentPrecedant="";
	foreach ($userTalks as $talk) {
		$fixedTitle=mysql_real_escape_string($talk['title']);
		$query = "INSERT INTO grisou.page (pageId, titre) VALUES (".$talk['pageid'].", '".$fixedTitle ."')" ;
		$r=Mysql_Query($query);


		$query = "INSERT INTO grisou.user (userId, userName) VALUES (".$talk['userid'].", '".$talk['user'] ."')";
		Mysql_Query($query);


		$comment = $talk['comment'];


		$posDebutComment = strpos($comment, "/*");
		$posFinComment = strpos($comment, "*/");


		if(is_numeric($posDebutComment) AND is_numeric($posDebutComment)) { 
			$comment = substr($comment, $posDebutComment + 3, $posFinComment - $posDebutComment - 4); // on supprime les caracteres /* et */
		}


		if(strlen($comment) > 0 ) {
			$query = "INSERT INTO grisou.comments (pageId, comment) VALUES (".$talk['pageid'].", '".$comment ."')" ;
			$r=Mysql_Query($query);
		}
		$commentPrecedant=$comment;
	}
	insertRelatedUsers( $user,$wikiUrl);
}


function insertRelatedUsers($user, $wikiUrl) {
	$pages=getPageId();
	while($pageIdRecord = Mysql_fetch_array($pages)) {
		$pageId = $pageIdRecord["pageId"];
		//$compteur = 0;




		$secondQueryUrl = $wikiUrl . "/w/api.php?action=query&list=usercontribs&format=json&uclimit=500&ucuser=". $user . "&ucnamespace=1&indexpageids=&export=&pageids=". $pageId;


		$secondQueryContent = getQueryContent($secondQueryUrl);


		$comments=getCommentsByPageId($pageId);


		while($commentsRecord = Mysql_fetch_array($comments)) {




			$comment=$commentsRecord["comment"];




			// Mise en forme du comment pour correspondre au format du contenu de $secondQueryContent
			$contentComment = "== " . $comment . " ==";


			// Recherche de la position du comment
			$posDebut = strPos($secondQueryContent, $contentComment);


			if(is_numeric($posDebut)) { 
				$posFin = strPos($secondQueryContent, "</text>");
				$listeDiscussions = substr($secondQueryContent, $posDebut, $posFin - $posDebut);
			} else {
				$posDebut = strPos($secondQueryContent, $comment);


				if(is_numeric($posDebut)) { 
					$posFin = strPos($secondQueryContent, "</text>");
					$listeDiscussions = substr($secondQueryContent, $posDebut, $posFin - $posDebut);
				} else {
					 continue; 
				}
			}


			// On isole la discussion
			$discussion = substr($listeDiscussions, strlen($contentComment), strlen($listeDiscussions) - strlen($contentComment));
			$posFin = strPos($discussion, "==");
			$discussion = substr($discussion, 0, $posFin);
			$chaine2RetourChariot = substr($discussion,0,4);
			$chaine1RetourChariot = substr($discussion,0,2);


			$compteur=0;


			do {
				$pos2RetourChariot = strPos($discussion,$chaine2RetourChariot);
				$pos1RetourChariot = strPos($discussion,$chaine1RetourChariot);


				if(is_numeric($pos2RetourChariot)) { 
					if($pos1RetourChariot < $pos2RetourChariot) {
						$posRetourChariot = $pos1RetourChariot;
						$chaineRetourChariot = $chaine1RetourChariot;
					} else {
						$posRetourChariot = $pos2RetourChariot;
						$chaineRetourChariot = $chaine2RetourChariot;
					}
				} else if(is_numeric($pos1RetourChariot)) { 
					$posRetourChariot = $pos1RetourChariot;
					$chaineRetourChariot = $chaine1RetourChariot;
				} else {
					$posRetourChariot = "";
					$chaineRetourChariot = "";
				}


				if(is_numeric($posRetourChariot)) { 
					$discussion = substr($discussion,$posRetourChariot+strlen($chaineRetourChariot),strlen($discussion)-$posRetourChariot);
				} else {
					$discussion = "";		
				}


				if(strlen($discussion) == 0) 
					break;


				if($discussion[0] == ":") { 
					$estCreateur = 0;
				} else {
					$estCreateur = 1;
				}


				//niveau d'indentation
				$niveau = 0;


				if($estCreateur == 0) 
					while($discussion[$niveau] == ':') { 
						$niveau++;
					}




				$userDebut = strPos($discussion, "([[User talk:");


				if(is_numeric($userDebut)) { 
					$userDebut += 13;
					$userFin = strPos($discussion, "|talk");
					$actualUser = substr($discussion, $userDebut, $userFin - $userDebut);


					if($estCreateur) 
						$userCreateur = $actualUser;
					$fixedComment=mysql_real_escape_string($comment);
					$query = "INSERT INTO grisou.talk (pageId, comment, ordre, niveau, user)
						VALUES (".$pageId.", '".$fixedComment."', ".$compteur.", ".$niveau.", '".$actualUser."')";


					Mysql_Query($query);


					$discussion = substr($discussion, $userFin, strlen($discussion) - $userFin);
					$compteur++;		
				}


			} while(is_numeric($posRetourChariot));
		}
	}
}


$talks = getTalks($user);
if (!$talks) {
	$message  = "Invalid query: " . mysql_error() . "\n";
	$message .= "Whole query: " . $sql;
	die($message);
}


printUserTalks($talks, $user);
nbIntervenant($user);
liaisonIntervenantDiscussion($user);


function printUserTalks($talks, $user) {
	$result = "<h2>Discussions auxquelles <span style='color:blue;'><i>" . $user . "</i></span> a contribu&eacute;</h2>
      <p>Cliquez sur une ligne parmis les r&eacute;sultats afin de visualiser les utilisateurs qui ont int&eacute;ragis avec <i>" . $user . " </i>dans cette discussion.<p>
      <table id='tbl_result' width='100%' cellpadding='0' cellspacing='0' border='0'>
      <tr>            
      <td class='head'>Page ID</td>
      <td class='head'>Article</td>
      <td class='head'>Titre de la discussion</td>  
      <td class='head' style='text-align:right; padding:0;'>Cr&eacute;ateur</td>
      <td class='head' style='text-align:right; padding:0 0 0 30px;'>Nombre d'interventions</td>
      </tr>";




	while($talkRecord = Mysql_fetch_array($talks)) {
		$pageId = $talkRecord["pageId"];
		$pageTitle = $talkRecord["titre"];
		$comment = $talkRecord["comment"];
		$nbIntervention = $talkRecord["nbIntervention"];
		$ordre = $talkRecord["ordre"];
		$niveau = $talkRecord["niveau"];
		$isTalkCreator = "Non";


		if ($talkRecord["minOrdre"] == 0)
			$isTalkCreator = "Oui";


		$result .= "<tr class='res'>";
		$result .= "<td class='res'>" . $pageId . "</td>";
		$result .= "<td class='res'>" . $pageTitle . "</td>";
		$result .= "<td class='res'>" . $comment . "</td>";
		$result .= "<td class='res' style='text-align:right; padding:0;'>" . $isTalkCreator . "</td>";
		$result .= "<td class='res' style='text-align:right; padding:0 0 0 30px;'>" . $nbIntervention . "</td>";
		$result .= "</tr>";


		$result .= "<tr style='background:#f6f6f6;'>";
		$result .= "<td colspan='5' style='padding:0 20px'>";
		$result .= "<div class='expandable'>";
		$result .= "<p class='desc'>Ont particip&eacute; &agrave; cette discussion:</p>";
		$result .= "<table width='100%' cellpadding='0' cellspacing='0' style='padding-bottom:20px;'>";
		$result .= "<tr class='subRes'><td class='subRes'>";


		$subResult = "";


		$relatedUsers = getRelatedUsers($pageId, $comment, $user);


		$numRows = mysql_num_rows($relatedUsers);
		$counter = 0;


		while ($relatedUserRecord = Mysql_fetch_array($relatedUsers)) {
				$subResult .= $relatedUserRecord["user"];


				if (++$counter != $numRows)
					$subResult .= "&nbsp;&nbsp;|&nbsp;&nbsp;";
		}


		if ($subResult == "") 
			$subResult .= "Aucune interaction a eu lieu dans cette discussion.";


		$result .= $subResult; 


		$reply = getReply($user, $pageId, $comment);


		if($reply === false)
    		die("Erreur sql provenant de la fonction getReply(): " . mysql_error());


		if($nbIntervention == 1 AND $isTalkCreator == "Oui") {
			$result .= "</td></tr></table></div></td></tr>";
			continue;
		} else {
			if($nbIntervention == 1) {
				$utilisateur = "Utilisateur";
			} else {
				$utilisateur = "Utilisateurs";
			}


			$result .= "</td></tr><tr><td  colspan='5'>";
			$result .= "<p class='desc'>" . $utilisateur . " &agrave qui <i>" . $user . "</i> a r&eacute;pondu:</p>";
			$result .= "</td></tr><tr class='subRes'><td class='subRes'>";


			$i = $nbIntervention;


			while($replyRecord = Mysql_fetch_array($reply)) {
				$repliedUser = "";


				// va chercher le nom de l'utlisateur a qui le contributeur a repondu, pour cette reponse
				$repliedUser = isAReplyTo($replyRecord["pageId"], $replyRecord["comment"], $replyRecord["ordre"], $replyRecord["niveau"]);


				if($repliedUser == "") { // dans le cas ou le contributeur a repondu a la racine, on affiche le nom du createur de la discussion
					$result .= getCreateur($replyRecord["pageId"], $replyRecord["comment"]);
				} else {
					$result .= $repliedUser;
				}


				$i--;


				if ($i > 1) {
					$result .= "&nbsp;&nbsp;|&nbsp;&nbsp;";
				} else {
					$result .= " ";
				}
			}	
		}


		$result .= "</td></tr></table></div></td></tr>";
	}


	$result .= "</table>";


	print $result;		
}


function checkIfUserExist($user, $relatedUsers) {
	foreach ($relatedUsers as $existingUsers) {
		if ($user == $existingUsers -> user)
			return true;
	}


	return false;
}


function getPageId() {
	$sql = "SELECT pageId
	  FROM comments
	  GROUP BY pageId
	  ORDER BY pageId";


	return mysql_query($sql);
}


function getCommentsByPageId($pageId) {
	$sql = "SELECT comment
	  FROM comments
	  WHERE pageId=".$pageId."";


	return mysql_query($sql);
}


function getTalks($user) {
	$sql = "SELECT COUNT(talk.pageId) as nbIntervention, talk.pageId, talk.comment, talk.ordre, MIN(talk.ordre) as minOrdre, talk.niveau, page.titre 
	  FROM talk JOIN page ON (page.pageId = talk.pageId) WHERE user = '" . $user . "'" .
	  "GROUP BY talk.pageId, talk.comment, page.titre";


	return mysql_query($sql);
}


function getRelatedUsers($pageId, $comment, $user) {
	$sql = "SELECT DISTINCT user FROM talk WHERE pageId = '" . $pageId . "' AND comment = '" . $comment . "' AND user !=  '" . $user . "'";	
	return mysql_query($sql);
}


function getReply($user, $pageId, $comment) {
	$sql = "SELECT pageId, user, comment, ordre, niveau FROM talk WHERE user =  '" . $user . "' AND pageId = '" . $pageId . "' AND comment = '" . $comment . "' AND ordre != 0";


	return mysql_query($sql);
}


function isAReplyTo($pageId, $comment, $ordre, $niveau) {	    
	$sql = "SELECT top.user, top.ordre FROM (
			    SELECT  user, ordre, niveau FROM talk WHERE pageId = '" . $pageId . "' AND  comment = '" . $comment . "' AND  ordre < '" . $ordre . "') AS top
			WHERE  niveau = '" . ($niveau - 1) . "' 
			ORDER BY top.ordre DESC 
			LIMIT 1";


	$repliedUser = mysql_query($sql);


	if(false === $repliedUser) 
		die("Erreur sql provenant de la fonction isAReplyTo(): " . mysql_error());


	$repliedUserRecord = Mysql_fetch_array($repliedUser);


	if (count($repliedUser)) {
		return $repliedUserRecord["user"];
	} else {
		return "";
	}
}


function getCreateur($pageId, $comment) {	    
	$sql = "SELECT user FROM talk WHERE pageId = '" . $pageId . "' AND comment = '" . $comment . "' AND ordre = 0";


	$createur = mysql_query($sql);


	if(false === $createur) 
		die("Erreur sql provenant de la fonction getCreateur(): " . mysql_error());


	$createurRecord = Mysql_fetch_array($createur);


	return $createurRecord["user"];
}


function connectToDB() {
    $hostname = "localhost";
    $database = "grisou";
    $username = "root";
    $password = "";


    $link = mysql_connect($hostname, $username, $password);


    if (!$link) 
   		die('La connection &agrave; la bd a &eacute;chou&eacute;: ' . mysql_error());


    $db_selected = mysql_select_db($database, $link);


    if (!$db_selected) 
   		die ('Impossible de s&eacute;lectionn&eacute; la bd: ' . mysql_error());


    // on vide les tables à chaque nouvelle connection
	mysql_query('TRUNCATE TABLE page;');
	mysql_query('TRUNCATE TABLE talk;');
	mysql_query('TRUNCATE TABLE user;');
	mysql_query('TRUNCATE TABLE comments;');
}


function getQueryContent($queryUrl) {
	return file_get_contents($queryUrl, true);
}


//Fonction qui calcule le nombre d'intervenants dans les discussions qu'un contributeur a créé
function nbIntervenant($user) {	
	$aff = "<br><br><h2>Nombre d'intervenants dans chaque discussion que <span style='color:blue;'><i>" . $user . "</i></span> a cr&eacute;&eacute;.</h2>
	  <p>
      <table id='tbl_result' width='100%' cellpadding='0' cellspacing='0'>
      <tr>            
      <td class='head' style='width:200px;'>Titre de la discussion</td>
      <td class='head' style='width:100px;'>Nombre d'intervenants</td>
	  </tr>";
 
 	$commentCreatedByUser = getCommentCreatedByUser($user);
 	
	if (!$commentCreatedByUser) {
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}


	$intervenantExist = FALSE;


	while($commentCreatedByUserRecord = Mysql_fetch_array($commentCreatedByUser)) {
		$nbIntervenant = getNbParticipant($user, $commentCreatedByUserRecord["pageId"], $commentCreatedByUserRecord["comment"]);


		$aff .= "<tr class='res'>";
		$aff .= "<td class='res'>" .$commentCreatedByUserRecord['comment']."</td>";
		$aff .= "<td class='res'>" . ($nbIntervenant) ."</td>";
		$aff .= "</tr>";
		$intervenantExist = TRUE;
	}


	if(!$intervenantExist) {
		$aff .= "<tr class='res'><td class='res' colspan='2'>Aucun r&eacute;sultat.</td></tr>";
	}


	$aff .= "</table>";
	print $aff;
}


function getCommentCreatedByUser($user) {
	$sql = "SELECT pageId, comment, user FROM `talk` WHERE user = '" . $user . "' AND ordre = 0";
	return mysql_query($sql);
}


function getNbParticipant($user, $pageId, $comment) {
	$sql = "SELECT user FROM talk WHERE user != '" . $user . "' AND pageId = '" . $pageId . "' AND comment = '" . $comment . "'";


	$result = mysql_query($sql);


	return mysql_num_rows($result);
}


//Trouver si un invidu est lie avec les memes intervenants dans plus d'une discussion
function liaisonIntervenantDiscussion($user) {
	$aff = "<br><br><h2>Liste des intervenants avec lesquels <span style='color:blue;'><i>" . $user . "</i></span> est li&eacute; dans plus d'un article.</h2>
      <table id='tbl_result' width='100%' cellpadding='0' cellspacing='0'>
      <tr>            
      <td class='head' style='width:200px;'>Nom de l'intervenant</td>
      <td class='head' style='width:100px;'>Nombre d'articles</td>
	  </tr>";


	$sql = "SELECT DISTINCT user, count( DISTINCT pageid ) as nbPageId FROM talk WHERE user != '" . $user . "' GROUP BY user";


	$resulat= mysql_query($sql);


	if (!$resulat) {
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}


	$intervenantExist = FALSE;


	while($enrg=Mysql_fetch_array($resulat)) {
		$aff .= "<tr class='res'>";


		if ($enrg['nbPageId'] > 1) {
			$aff .= "<td class='res'>" .$enrg['user']."</td>";
			$aff .= "<td class='res'>" .$enrg['nbPageId'] ."</td>";
			$aff .= "</tr>";
			$intervenantExist = TRUE;
		}
	}


	if(!$intervenantExist) {
		$aff .= "<tr class='res'><td class='res' colspan='2'>Aucun r&eacute;sultat.</td></tr>";
	}


	$aff .= "</table>";
	print $aff;
}


?>

