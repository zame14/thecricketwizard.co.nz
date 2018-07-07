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

<body>
<?php
/********************
Validation
********************/
//print_r($_REQUEST);
$user = getUserDetails($_SESSION['userid']);
$colour = "#E8795E";
$text = "Statistics";


$mp = dbToArray("call getMatchesPlayed('where m.teamid=".$user->teamid."','','')");
if(count($mp)==0){
	redirect_rel('statshome.php');
}

$opponenttext = "all opposition";
$comptext = "all competitions";
$seasontext = "all seasons";
$criteria = "WHERE m.teamid=".$user->teamid."";

$a=0;
$pagenav="";

if(isset($_REQUEST['id'])){
	$id = $_REQUEST['id'];
	//check if opponent has been selected
	if((isset($_REQUEST['opponent']))&&$_REQUEST['opponent']<>"Filter by opposition"){
		$opponent = '"'.$_REQUEST['opponent'].'"';
		$opponenttext = $_REQUEST['opponent'];
		//check if comp has been selected
		if((isset($_REQUEST['compid']))&&$_REQUEST['compid']<>"Filter by competition"){
			//check if season has been selected
			$comptext = $_REQUEST['compid'];
			if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
				//all three have been selected so set criteria			
				$season = '"'.$_REQUEST['season'].'"';
				$seasontext = $_REQUEST['season']." season";
				$criteria = "WHERE m.teamid=".$user->teamid." AND opponent=".$opponent." AND m.compid=".$_REQUEST['compid']." AND season=".$season."";
				$a=1;
				$pagenav = "&opponent=".$_REQUEST['opponent']."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."";
			}
			else {
				//season not selected, but opponent and comp have been selected
				$criteria = "WHERE m.teamid=".$user->teamid." AND opponent=".$opponent." AND m.compid=".$_REQUEST['compid']."";
				$a=2;
				$pagenav = "&opponent=".$_REQUEST['opponent']."&compid=".$_REQUEST['compid']."";
			}
		}
		else if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
			//comp not select but opponent and season have been
				$season = '"'.$_REQUEST['season'].'"';
				$criteria = "WHERE m.teamid=".$user->teamid." AND opponent=".$opponent." AND season=".$season."";			
				$a=3;
				$pagenav = "&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."";
		}
		else {
			//just opponent has been selected
			$criteria = "WHERE m.teamid=".$user->teamid." AND opponent=".$opponent."";	
			$a=4;
			$pagenav = "&opponent=".$_REQUEST['opponent']."";	
		}
	}
	//Opponent not selected but check comp and season
	else if(isset($_REQUEST['compid'])&&$_REQUEST['compid']<>"Filter by competition"){
		$comptext = $_REQUEST['compid'];
		//season and comp have been selected
		if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
			$season = '"'.$_REQUEST['season'].'"';
			$seasontext = $_REQUEST['season']." season";
			$criteria = "WHERE m.teamid=".$user->teamid." AND m.compid=".$_REQUEST['compid']." AND season=".$season."";
			$a=5;
			$pagenav = "&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."";
		}
		else {
			//just comp been selected
			$criteria = "WHERE m.teamid=".$user->teamid." AND m.compid=".$_REQUEST['compid']."";
			$a=6;
			$pagenav = "&compid=".$_REQUEST['compid']."";
		}		
	}
	//just season has been selected
	else if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
			$season = '"'.$_REQUEST['season'].'"';
			$seasontext = $_REQUEST['season']." season";
			$criteria = "WHERE m.teamid=".$user->teamid." AND season=".$season."";			
			$a=7;
			$pagenav = "&season=".$_REQUEST['season']."";		
	}	
	if($id=="batting"){		
		$text2 = "Batting & Fielding";
	}
	else if($id=="bowling"){
		$text2 = "Bowling";
	}
	else {
		$text2 = $user->teamname;
	}
}
else {
	redirect_rel('statshome.php');
}
if($comptext<>"all competitions"){ 
	$rs = dbToArray("call getComps('where compid=".$comptext."')");
	$comptext = $rs[1]['competition'];
}

