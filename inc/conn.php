<?php
/************************************************************************
*	THIS FILE IS USED TO CONNECT TO DATABASE
************************************************************************/
require_once "config.php";

$db = mysql_connect($hostname, $username, $pword) or die("Unable to connect to mysql");
$selected = mysql_select_db($dbname,$db) or die("Could not select \"".$dbname."");

function db_connect($dbname){
global $hostname, $username, $pword;
	
	$db = mysql_connect($hostname, $username, $pword) or die("Unable to connect to mysql");
	$selected = mysql_select_db($dbname,$db) or die("Could not select " . $dbname);
	return $db;
}

function dbi_connect($dbname)
{
	global $hostname, $username, $pword;
	if($dbname=='me03hot13066com46198_cricket_wizard_sponsors') { $username = 'me03h_sponsors'; } else { $username = 'me03h_cricket_wi'; }
	$mysqli_db = mysqli_connect($hostname, $username, $pword) or die("Unable to connect to mysqli.");
	$selected = mysqli_select_db($mysqli_db,$dbname)or die("Could not select db" . $dbname);
	return $mysqli_db;
}

function db_close(){
	mysql_close();
}

?>
