<?php
include_once("inc/cricket_lib.php");
include_once("inc/db_functions.php");
include_once("inc/form_lib.php");
session_start();
//print_r($_SESSION);
if(isset($_SESSION['m']['teamid'])&&$_SESSION['m']['teamid']<>""){
	dbToArray("call deleteSelectedPlayers(".$_SESSION['m']['teamid'].")");
}
$d = dbToArray("call updateUserLog(".$_SESSION['userid'].",'".$_SESSION['sessionid']."');");
session_unset();
session_destroy();
redirect_rel('index.php');
?>