$o="Filter by opponent";
if(isset($_REQUEST['opponent'])){
	$o = $_REQUEST['opponent'];
}
$c="Filter by competition";
if(isset($_REQUEST['compid'])){
	$c = $_REQUEST['compid'];
}
$s="Filter by season";
if(isset($_REQUEST['season'])){
	$s = $_REQUEST['season'];
}
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px;">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" border="0" height="800" id="main" style="table-layout:fixed;">
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
						<td valign="middle" align="right" style="padding-right:50px; padding-top:5px;"><?php echo $text; ?></td>
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
						<td rowspan="2" valign="top" bgcolor="#212121"; width="900" height="100%" id="mainfrm">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="left" valign="middle" style="font-size:20px; padding-left:20px; padding-top:10px;" id="whitetext" colspan="3">
								<?php 
								if(!isset($_REQUEST["compid"]) || $_REQUEST["compid"]=="Filter by competition"){
									$_REQUEST["compid"]="";
								}
								if(!isset($_REQUEST["opponent"]) || $_REQUEST["opponent"]=="Filter by opposition"){
									$_REQUEST["opponent"]="";
								}
								else {
									$_REQUEST["opponent"]=str_replace(" ","_",$_REQUEST["opponent"]);
								}
								if(!isset($_REQUEST["season"]) || $_REQUEST["season"]=="Filter by season"){
									$_REQUEST["season"]="";
								}
								else {
									$_REQUEST["season"]=str_replace(" ","_",$_REQUEST["season"]);
								}
								echo "<strong>".$text2."</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; if($id=="batting" || $id=="bowling"){ echo "<input type=button value='Print' onClick=window.open('printstats.php?id=".$id."&teamid=".$user->teamid."&season=".$_REQUEST["season"]."&compid=".$_REQUEST["compid"]."&opponent=".$_REQUEST["opponent"]."'); style='color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;'>"; } ?>
								</td>
							</tr>
							<tr>
								<td align="left" style="font-size:18px; padding-left:20px; padding-bottom:10px; padding-top:10px; padding-right:10px;" id="whitetext">
								<?php
								if($id=="batting"){
									echo "Welcome to the batting & fielding statistics for the ".$user->teamname. " cricket team.<br>";
									echo "To change the criteria for which the stats are based on, select from the drop down menus below and click the View stats button.  To remove the filter click Clear.<br><br>"; 
									echo "<div id='goldtext'>View player statistics</div>";
									echo "To view players individual statistics, click on the players name.<br><br>";
									
								}
								else if($id=="bowling"){
									echo "Welcome to the bowling statistics for the ".$user->teamname. " cricket team.<br>";
									echo "To change the criteria for which the stats are based on, select from the drop down menus below and click the View stats button.  To remove the filter click Clear.<br><br>"; 
									echo "<div id='goldtext'>View player statistics</div>";
									echo "To view players individual statistics, click on the players name.<br><br>";
								}
								else {
									echo "Welcome to the team statistics for the ".$user->teamname." cricket team.<br>  To change the criteria for which the stats are based on, select from the drop down menus below and click the View stats button.  To remove the filter click Clear.";
								}
								?>
								</td>
							</tr>
							<tr>
								<td>
								<form name="tfrm" action="viewstats.php?id=<?php echo $id ?>" method="post">
								<table cellpadding="5" cellspacing="0" border="0">
									<tr>
										<td style="padding-left:20px;">
										<?php 
										$opponents = dbToArray("call getOpponents('where teamid=".$user->teamid."')"); 
										print_dropDown("opponent", "Filter by opposition", $opponents,$o);
										?>
										</td>
										<td style="padding-left:20px;">
										<?php 
										$comps = dbToArray("call getComps('where teamid=".$user->teamid."')"); 
										print_dropDown("compid", "Filter by competition", $comps,$c);
										?>
										</td>
										<td style="padding-left:20px;">
										<?php 
										$seasons = dbToArray("call getSeasons(".$user->teamid.")"); 
										print_dropDown("season", "Filter by season", $seasons,$s);
										?>								
										</td>
									</tr>
									<tr>
										<td style="padding-left:20px;" colspan="2">
										<input type="button" value="View stats" onClick="View();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">&nbsp;&nbsp;
										<input type="button" value="Clear" onClick="javascript:document.location='viewstats.php?id=<?php echo $id ?>'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
										</td>
									</tr>						
								</table>
								</form>		
								</td>
							</tr>
							<tr>
								<td width="100%" valign="top" style="padding-left:15px; padding-right:15px; padding-top:0px;">
								<?php 		
								if(isset($id)){
									if($id=="team"){
										team($user,$criteria,$opponenttext,$comptext,$seasontext,$pagenav);
									}
									else if($id=="batting"){
										batting($user->teamid,$opponenttext,$comptext,$seasontext,$criteria,$a,$pagenav); 
									}
									else if($id=="bowling"){
										bowling($user->teamid,$opponenttext,$comptext,$seasontext,$criteria,$a,$pagenav);
									}
								}
								else {
									team($user, $criteria,$opponenttext,$comptext,$seasontext,$pagenav);
								}
								?>
								</td>
							</tr>
						</table>
						</td>
						<td bgcolor="<?php echo $colour; ?>" width="30" height="10%" style="table-layout:fixed ">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="padding-top:10px;">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="center"><?php mainmenu2(); ?></td>
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
				<td colspan="2" align="center" style="padding-bottom:10px; padding-top:20px; "><?php footer(); ?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
