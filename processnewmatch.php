<?php
session_start();
/********************
Includes
********************/
include_once("inc/db_functions.php");
include_once("inc/form_lib.php");
include_once("inc/cricket_lib.php");
validate_usersession();
printcss();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>The Cricket Wizard</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<script language="javascript">
var secs;
var timerID = null;
var timerRunning = false;
var delay = 1000;
function InitializeTimer(){
    // Set the length of the timer, in seconds
    secs = 5
    StopTheClock()
    StartTheTimer()
}
function StopTheClock(){
    if(timerRunning)
        clearTimeout(timerID)
    timerRunning = false
}
function StartTheTimer(){
    if (secs==0) {
        StopTheClock()
		document.location='matches.php';
    }
    else {
        self.status = secs
        secs = secs - 1
        timerRunning = true
        timerID = self.setTimeout("StartTheTimer()", delay)
    }
}
</script>
<body onLoad="InitializeTimer()">
<?php
//print_r($_SESSION);
/********************
Validation
********************/
$user = getUserDetails($_SESSION['userid']);
$colour = "#F7C200";
$text = "Fixtures";
if($user->role != "Team Admin"){
	redirect_rel('home.php');
}
$sql = "call getSelectedPlayers(".$_SESSION['m']['teamid'].",1)";
$result = dbToArray($sql);
//set_variables($result);
$matchid = 0;
$dbdate = dbDateDisplay($_SESSION['m']['date'],'Y-m-d');

/****************************************
Batting and Bowling orders
****************************************/
//if one dayer
if($_SESSION['m']['mt']==1){
	//get the batting and bowling orders.  This is importane for when we display the data on the scorecard.
	$bat_order = explode(" ",$_SESSION['m']["battingorder"]);
	$bowling_order = explode(" ",$_SESSION['m']["bowlingorder"]);
	/*
	//$_SESSION['m']["bowlingorder"] ="";
	$i=7;
	$bowlorder = array_search($i,$bowling_order);
	echo "<br>";
	echo $bowlorder;
	echo (!is_numeric($bowlorder)) ? "here" : "here1";
			
	$bowlingid = 0;
	$bowlorder = array_search($i,$bowling_order);
	($_SESSION['m']['maidens'.$i]=='' ) ? $_SESSION['m']['maidens'.$i]='10101' : $_SESSION['m']['maidens'.$i];
	($_SESSION['m']['wides'.$i]=='' ) ? $_SESSION['m']['wides'.$i]='10101' : $_SESSION['m']['wides'.$i];
	($_SESSION['m']['noballs'.$i]=='' ) ? $_SESSION['m']['noballs'.$i]='10101' : $_SESSION['m']['noballs'.$i];
	$sqlbowl = "call insertUpdateBowling(".$bowlingid.",".$_SESSION['m']['maidens'.$i].",".$_SESSION['m']['runsconceded'.$i].",".$_SESSION['m']['wickets'.$i].",".$_SESSION['m']['wides'.$i].",".$_SESSION['m']['noballs'.$i].",".$_SESSION['m']['overs'.$i].",".$bowlorder.");";
	$resultbowl =dbToArray($sqlbowl);
				
	print_r($_SESSION['m']["bowlingorder"]);
	echo "<br>";
	
	echo $bowlorder;
	*/
	
}
else {
	//if two dayer
	//Need to remove double blank space between 1st innings and 2nd Innings data
	$_SESSION['m']["battingorder"] = str_replace("  "," ",$_SESSION['m']["battingorder"]);
	$_SESSION['m']["bowlingorder"] = str_replace("  "," ",$_SESSION['m']["bowlingorder"]);

	$bat_order_data = explode(" ",$_SESSION['m']["battingorder"]);
	$bowling_order_data = explode(" ",$_SESSION['m']["bowlingorder"]);	

 	//slice array to get first innings order
 	$bat_order = array_slice($bat_order_data,0,$_SESSION['m']['numplayers']);
	$bowling_order = array_slice($bowling_order_data,0,$_SESSION['m']['numplayers']);
	
	//slice array to get second innings order 
	$bat_order2 = array_slice($bat_order_data,$_SESSION['m']['numplayers'],$_SESSION['m']['numplayers']);
 	$bowling_order2 = array_slice($bowling_order_data,$_SESSION['m']['numplayers'],$_SESSION['m']['numplayers']);
}
//die();
/************************************************
Insert Match
*************************************************/
$sql1 = "call insertUpdateMatch(".$matchid.",'".$_SESSION['m']['teamid']."', '".addslashes($_SESSION['m']['opponent'])."', '".$_SESSION['m']['compid']."', '".addslashes($_SESSION['m']['venue'])."',
							'".$_SESSION['m']['gradeid']."', '".$dbdate."', '".$_SESSION['m']['resultid']."', '".addslashes($_SESSION['m']['summary'])."', '".addslashes($_SESSION['m']['season'])."');"; 
