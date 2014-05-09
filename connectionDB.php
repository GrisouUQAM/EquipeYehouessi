<?php

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
}
?>