<script language="javascript">
function View() {
	frm = document.tfrm;
	frm.submit();
}
</script>
</body>
</html>
<?php
function team($user,$criteria,$opponenttext,$comptext,$seasontext,$pagenav) {
$sql = "call getNumGamesPlayed('".$criteria."');";
$played = dbToArray($sql);

$sql = "call getNumGamesWon('".$criteria."');";
$won = dbToArray($sql);

$sql = "call getNumGamesLost('".$criteria."');";
$lost = dbToArray($sql);

$sql = "call getNumGamesOther('".$criteria."');";
$other = dbToArray($sql);

if($played[1]['played']>0){
$winning = ($won[1]['win']/$played[1]['played'])*100;
}
else {
$winning = 0;
}

$sql1 = "call mostCaps('".$criteria."','order by caps desc');";
$result1 = dbToArray($sql1);

$sql2 = "call mostRuns('".$criteria."','order by sum(b.runs) desc');";
$result2 = dbToArray($sql2);

$sql3 = "call mostWickets('".$criteria."','order by sum(b.wickets) desc');";
$result3 = dbToArray($sql3);

$sql5 = "call highestIndividualScore('".$criteria."','order by b.runs desc, did desc');";
$result5 = dbToArray($sql5);

$sql6 = "call mostSixes('".$criteria."','order by sum(b.sixes) desc');";
$result6 = dbToArray($sql6);

$sql7 = "call bestIndividualBowling('".$criteria."','order by wickets desc, runsconceded asc');";
$result7 = dbToArray($sql7);

$sql8 = "call mostCatches('".$criteria."','order by sum(f.catches) desc');";
$result8 = dbToArray($sql8);
?>
<form name="thefrm" action="viewstats.php" method="post">
<input type="hidden" name="action">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td align="right" style="padding-bottom:10px; ">
		<input type="button" value="Batting" onClick="javascript:document.location='viewstats.php?id=batting<?php echo $pagenav; ?>'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
		&nbsp;&nbsp;&nbsp;<input type="button" value="Bowling" onClick="javascript:document.location='viewstats.php?id=bowling<?php echo $pagenav; ?>'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
		</td>
	</tr>
	<tr>
		<td width="60%">
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="stats">
			<tr>
				<td colspan="2"><strong><?php echo "vs ".$opponenttext.", ".$comptext.", ".$seasontext; ?></strong></td>
			</tr>
			<tr>
				<td width="27%">Played:</td>
				<td width="73%"><?php echo $played[1]['played']; ?></td>
			</tr>
			<tr>
				<td>Won:</td>
				<td><?php echo $won[1]['win']; ?></td>
			</tr>
			<tr>
				<td>Loss:</td>
				<td><?php echo $lost[1]['loss']; ?></td>
			</tr>
			<tr>
				<td>Other:</td>
				<td><?php echo $other[1]['other']; ?></td>
			</tr>
			<tr>
				<td>Win ratio:</td>
				<td><?php echo round($winning,2).'%'; ?></td>
			</tr>
			<tr>
				<td>Most caps:</td>
				<td><?php echo $result1[1]['caps'].' &ndash; '.stripslashes($result1[1]['player']); ?></td>
			</tr>
			<tr>
				<td>Highest run scorer:</td>
				<td><?php echo $result2[1]['runs'].' &ndash; '.stripslashes($result2[1]['player']); ?></td>
			</tr>
			<tr>
				<td>Highest individual score:</td>
				<td><?php 
				if($result5[1]['did']==7){
					echo $result5[1]['runs'].'* &ndash; '.stripslashes($result5[1]['player']); 
				}
				else {
					echo $result5[1]['runs'].' &ndash; '.stripslashes($result5[1]['player']);
				} 
				?></td>
			</tr>
			<tr>
				<td>Most sixes:</td>
				<td>
				<?php 
				if($result6[1]['sixes']<>""){
					echo $result6[1]['sixes'].' &ndash; '.stripslashes($result6[1]['player']); 
				}
				else {
					echo '&ndash;';
				}
				?>
				</td>
			</tr>
			<tr>
				<td>Top wicket taker:</td>
				<td><?php echo $result3[1]['wickets'].' &ndash; '.stripslashes($result3[1]['player']); ?></td>
			</tr>
			<tr>
				<td>Best bowling figures:</td>
				<td><?php echo $result7[1]['bb'].' &ndash; '.stripslashes($result7[1]['player']); ?></td>
			</tr>
			<tr>
				<td>Most catches:</td>
				<td><?php echo $result8[1]['catches'].' &ndash; '.stripslashes($result8[1]['player']); ?></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2"><strong>Team records</strong></td>
			</tr>	
			<tr>
				<td>Most runs in a season:</td>
				<td>
				<?php
				$mostruns = dbToArray("call mostRunsInSeason(".$user->teamid.");");
				if($mostruns[1]['runs']>0){
					echo $mostruns[1]['runs']." &ndash; ".stripslashes($mostruns[1]['player'])." (".$mostruns[1]['season'].")";
				}
				else {
					echo "-";
				}
				?>
				</td>
			</tr>
			<tr>
				<td>Most sixes in a season:</td>
				<td>
				<?php
				$mostsixes = dbToArray("call mostSixesInSeason(".$user->teamid.");");
				if($mostsixes[1]['sixes'] <> ""){
					echo $mostsixes[1]['sixes']." &ndash; ".stripslashes($mostsixes[1]['player'])." (".$mostsixes[1]['season'].")";
				}
				else {
					echo "-";
				}
				?>				
				</td>
			</tr>
			<tr>
				<td>Most sixes in a match:</td>
				<td>
				<?php
				$mostsixesM = dbToArray("call mostSixesInMatch(".$user->teamid.");");
				if($mostsixesM[1]['sixes'] <> ""){
					echo $mostsixesM[1]['sixes']." &ndash; ".stripslashes($mostsixesM[1]['player'])." vs ".$mostsixesM[1]['opponent']." (".$mostsixesM[1]['season'].")";
				}
				else {
					echo "-";
				}
				?>				
				</td>
			</tr>
			<tr>
				<td>Most wickets in a season:</td>
				<td>
				<?php
				$mostwickets = dbToArray("call mostWicketsInSeason(".$user->teamid.");");
				if($mostwickets[1]['wickets']>0){
					echo $mostwickets[1]['wickets']." &ndash; ".stripslashes($mostwickets[1]['player'])." (".$mostwickets[1]['season'].")";
				}
				else {
					echo "-";
				}
				?>				
				</td>
			</tr>
			<tr>
				<td>Most catches in a season:</td>
				<td>
				<?php
				$mostcatches = dbToArray("call mostCatchesInSeason(".$user->teamid.");");
				if($mostcatches[1]['catches']>0){
					echo $mostcatches[1]['catches']." &ndash; ".stripslashes($mostcatches[1]['player'])." (".$mostcatches[1]['season'].")";
				}
				else {
					echo "-";
				}
				?>					
				</td>
			</tr>	
			<tr>
				<td>Most catches in a match:</td>
				<td>
				<?php
				$mostcatchesM = dbToArray("call mostCatchesInMatch(".$user->teamid.");");
				if($mostcatchesM[1]['catches']>0){
					echo $mostcatchesM[1]['catches']." &ndash; ".stripslashes($mostcatchesM[1]['player'])." vs ".$mostcatchesM[1]['opponent']. " (".$mostcatchesM[1]['season'].")";
				}
				else {
					echo "-";
				}
				?>					
				</td>
			</tr>			
		</table>
		</td>
	</tr>

	<tr>
		<td align="center" style="padding-top:20px; padding-bottom:10px;"><input type="button" value="Back" onClick="javascript:document.location='statshome.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
	</tr>
</table>
</form>
<?php
}