$result1 =dbToArray($sql1);
$matchid = $result1[1][1]['matchid'];
$count = count($result);

for($i=1; $i<=$count; $i++){
	//insert batting details
	if($_SESSION['m']['mt']==1){
		if($_SESSION['m']["runs".$i] != ''){
			$battingid = 0;
			$batorder = array_search($i,$bat_order);
			(!is_numeric($batorder)) ? $batorder=$i : $batorder;
			($_SESSION['m']['balls'.$i]=='' ) ? $_SESSION['m']['balls'.$i]='10101' : $_SESSION['m']['balls'.$i];
			($_SESSION['m']['fours'.$i]=='' ) ? $_SESSION['m']['fours'.$i]='10101' : $_SESSION['m']['fours'.$i];
			($_SESSION['m']['sixes'.$i]=='' ) ? $_SESSION['m']['sixes'.$i]='10101' : $_SESSION['m']['sixes'.$i];
			$sqlbat = "call insertUpdateBatting(".$battingid.",".$_SESSION['m']['runs'.$i].",".$_SESSION['m']['balls'.$i].",'".$_SESSION['m']['dismissal'.$i]."',".$_SESSION['m']['fours'.$i].",".$_SESSION['m']['sixes'.$i].",".$batorder.");";
			$resultbat =dbToArray($sqlbat);
			$battingid = $resultbat[1][1]['battingid'];
			if($_SESSION['m']["runs".$i] >=100){
				//insert to honours
				$sqlh = "call insertHonoursPerformance(".$_SESSION['m']['playerid'.$i].",".$result1[1][1]['matchid'].",'".$_SESSION['m']["runs".$i]."',0,0,".$_SESSION['m']['teamid'].",".$_SESSION['m']['dismissal'.$i].",".$battingid.");";
				$resulth =dbToArray($sqlh);
			}
		}
		else{
			$battingid = 0;
		}
		//insert bowling details
		if($_SESSION['m']["overs".$i] != ''){
			$bowlingid = 0;
			$bowlorder = array_search($i,$bowling_order);
			(!is_numeric($bowlorder)) ? $bowlorder=$i : $bowlorder;
			($_SESSION['m']['maidens'.$i]=='' ) ? $_SESSION['m']['maidens'.$i]='10101' : $_SESSION['m']['maidens'.$i];
			($_SESSION['m']['wides'.$i]=='' ) ? $_SESSION['m']['wides'.$i]='10101' : $_SESSION['m']['wides'.$i];
			($_SESSION['m']['noballs'.$i]=='' ) ? $_SESSION['m']['noballs'.$i]='10101' : $_SESSION['m']['noballs'.$i];
			$sqlbowl = "call insertUpdateBowling(".$bowlingid.",".$_SESSION['m']['maidens'.$i].",".$_SESSION['m']['runsconceded'.$i].",".$_SESSION['m']['wickets'.$i].",".$_SESSION['m']['wides'.$i].",".$_SESSION['m']['noballs'.$i].",".$_SESSION['m']['overs'.$i].",".$bowlorder.");";
			$resultbowl =dbToArray($sqlbowl);
			$bowlingid = $resultbowl[1][1]['bowlingid'];
			if($_SESSION['m']["wickets".$i] >=6){
				//insert to honours
				$sqlh = "call insertHonoursPerformance(".$_SESSION['m']['playerid'.$i].",".$result1[1][1]['matchid'].",0,".$_SESSION['m']["wickets".$i].",".$_SESSION['m']["runsconceded".$i].",".$_SESSION['m']['teamid'].",0,".$bowlingid.");";
				$resulth =dbToArray($sqlh);
			}
		}
		else{
			$bowlingid = 0;
		}
		//insert fielding details
		if($_SESSION['m']["catches".$i] != '' || $_SESSION['m']["stumpings".$i] != '' || $_SESSION['m']["byes".$i] != ''){
			$fieldingid = 0;
			($_SESSION['m']['catches'.$i]=='' ) ? $_SESSION['m']['catches'.$i]='0' : $_SESSION['m']['catches'.$i];
			($_SESSION['m']['stumpings'.$i]=='' ) ? $_SESSION['m']['stumpings'.$i]='0' : $_SESSION['m']['stumpings'.$i];
			($_SESSION['m']['byes'.$i]=='' ) ? $_SESSION['m']['byes'.$i]='0' : $_SESSION['m']['byes'.$i];
			$sqlf =  "call insertUpdateFielding(".$fieldingid.",".$_SESSION['m']['catches'.$i].",".$_SESSION['m']['stumpings'.$i].",".$_SESSION['m']["byes".$i].");";
			$resultf =dbToArray($sqlf);
			$fieldingid = $resultf[1][1]['fieldingid'];
		}
		else{
			$fieldingid = 0;
		}

		//insert performance
		$performanceid = 0;
		$sqlp = "call insertUpdatePerformance(".$performanceid.",".$_SESSION['m']['playerid'.$i].",".$matchid.",".$battingid.",".$bowlingid.",".$fieldingid.",1);";
		$resultp =dbToArray($sqlp);
		
		$ten = dbToArray("call checkWickets10(".$matchid.",".$_SESSION['m']['playerid'.$i].");");		
	}	
	/**********************************
	Two dayer
	**********************************/
	else if($_SESSION['m']['mt']==2){
		if($_SESSION['m']["runs".$i] != ''){
			$battingid = 0;
			$batorder = array_search($i,$bat_order);
			(!is_numeric($batorder)) ? $batorder=$i : $batorder;
			($_SESSION['m']['balls'.$i]=='' ) ? $_SESSION['m']['balls'.$i]='10101' : $_SESSION['m']['balls'.$i];
			($_SESSION['m']['fours'.$i]=='' ) ? $_SESSION['m']['fours'.$i]='10101' : $_SESSION['m']['fours'.$i];
			($_SESSION['m']['sixes'.$i]=='' ) ? $_SESSION['m']['sixes'.$i]='10101' : $_SESSION['m']['sixes'.$i];
			$sqlbat = "call insertUpdateBatting(".$battingid.",".$_SESSION['m']['runs'.$i].",".$_SESSION['m']['balls'.$i].",'".$_SESSION['m']['dismissal'.$i]."',".$_SESSION['m']['fours'.$i].",".$_SESSION['m']['sixes'.$i].",".$batorder.");";
			$resultbat =dbToArray($sqlbat);
			$battingid = $resultbat[1][1]['battingid'];
			if($_SESSION['m']["runs".$i] >=100){
				//insert to honours
				$sqlh = "call insertHonoursPerformance(".$_SESSION['m']['playerid'.$i].",".$result1[1][1]['matchid'].",'".$_SESSION['m']["runs".$i]."',0,0,".$_SESSION['m']['teamid'].",".$_SESSION['m']['dismissal'.$i].",".$battingid.");";
				$resulth =dbToArray($sqlh);
			}
		}
		else{
			$battingid = 0;
		}
		if($_SESSION['m']["runs2".$i] != ''){
			$battingid2 = 0;
			$batorder2 = array_search($i,$bat_order2);
			(!is_numeric($batorder2)) ? $batorder2=$i : $batorder2;
			($_SESSION['m']['balls2'.$i]=='' ) ? $_SESSION['m']['balls2'.$i]='10101' : $_SESSION['m']['balls2'.$i];
			($_SESSION['m']['fours2'.$i]=='' ) ? $_SESSION['m']['fours2'.$i]='10101' : $_SESSION['m']['fours2'.$i];
			($_SESSION['m']['sixes2'.$i]=='' ) ? $_SESSION['m']['sixes2'.$i]='10101' : $_SESSION['m']['sixes2'.$i];
			$sqlbat = "call insertUpdateBatting(".$battingid2.",".$_SESSION['m']['runs2'.$i].",".$_SESSION['m']['balls2'.$i].",'".$_SESSION['m']['dismissal2'.$i]."',".$_SESSION['m']['fours2'.$i].",".$_SESSION['m']['sixes2'.$i].",".$batorder2.");";
			$resultbat =dbToArray($sqlbat);
			$battingid2 = $resultbat[1][1]['battingid'];
			if($_SESSION['m']["runs2".$i] >=100){
				//insert to honours
				$sqlh = "call insertHonoursPerformance(".$_SESSION['m']['playerid'.$i].",".$result1[1][1]['matchid'].",'".$_SESSION['m']["runs2".$i]."',0,0,".$_SESSION['m']['teamid'].",".$_SESSION['m']['dismissal2'.$i].",".$battingid2.");";
				$resulth =dbToArray($sqlh);
			}
		}
		else{
			$battingid2 = 0;
		}
		//insert bowling details
		if($_SESSION['m']["overs".$i] != ''){
			$bowlingid = 0;
			$bowlorder = array_search($i,$bowling_order);
			(!is_numeric($bowlorder)) ? $bowlorder=$i : $bowlorder;
			($_SESSION['m']['maidens'.$i]=='' ) ? $_SESSION['m']['maidens'.$i]='10101' : $_SESSION['m']['maidens'.$i];
			($_SESSION['m']['wides'.$i]=='' ) ? $_SESSION['m']['wides'.$i]='10101' : $_SESSION['m']['wides'.$i];
			($_SESSION['m']['noballs'.$i]=='' ) ? $_SESSION['m']['noballs'.$i]='10101' : $_SESSION['m']['noballs'.$i];
			$sqlbowl = "call insertUpdateBowling(".$bowlingid.",".$_SESSION['m']['maidens'.$i].",".$_SESSION['m']['runsconceded'.$i].",".$_SESSION['m']['wickets'.$i].",".$_SESSION['m']['wides'.$i].",".$_SESSION['m']['noballs'.$i].",".$_SESSION['m']['overs'.$i].",".$bowlorder.");";
			$resultbowl =dbToArray($sqlbowl);
			$bowlingid = $resultbowl[1][1]['bowlingid'];
			$bowlorder = $bowlorder +1;
			if($_SESSION['m']["wickets".$i] >=6){
				//insert to honours
				$sqlh = "call insertHonoursPerformance(".$_SESSION['m']['playerid'.$i].",".$result1[1][1]['matchid'].",0,".$_SESSION['m']["wickets".$i].",".$_SESSION['m']["runsconceded".$i].",".$_SESSION['m']['teamid'].",0,".$bowlingid.");";
				$resulth =dbToArray($sqlh);
			}		
		}
		else{
			$bowlingid = 0;
		}
		//insert bowling details
		if($_SESSION['m']["overs2".$i] != ''){
			$bowlingid2 = 0;
			$bowlorder2 = array_search($i,$bowling_order2);
			(!is_numeric($bowlorder2)) ? $bowlorder2=$i : $bowlorder2;
			($_SESSION['m']['maidens2'.$i]=='' ) ? $_SESSION['m']['maidens2'.$i]='10101' : $_SESSION['m']['maidens2'.$i];
			($_SESSION['m']['wides2'.$i]=='' ) ? $_SESSION['m']['wides2'.$i]='10101' : $_SESSION['m']['wides2'.$i];
			($_SESSION['m']['noballs2'.$i]=='' ) ? $_SESSION['m']['noballs2'.$i]='10101' : $_SESSION['m']['noballs2'.$i];
			$sqlbowl = "call insertUpdateBowling(".$bowlingid2.",".$_SESSION['m']['maidens2'.$i].",".$_SESSION['m']['runsconceded2'.$i].",".$_SESSION['m']['wickets2'.$i].",".$_SESSION['m']['wides2'.$i].",".$_SESSION['m']['noballs2'.$i].",".$_SESSION['m']['overs2'.$i].",".$bowlorder2.");";
			$resultbowl =dbToArray($sqlbowl);
			$bowlingid2 = $resultbowl[1][1]['bowlingid'];
			if($_SESSION['m']["wickets2".$i] >=6){
				//insert to honours
				$sqlh = "call insertHonoursPerformance(".$_SESSION['m']['playerid'.$i].",".$result1[1][1]['matchid'].",0,".$_SESSION['m']["wickets2".$i].",".$_SESSION['m']["runsconceded2".$i].",".$_SESSION['m']['teamid'].",0,".$bowlingid2.");";
				$resulth =dbToArray($sqlh);
			}
		}
		else{
			$bowlingid2 = 0;
		}
		//insert fielding details
		if($_SESSION['m']["catches".$i] != '' || $_SESSION['m']["stumpings".$i] != '' || $_SESSION['m']["byes".$i] != ''){
			$fieldingid = 0;
			($_SESSION['m']['catches'.$i]=='' ) ? $_SESSION['m']['catches'.$i]='0' : $_SESSION['m']['catches'.$i];
			($_SESSION['m']['stumpings'.$i]=='' ) ? $_SESSION['m']['stumpings'.$i]='0' : $_SESSION['m']['stumpings'.$i];
			($_SESSION['m']['byes'.$i]=='' ) ? $_SESSION['m']['byes'.$i]='0' : $_SESSION['m']['byes'.$i];
			$sqlf =  "call insertUpdateFielding(".$fieldingid.",".$_SESSION['m']['catches'.$i].",".$_SESSION['m']['stumpings'.$i].",".$_SESSION['m']["byes".$i].");";
			$resultf =dbToArray($sqlf);
			$fieldingid = $resultf[1][1]['fieldingid'];
		}
		else{
			$fieldingid = 0;
		}
		//insert fielding details
		if($_SESSION['m']["catches2".$i] != '' || $_SESSION['m']["stumpings2".$i] != '' || $_SESSION['m']["byes2".$i] != ''){
			$fieldingid2 = 0;
			($_SESSION['m']['catches2'.$i]=='' ) ? $_SESSION['m']['catches2'.$i]='0' : $_SESSION['m']['catches2'.$i];
			($_SESSION['m']['stumpings2'.$i]=='' ) ? $_SESSION['m']['stumpings2'.$i]='0' : $_SESSION['m']['stumpings2'.$i];
			($_SESSION['m']['byes2'.$i]=='' ) ? $_SESSION['m']['byes2'.$i]='0' : $_SESSION['m']['byes2'.$i];
			$sqlf =  "call insertUpdateFielding(".$fieldingid2.",".$_SESSION['m']['catches2'.$i].",".$_SESSION['m']['stumpings2'.$i].",".$_SESSION['m']["byes2".$i].");";
			$resultf =dbToArray($sqlf);
			$fieldingid2 = $resultf[1][1]['fieldingid'];
		}
		else{
			$fieldingid2 = 0;
		}
	
		//insert performance
		$performanceid=0;
		$sqlp = "call insertUpdatePerformance(".$performanceid.",".$_SESSION['m']['playerid'.$i].",".$matchid.",".$battingid.",".$bowlingid.",".$fieldingid.",1);";
		$resultp =dbToArray($sqlp);
		//insert performance, 2nd Innings
		$sqlp = "call insertUpdatePerformance(".$performanceid.",".$_SESSION['m']['playerid'.$i].",".$matchid.",".$battingid2.",".$bowlingid2.",".$fieldingid2.",2);";
		$resultp =dbToArray($sqlp);		
		
		$ten = dbToArray("call checkWickets10(".$matchid.",".$_SESSION['m']['playerid'.$i].");");				
	}

}// end loop
if($_SESSION['m']['mt']==1){
	//insert partnership details
	for($n=1; $n<=10; $n++){
		($_SESSION['m']['batsmanid1_'.$n] != "Select batsman 1") ? $_SESSION['m']['batsmanid1_'.$n] : $_SESSION['m']['batsmanid1_'.$n] =0; 
		($_SESSION['m']['batsmanid2_'.$n] != "Select batsman 2") ? $_SESSION['m']['batsmanid2_'.$n] : $_SESSION['m']['batsmanid2_'.$n] =0;
		($_SESSION['m']['partnership'.$n] != "") ? $_SESSION['m']['partnership'.$n] : $_SESSION['m']['partnership'.$n] =10101;
		$insert = dbToArray("call insertUpdatePartnership(0,".$matchid.",".$_SESSION['m']['wicket'.$n].",".$_SESSION['m']['batsmanid1_'.$n].",".$_SESSION['m']['batsmanid2_'.$n].",".$_SESSION['m']['partnership'.$n].",1);");
	}
}
else if($_SESSION['m']['mt']==2){
	//insert partnership details
	for($n=1; $n<=10; $n++){
		($_SESSION['m']['batsmanid1_'.$n] != "Select batsman 1") ? $_SESSION['m']['batsmanid1_'.$n] : $_SESSION['m']['batsmanid1_'.$n] =0; 
		($_SESSION['m']['batsmanid2_'.$n] != "Select batsman 2") ? $_SESSION['m']['batsmanid2_'.$n] : $_SESSION['m']['batsmanid2_'.$n] =0;
		($_SESSION['m']['partnership'.$n] != "") ? $_SESSION['m']['partnership'.$n] : $_SESSION['m']['partnership'.$n] =10101;
		$insert = dbToArray("call insertUpdatePartnership(0,".$matchid.",".$_SESSION['m']['wicket'.$n].",".$_SESSION['m']['batsmanid1_'.$n].",".$_SESSION['m']['batsmanid2_'.$n].",".$_SESSION['m']['partnership'.$n].",1);");

		($_SESSION['m']['batsmanid21_'.$n] != "Select batsman 1") ? $_SESSION['m']['batsmanid21_'.$n] : $_SESSION['m']['batsmanid21_'.$n] =0; 
		($_SESSION['m']['batsmanid22_'.$n] != "Select batsman 2") ? $_SESSION['m']['batsmanid22_'.$n] : $_SESSION['m']['batsmanid22_'.$n] =0;
		($_SESSION['m']['partnership2'.$n] != "") ? $_SESSION['m']['partnership2'.$n] : $_SESSION['m']['partnership2'.$n] =10101;
		$insert2 = dbToArray("call insertUpdatePartnership(0,".$matchid.",".$_SESSION['m']['wicket2'.$n].",".$_SESSION['m']['batsmanid21_'.$n].",".$_SESSION['m']['batsmanid22_'.$n].",".$_SESSION['m']['partnership2'.$n].",2);");	
	}
}
unsetSessions();	
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px;">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" border="0" height="800" id="main" style="table-layout:fixed ">
			<tr height="100">
			<!-- 
			1st table row
			includes heading and login.
			-->
				<td bgcolor="#404040" style="padding-left:10px; padding-top:5px;"><img src="images/headingloggedout.jpg"></td>
				<td align="right" bgcolor="#404040" style="color:#FFFFFF; padding-right:10px; padding-top:5px; " width="50%">
				<?php loggedIn($user->firstname, $user->lastname, $user->teamname, $user->role); ?>
				</td>
			</tr>
			<tr height="50">
			<!-- 
			2nd table row
			includes banner colour and text.
			-->	
				<td colspan="2" valign="top" bgcolor="<?php echo $colour; ?>">
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<tr id="banner" bgcolor="<?php echo $colour; ?>">
						<td valign="middle" align="right" style="padding-right:50px; padding-top:5px;"><?php echo "Processing..."; ?></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr height="100%">
			<!-- 
			3rd table row
			includes main page content.
			-->		
				<td colspan="2" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%" border="0" height="100%">
					<tr>
						<td bgcolor="<?php echo $colour; ?>" height="10%" style="table-layout:fixed ">&nbsp;</td>
						<td rowspan="2" valign="top" bgcolor="#212121"; width="900" height="100%">
						<table cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#FFFFFF"; height="100%">
							<tr>
								<td colspan="5" width="100%" valign="middle" align="center"><img src="images/wait.gif"></td>
							</tr>
						</table>
						</td>
						<td bgcolor="<?php echo $colour; ?>" width="30" height="10%" style="table-layout:fixed ">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="padding-top:10px;">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="center"><img src="images/wizard2.jpg"></td>
							</tr>
						</table>
						</td>
						<td>&nbsp;</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
			<!-- 
			4th table row
			includes footer.
			-->		
				<td colspan="2" align="center" style="padding-bottom:10px; padding-top:20px; ">&nbsp;</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>