function batting($teamid,$opponenttext,$comptext,$seasontext,$criteria,$a,$pagenav){
//$criteria1 = $criteria.' and bat.battingid >0';
$cols = 14;
$showExtra = false;
if($teamid <> 1) {
    $cols = 16;
    $showExtra = true;
}
$sql = "call getStatsTeam('".$criteria."','group by p.playerid','order by sum(runs) desc','');";
$result = dbToArray($sql);
$count = count($result);

$rows = $count;
if(!isset($_GET['pagenum'])){
	$pagenum = 1;
}
else {
	$pagenum = $_GET['pagenum'];
}

$page_rows = 20;
$last = ceil($rows/$page_rows);
$previous="";
$next="";
if ($pagenum < 1) { 
	$pagenum = 1; 
} 
elseif ($pagenum > $last) { 
	$pagenum = $last; 
} 

if($pagenum<>1){
	$previous = $pagenum-1;
}

if($pagenum <> $last){
	$next = $pagenum+1;
}
$max = 'limit ' .($pagenum - 1) * $page_rows .',' .$page_rows;

if($count<>0){
	$sql1 = "call getStatsTeam('".$criteria."','group by p.playerid','order by sum(runs) desc','".$max."');";
	$result1 = dbToArray($sql1);
	$count1 = count($result1);
}

switch ($a){
	case 0;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";		
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";		
		break;
	case 1;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
	case 2;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
	case 3;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
	case 4;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
	case 5;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
	case 6;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
	case 7;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
}
?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<?php
	if($count<>0){ ?>
	<tr>
		<td align="right" style="padding-bottom:10px; ">
		<input type="button" value="Team" onClick="javascript:document.location='viewstats.php?id=team<?php echo $pagenav; ?>'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
		&nbsp;&nbsp;&nbsp;<input type="button" value="Bowling" onClick="javascript:document.location='viewstats.php?id=bowling<?php echo $pagenav; ?>'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="stats">
			<tr>
				<td colspan="<?php echo $cols; ?>" align="right">
				<?php
				if ($pagenum == 1){
					echo "&nbsp;";
				} 
				else {
					echo $firstpage;
					echo " ";
					echo $prevpage;
					echo " ";
				}		
				if ($pagenum == $last) {
				} 
				else {
					echo $nextpage;
					echo " ";
					echo $lastpage;
				}
				?>
				</td>
			</tr>
			<tr>
				<td colspan="14"><strong><?php echo "vs ".$opponenttext.", ".$comptext.", ".$seasontext; ?></strong></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>M</td>
				<td>I</td>
				<td>NO</td>
				<td>Runs</td>
                <?php
                if($showExtra) { ?>
                    <td>BF</td>
                    <td>SR</td>
                <?php } ?>
				<td>Ave</td>
				<td>HS</td>
				<td>100</td>
				<td>50</td>
				<td>4s</td>
				<td>6s</td>
				<td>Ct</td>
				<td>St</td>
			</tr>
			<?php
			for($i=1;$i<=$count1;$i++){
			$pos = ($pagenum-1)*$page_rows+$i;
				if($result1[$i]['matches']>0){
				?>
				<tr>
					<td><?php echo $pos; ?></td>
					<td><a href="playerstatshome.php?pid=<?php echo $result1[$i]['playerid']?>" style="cursor:pointer; "><?php echo stripslashes($result1[$i]['player']); ?></a></td>
					<td title="Matches"><?php echo $result1[$i]['matches']; ?></td>
					<td title="Innings"><?php echo $result1[$i]['innings']; ?></td>
					<td title="Not outs"><?php echo ($result1[$i]['innings']>0) ? $result1[$i]['notouts'] : "-"; ?></td>
					<td title="Runs"><?php echo ($result1[$i]['innings']>0) ? $result1[$i]['runs'] : "-"; ?></td>
                    <?php
                    if($showExtra) { ?>
                        <td title="Balls Faced"><?php echo ($result1[$i]['ballsfaced']<>"-") ? $result1[$i]['ballsfaced'] : "&nbsp;"; ?></td>
                        <td title="Strike Rate"><?php echo ($result1[$i]['ballsfaced']<>"-") ? round(($result1[$i]['runs']/$result1[$i]['ballsfaced'])*100,2) : "&nbsp;"; ?></td>
                    <?php } ?>
					<td title="Batting average">
					<?php 
					if($result1[$i]['innings']==$result1[$i]['notouts']){
						echo"-";
					}
					else {
						echo ($result1[$i]['innings']>0) ? round($result1[$i]['bataverage'],2) : "-"; 
					}
					?>
					</td>
					<?php
					$hs = dbToArray("call highestIndividualScore('".$criteria." AND p.playerid = ".$result1[$i]['playerid']."','order by runs desc, did desc');");
					if($hs[1]['did']==7){
						$highestscore = ($result1[$i]['innings']>0) ? $hs[1]['runs']."*" : "-";
					}
					else {
						$highestscore = ($result1[$i]['innings']>0)?$hs[1]['runs'] : "-";
					}
					?>
					<td title="HS"><?php echo $highestscore; ?></td>
					<td title="100s"><?php echo ($result1[$i]['innings']>0) ? $result1[$i]['100s'] : "-"; ?></td>
					<td title="50s"><?php echo ($result1[$i]['innings']>0) ? $result1[$i]['50s'] : "-"; ?></td>
					<td title="4s"><?php echo ($result1[$i]['innings']>0) ? $result1[$i]['fours'] : "-"; ?></td>
					<td title="6s"><?php echo ($result1[$i]['innings']>0) ? $result1[$i]['sixes'] : "-"; ?></td>
					<td title="Catches"><?php echo $result1[$i]['catches']; ?></td>
					<td title="Stumpings"><?php echo $result1[$i]['stumpings']; ?></td>
				</tr>
				<?php
				}
			}
			?>
			<tr>
				<td colspan="14" align="right"><?php echo "Page ".$pagenum." of ".$last; ?></td>
			</tr>
		</table>		
		</td>
	</tr>
	<tr>
		<td align="right" style="padding-top:20px; ">
		<?php
		if ($pagenum == 1){
			echo "&nbsp;";
		} 
		else {
			echo $firstpage;
			echo " ";
			echo $prevpage;
			echo " ";
		}		
		if ($pagenum == $last) {
		} 
		else {
			echo $nextpage;
			echo " ";
			echo $lastpage;
		}
		?>
		</td>
	</tr>
	<?php }
	else { ?>
	<tr>
		<td id="whitetext" style="font-size:18px; padding-bottom:450px; padding-left:5px;">No statistics available to view</td>
	</tr>
	<?php } ?>
	<tr>
		<td align="center" style="padding-top:20px; padding-bottom:10px;"><input type="button" value="Back" onClick="javascript:document.location='statshome.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
	</tr>
</table>
<?php
}

function bowling($teamid,$opponenttext,$comptext,$seasontext,$criteria,$a,$pagenav){
//$criteria1 = $criteria.' and deliveries <> ""';
$sql = "call getStatsTeam('".$criteria."','group by p.playerid','order by sum(wickets) desc','');";
$result = dbToArray($sql);
$count = count($result);

$rows = $count;
if(!isset($_GET['pagenum'])){
	$pagenum = 1;
}
else {
	$pagenum = $_GET['pagenum'];
}

$page_rows = 20;
$last = ceil($rows/$page_rows);
$previous="";
$next="";
if ($pagenum < 1) { 
	$pagenum = 1; 
} 
elseif ($pagenum > $last) { 
	$pagenum = $last; 
} 

if($pagenum<>1){
	$previous = $pagenum-1;
}

if($pagenum <> $last){
	$next = $pagenum+1;
}
$max = 'limit ' .($pagenum - 1) * $page_rows .',' .$page_rows;

if($count<>0){
	$sql1 = "call getStatsTeam('".$criteria."','group by p.playerid','order by sum(wickets) desc','".$max."');";
	$result1 = dbToArray($sql1);
	$count1 = count($result1);
}

switch ($a){
	case 0;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";		
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";		
		break;
	case 1;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
	case 2;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
	case 3;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
	case 4;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&opponent=".$_REQUEST['opponent']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
	case 5;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
	case 6;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&compid=".$_REQUEST['compid']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
	case 7;
		$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
		$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
		$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
		$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$_REQUEST['id']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";	
		break;
}
?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<?php
	if($count<>0){ ?>
	<tr>
		<td align="right" style="padding-bottom:10px; ">
		<input type="button" value="Team" onClick="javascript:document.location='viewstats.php?id=team<?php echo $pagenav; ?>'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
		&nbsp;&nbsp;&nbsp;<input type="button" value="Batting" onClick="javascript:document.location='viewstats.php?id=batting<?php echo $pagenav; ?>'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="stats">
			<tr>
				<td colspan="12" align="right">
				<?php
				if ($pagenum == 1){
					echo "&nbsp;";
				} 
				else {
					echo $firstpage;
					echo " ";
					echo $prevpage;
					echo " ";
				}		
				if ($pagenum == $last) {
				} 
				else {
					echo $nextpage;
					echo " ";
					echo $lastpage;
				}
				?>
				</td>
			</tr>
			<tr>
				<td colspan="12"><strong><?php echo "vs ".$opponenttext.", ".$comptext.", ".$seasontext; ?></strong></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>M</td>
				<td>Balls</td>
				<td>Mdns</td>
				<td>Runs</td>
				<td>Wkts</td>
				<td>Ave</td>
				<td>SR</td>
				<td>Econ</td>
				<td>5</td>
				<td>10</td>
			</tr>
			<?php
			for($i=1;$i<=$count1;$i++){
			$pos = ($pagenum-1)*$page_rows+$i;
			if($result1[$i]['deliveries']>0){
			?>
				<tr>
					<td><?php echo $pos; ?></td>
					<td><a href="playerstatshome.php?pid=<?php echo $result1[$i]['playerid']?>" style="cursor:pointer; "><?php echo stripslashes($result1[$i]['player']); ?></a></td>
					<td title="Matches"><?php echo $result1[$i]['matches']; ?></td>
					<td title="Deliveries"><?php echo $result1[$i]['deliveries']; ?></td>
					<td title="Maidens"><?php echo $result1[$i]['maidens']; ?></td>
					<td title="Runs conceded"><?php echo $result1[$i]['runsconceded']; ?></td>
					<td title="Wickets"><?php echo $result1[$i]['wickets']; ?></td>
					<td title="Bowling average"><?php if($result1[$i]['deliveries']>0&&$result1[$i]['wickets']==0){echo"-";}else{echo round($result1[$i]['bowlaverage'],2);} ?></td>
					<td title="Bowling strike rate"><?php if($result1[$i]['deliveries']>0&&$result1[$i]['wickets']==0){echo"-";}else{echo round($result1[$i]['strikerate'],2);} ?></td>
					<td title="Runs per over"><?php echo round($result1[$i]['rpo'],2); ?></td>
					<td title="Five wicket bags"><?php echo $result1[$i]['fivewickets']; ?></td>
					<td title="Ten wickets in a match"><?php echo $result1[$i]['tenwickets']; ?></td>
				</tr>
				<?php 
				} 
			}
			?>
			<tr>
				<td colspan="12" align="right"><?php echo "Page ".$pagenum." of ".$last; ?></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td align="right" style="padding-top:20px; ">
		<?php
		if ($pagenum == 1){
			echo "&nbsp;";
		} 
		else {
			echo $firstpage;
			echo " ";
			echo $prevpage;
			echo " ";
		}		
		if ($pagenum == $last) {
		} 
		else {
			echo $nextpage;
			echo " ";
			echo $lastpage;
		}
		?>
		</td>
	</tr>
	<?php }
	else {
	?>
	<tr>
		<td id="whitetext" style="font-size:18px; padding-bottom:450px; padding-left:5px;">No statistics available to view</td>
	</tr>	
	<?php } ?>
	<tr>
		<td align="center" style="padding-top:20px; padding-bottom:10px;"><input type="button" value="Back" onClick="javascript:document.location='statshome.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
	</tr>
</table>
<?php
}
?>