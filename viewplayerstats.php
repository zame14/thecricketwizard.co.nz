<?php
/********************
Includes
********************/
include_once("inc/db_functions.php");
include_once("inc/form_lib.php");
include_once("inc/cricket_lib.php");
session_start();
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
<script type="text/javascript" src="flexcroll.js"></script>
<script type="text/javascript" src="toolstips/wz_tooltip.js"></script>
<?php
/********************
Validation
********************/
$user = getUserDetails($_SESSION['userid']);
$colour = "#E8795E";
$text = "Statistics";

$mp = dbToArray("call getMatchesPlayed('where m.teamid=".$user->teamid."','','')");
if(count($mp)==0){
	redirect_rel('statshome.php');
}
$error=0;
//print_r($_REQUEST);

if(isset($_REQUEST['id'])&&$_REQUEST['id']<>""){
	$id = $_REQUEST['id'];
	if($id<>"opposition" && $id<>"competition" && $id<>"season" && $id<>"dismissals" && $id<>"scores" && $id<>"wickets" && $id<>"boundaries"&& $id<>"innings"){
		redirect_rel('playerstatshome.php');
	}
}
if(isset($_REQUEST['pid'])){
	//Check the user is viewing a profile from his/her team
	$check = dbToArray("call getPlayers('WHERE pt.teamid=".$user->teamid." AND pt.userid=".$_REQUEST["pid"]."','','');");
	if($check[1]["userid"]==""){
		//not viewing correct team
		redirect_rel("statshome.php");
	}
}
else {
	redirect_rel("statshome.php");
}


$sqlall = "call getStatsTeam('where p.playerid=".$_REQUEST["pid"]."','group by p.playerid','','');";
$resultall = dbToArray($sqlall);

if($resultall[1]['teamname']<>$user->teamname){
	//something has gone wrong, this user should not be viewing players from a different team.
	redirect_rel('statshome.php');
}
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px;">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" border="0" height="800" id="main" style="table-layout:fixed">
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
								<td align="left" valign="middle" style="font-size:24px; padding-left:15px; padding-top:10px;" id="whitetext" colspan="3"><strong><?php echo stripslashes($resultall[1]['player']); ?></strong></td>
							</tr>
							<tr>
								<td width="100%" valign="top" style="padding-left:15px; padding-right:15px; padding-top:15px;">
								<?php 
								if($id=="boundaries"){
									boundaries($_REQUEST["pid"], $user, $id, $error);
								}
								else if($id=="innings"){
									innings($user, $_REQUEST["pid"], $id, $error);
								}
								else {
									playerstats($resultall, $user, $_REQUEST["pid"], $id, $error);	
								}			
								?>
								</td>
							</tr>
							<tr>
								<td style="padding-bottom:20px; padding-top:20px;" align="center" valign="bottom"><input type="button" value="Back" onClick="javascript:document.location='playerstatshome.php?pid=<?php echo $_REQUEST["pid"] ?>';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
							</tr>					
						</table>
						</td>
						<td bgcolor="<?php echo $colour; ?>" width="30" height="10%" style="table-layout:fixed ">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="padding-top:10px; padding-left:5px; padding-right:5px;" width="160">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="center"><?php mainmenu2(); ?></td>
							</tr>
							<?php
							//if there is only one player in the team, dont display menu
							$check = dbToArray("call getPlayers('WHERE pt.teamid=".$user->teamid."','','');");
							if(count($check)>1){ ?>
							<tr>
								<td><?php select_players($user->teamid, $_REQUEST["pid"],$id); ?></td>
							</tr>
							<?php } ?>
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
<?php
function playerstats($resultall, $user, $pid, $id,$error){
if($id=="competition"){
	$groupby = "group by m.compid";
	$orderby = "order by m.compid";
	$text = "Competition";
}
else if($id=="season"){
	$groupby = "group by season";
	$orderby = "order by season";
	$text = "Season";
} 
else if($id=="opposition"){
	$groupby = "group by opponent";
	$orderby = "order by opponent";
	$text = "Opposition";
}
$sql = "call getStatsTeam('where p.playerid=".$pid."','".$groupby."','".$orderby."','');";
$result = dbToArray($sql);
$count = count($result);

$sql1all = "call getBestBowled('where p.playerid=".$pid."','','order by wickets desc, runsconceded asc','limit 1');";
$result1all = dbToArray($sql1all);	
if ($result1all[1]['bb']<>""){
	$bestall = $result1all[1]['bb'];		
}
else {
	$bestall = "-";
}

?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
	<tr>
		<td id="greytext">Batting & Fielding Statistics</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="stats">
			<tr>
				<td><?php echo $text; ?></td>
				<td>M</td>
				<td>I</td>
				<td>NO</td>
				<td>Runs</td>
				<td>HS</td>
				<td>Ave</td>
				<td>100</td>
				<td>50</td>
				<td>4s</td>
				<td>6s</td>
				<td>SR</td>
				<td>Ct</td>
				<td>St</td>
			</tr>
			<?php
			for($i=1;$i<=$count;$i++){
			if($result[$i]['innings']==0){
				$result[$i]['notouts']="-";
				$result[$i]['100s']="-";
				$result[$i]['50s']="-";
				$result[$i]['sixes']="-";
				$result[$i]['fours']="-";
			}
			?>
			<tr>
				<td>
				<?php 
				if($id=="competition"){
					echo $result[$i]['competition'];
					$criteria = "WHERE m.compid=".$result[$i]['compid']." AND p.playerid=".$result[1]['playerid']."";
				}
				else if($id=="season"){
					echo $result[$i]['season'];
					$season = '"'.$result[$i]['season'].'"';
					$criteria = "WHERE season=".$season." AND p.playerid=".$result[1]['playerid']."";
				} 
				else if($id=="opposition"){
					echo $result[$i]['opponent'];
					$opponent = '"'.$result[$i]['opponent'].'"';
					$criteria = "WHERE opponent=".$opponent." AND p.playerid=".$result[1]['playerid']."";
				} 
				if($result[$i]['did']==7){
					$result[$i]['hs'] = $result[$i]['hs']."*";
				}
				?>
				</td>
				<td title="Matches"><?php echo $result[$i]['matches']; ?></td>
				<td title="Innings"><?php echo $result[$i]['innings']; ?></td>
				<td title="Not outs"><?php echo $result[$i]['notouts']; ?></td> 
				<td title="Runs"><?php echo $result[$i]['runs']; ?></td>
				<td title="Highest Score">
				<?php 
				if($result[$i]['innings']<>0){
					$hs = dbToArray("call highestIndividualScore('".$criteria."','order by runs desc, did desc');");
					if($hs[1]['did']==7){
						$hs[1]['runs'] = $hs[1]['runs']."*";
					}
					echo $hs[1]['runs'];
				}
				else {
					echo "-";
				}
				?>
				</td>
					<td title="Batting average">
					<?php 
					if(($result[$i]['innings']==$result[$i]['notouts'])||($result[$i]['innings']==0)){
						echo"-";
					}
					else {
						echo round($result[$i]['bataverage'],2); 
					}
					?>
					</td>
				<td title="100s"><?php echo $result[$i]['100s']; ?></td>
				<td title="50"><?php echo $result[$i]['50s']; ?></td>
				<td title="4s"><?php echo $result[$i]['fours']; ?></td>
				<td title="6s"><?php echo $result[$i]['sixes']; ?></td>
				<td title="Batting strike rate">
				<?php
				if($result[$i]['ballsfaced']<>"-"){
					$sr = dbToArray("call getStatsTeam('where p.playerid=".$pid." AND bat.ballsfaced is not null AND m.compid=".$result[$i]['compid']."','".$groupby."','".$orderby."','');");					
					echo round($sr[1]['srate'],2); 
				}
				else {
					echo "-";
				}
				?>
				</td>
				<td title="Catches"><?php echo $result[$i]['catches']; ?></td>
				<td title="Stumpings"><?php echo $result[$i]['stumpings']; ?></td>
			</tr>
			<?php } 
			?>
			<tr>
				<td><strong><?php echo "Overall"; ?></strong></td>
				<td><strong><?php echo $resultall[1]['matches']; ?></strong></td>
				<td><strong><?php echo $resultall[1]['innings']; ?></strong></td>
				<td><strong><?php echo $resultall[1]['notouts']; ?></strong></td>
				<td><strong><?php echo $resultall[1]['runs']; ?></strong></td>
				<td><strong>
				<?php 
				$hsall = dbToArray("call highestIndividualScore('where p.playerid=".$pid."','order by runs desc, did desc');");
				if($hsall[1]['did']==7){
					$hsall[1]['runs'] = $hsall[1]['runs']."*";
				}
				echo $hsall[1]['runs'];
				?>
				</strong>
				</td>
				<td>
				<strong>
				<?php 
				if($resultall[1]['innings']==$resultall[1]['notouts']){
					echo"-";
				}
				else {
					echo round($resultall[1]['bataverage'],2); 
				}
				?>
				</td>
				</strong>
				<td><strong><?php echo $resultall[1]['100s']; ?></strong></td>
				<td><strong><?php echo $resultall[1]['50s']; ?></strong></td>
				<td><strong><?php echo $resultall[1]['fours']; ?></strong></td>
				<td><strong><?php echo $resultall[1]['sixes']; ?></strong></td>
				<td><strong>
				<?php 
				if($resultall[1]['ballsfaced']<>"-"){
					$srall = dbToArray("call getStatsTeam('where p.playerid=".$pid." AND bat.ballsfaced is not null','group by p.playerid','','');");					
					echo round($srall[1]['srate'],2); 
				}
				else {
					echo "-";
				}
				?>
				</strong></td>
				<td><strong><?php echo $resultall[1]['catches']; ?></strong></td>
				<td><strong><?php echo $resultall[1]['stumpings']; ?></strong></td>	
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td id="greytext" style="padding-top:40px; ">Bowling Statistics</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="stats">
			<tr>
				<td><?php echo $text; ?></td>
				<td>M</td>
				<td>Balls</td>
				<td>Mdns</td>
				<td>Runs</td>
				<td>Wkts</td>
				<td>BB</td>
				<td>Ave</td>
				<td>5</td>
				<td>10</td>
				<td>SR</td>
				<td>Econ</td>
			</tr>
			<?php
			$m = 1;
			for($i=1;$i<=$count;$i++){			
			?>
			<tr>
				<td>
				<?php
				if($id=="competition"){
					echo $result[$i]['competition'];
					$criteria = "WHERE m.compid=".$result[$i]['compid']." AND p.playerid=".$result[1]['playerid']."";
				}
				else if($id=="season"){
					echo $result[$i]['season'];
					$season = '"'.$result[$i]['season'].'"';
					$criteria = "WHERE season=".$season." AND p.playerid=".$result[1]['playerid']."";					
				} 
				else if($id=="opposition"){
					echo $result[$i]['opponent'];
					$opponent = '"'.$result[$i]['opponent'].'"';
					$criteria = "WHERE opponent=".$opponent." AND p.playerid=".$result[1]['playerid']."";
				} 
				if($result[$i]['deliveries']=="-"){
					$result[$i]['fivewickets'] = "-";
					$best = "-";
					$bowlave = "-";
					$sr = "-";
					$econ = "-";
					
				}
				else {
					$bb = dbToArray("call getBestBowled('".$criteria."','','order by wickets desc, runsconceded asc','limit 1');");
					$best = $bb[1]['bb'];
					$bowlave = round($result[$i]['bowlaverage'],2);
					$sr = round($result[$i]['strikerate'],2);				
					$econ = round($result[$i]['rpo'],2);
					if($result[$i]['wickets']==0){
						$bowlave = "-";
						$sr = "-";					
					}
					$m++;		
				}			
				if($resultall[1]['deliveries']=="-"){
					$bowlaveall = "-";
					$srall = "-";
					$econall = "-";
				}
				else {
					$bowlaveall = round($resultall[1]['bowlaverage'],2);
					$srall = round($resultall[1]['strikerate'],2);
					$econall = round($resultall[1]['rpo'],2);
					if($resultall[1]['wickets']=="0"){
						$bowlaveall = "-";
						$srall = "-";					
					}			
				}
				?>
				</td>
				<td title="Matches"><?php echo $result[$i]['matches']; ?></td>
				<td title="Deliveries"><?php echo $result[$i]['deliveries']; ?></td>
				<td title="Maidens"><?php echo $result[$i]['maidens']; ?></td>
				<td title="Runs conceded"><?php echo $result[$i]['runsconceded']; ?></td>
				<td title="wickets"><?php echo $result[$i]['wickets']; ?></td>
				<td title="Best bowling"><?php echo $best; ?></td>
				<td title="Bowling average"><?php echo $bowlave; ?></td>
				<td title="Five wicket bags"><?php echo $result[$i]['fivewickets']; ?></td>
				<td title="Ten wickets in a match"><?php echo $result[$i]['tenwickets']; ?></td>
				<td title="Bowling strike rate"><?php echo $sr; ?></td>
				<td title="Runs per over"><?php echo $econ; ?></td>
			</tr>
			<?php } 
			?>
			<tr>
				<td><strong><?php echo  "Overall"; ?></strong></td>
				<td><strong><?php echo  $resultall[1]['matches']; ?></strong></td>
				<td><strong><?php echo  $resultall[1]['deliveries']; ?></strong></td>
				<td><strong><?php echo  $resultall[1]['maidens']; ?></strong></td>
				<td><strong><?php echo  $resultall[1]['runsconceded']; ?></strong></td>
				<td><strong><?php echo  $resultall[1]['wickets']; ?></strong></td>
				<td><strong><?php echo 	$bestall; ?></strong></td>
				<td><strong><?php echo  $bowlaveall; ?></strong></td>
				<td><strong><?php echo  $resultall[1]['fivewickets']; ?></strong></td>
				<td><strong><?php echo  $resultall[1]['tenwickets']; ?></strong></td>
				<td><strong><?php echo  $srall; ?></strong></td>
				<td><strong><?php echo  $econall; ?></strong></td>
			</tr>
		</table>		
		</td>
	</tr>
</table>
<?php
}



function boundaries($pid, $user, $id, $error){
$b = dbToArray("call getStatsTeam('where p.playerid=".$pid."','group by compid','order by compid','');");
$all = dbToArray("call getStatsTeam('where p.playerid=".$pid."','group by p.playerid','','');");
?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
	<tr>
		<td id="greytext">Boundaries</td>
	</tr>
	<tr>
		<td id="whitetext" style="font-size:18px; padding-bottom:10px;">The percenatge of runs scored in boundaries for each competition.</td>
	</tr>
	<tr>
		<td valign="top">
		<table cellpadding="5" cellspacing="0" border="1" width="80%" id="stats">
			<tr>
				<td><strong>Competition</strong></td>
				<td><strong>Runs scored in boundaries</strong></td>
				<td><strong>Total runs scored</strong></td>
				<td><strong>Percentage</strong></td>
			</tr>
			<?php
			for($i=1;$i<=count($b);$i++){
			?>
			<tr>
				<td><?php echo $b[$i]['competition']; ?></td>
				<td>
				<?php 
				$fours = $b[$i]['fours']*4;
				$sixes = $b[$i]['sixes']*6;
				$boundaries = $fours + $sixes;
				echo $boundaries;
				?>
				</td>
				<td><?php echo $b[$i]['runs']; ?></td>
				<td>
				<?php 
				if($b[$i]['runs']>0){
					$percentage = ($boundaries/$b[$i]['runs'])*100;
					echo round($percentage,2)."%";
				}
				else {
					echo "-";
				} 
				?>
				</td>
			</tr>
			<?php } 
			?>
			<tr>
				<td><strong><?php echo "Overall"; ?></strong></td>
				<td><strong>
				<?php 
				$fours = $all[1]['fours']*4;
				$sixes = $all[1]['sixes']*6;
				$boundaries = $fours + $sixes;
				echo $boundaries;
				?>
				</strong></td>
				<td><strong><?php echo $all[1]['runs']; ?></strong></td>
				<td><strong>
				<?php 
				if($all[1]['runs']){
					$percentage = ($boundaries/$all[1]['runs'])*100;
					echo round($percentage,2)."%"; 
				}
				else {
					echo "-";
				}
				?>
				</strong></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<?php
}

function innings($user, $pid, $id, $error){
$all = dbToArray("call getStatsTeam('where p.playerid=".$pid." and mtid=2','group by p.playerid','','');");
$hsall = dbToArray("call highestIndividualScore('where p.playerid=".$pid."','order by runs desc');");
if($hsall[1]['did']==7){
	$hsall[1]['runs'] = $hsall[1]['runs']."*";
}
$bball = dbToArray("call getBestBowled('where p.playerid=".$pid." and mtid=2','','order by wickets desc, runsconceded asc', 'limit 1');");
if (count($bball[1]['bb'])>0){
	$bestall = $bball[1]['bb'];		
}
else {
	$bestall = "-";
}
$check = dbToArray("call getStatsTeam('where p.playerid=".$pid." and mtid=2','group by p.playerid','','');");

?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
	<tr>
		<td id="greytext">Batting / Fielding Statistics</td>
	</tr>
	<tr>
		<td id="whitetext" style="font-size:18px; padding-bottom:10px;">Comparing 1st innings batting & fielding performances with 2nd inning performances.</td>
	</tr>
	<tr>
		<?php if(count($check)>0){ ?>
		<td>	
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="stats">
			<tr>
				<td>Innings</td>
				<td>M</td>
				<td>I</td>
				<td>NO</td>
				<td>Runs</td>
				<td>HS</td>
				<td>Ave</td>
				<td>100</td>
				<td>50</td>
				<td>4s</td>
				<td>6s</td>
				<td>SR</td>
				<td>Ct</td>
				<td>St</td>
			</tr>
			<?php
			for($i=1;$i<=2;$i++){
			$innings = dbToArray("call getStatsTeam('where p.playerid=".$pid." and mtid=2 and matchinnings=".$i."','group by p.playerid','','');");
			if($innings[1]['innings']==0){
				$innings[1]['notouts'] = "-";
				$innings[1]['runs'] = "-";
				$innings[1]['100s'] = "-";
				$innings[1]['50s'] = "-";
				$innings[1]['fours'] = "-";
				$innings[1]['sixes'] = "-";
			}
			?>
			<tr>
				<td><?php if($i==1){ echo "1st"; } else { echo "2nd"; } ?></td>
				<td><?php echo $all[1]['matches']; ?></td>
				<td><?php echo $innings[1]['innings']; ?></td>
				<td><?php echo $innings[1]['notouts']; ?></td>
				<td><?php echo $innings[1]['runs']; ?></td>
				<td>
				<?php
				if($innings[1]['innings']<>0){
					$hs = dbToArray("call highestIndividualScore('where p.playerid=".$pid." and mtid=2 and matchinnings=".$i."','order by runs desc');");
					if($hs[1]['did']==7){
						$hs[1]['runs'] = $hs[1]['runs']."*";
					}
					echo $hs[1]['runs'];
				}
				else {
					echo "-";
				}
				?>
				</td>
				<td>
				<?php 
				if($innings[1]['innings']<>0){
					if($innings[1]['innings']==$innings[1]['notouts']){
						echo "-";
					}
					else {
						echo round($innings[1]['bataverage'],2); 
					}
				}
				else {
					echo "-";
				}				
				?>
				</td>
				<td><?php echo $innings[1]['100s']; ?></td>
				<td><?php echo $innings[1]['50s']; ?></td>
				<td><?php echo $innings[1]['fours']; ?></td>
				<td><?php echo $innings[1]['sixes']; ?></td>
				<td>
				<?php 
				if($innings[1]['innings']==""){
					echo "-";
				}
				else {
					echo round($innings[1]['srate'],2); 
				}
				?>
				</td>
				<td>
				<?php
				if($innings[1]['catches']==""){
					echo 0;
				}
				else {
					echo $innings[1]['catches']; 
				}
				?>
				</td>
				<td><?php echo $innings[1]['stumpings']; ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td><strong><?php echo "Overall"; ?></strong></td>
				<td><strong><?php echo $all[1]['matches']; ?></strong></td>
				<td><strong><?php echo $all[1]['innings']; ?></strong></td>
				<td><strong><?php echo $all[1]['notouts']; ?></strong></td>
				<td><strong><?php echo $all[1]['runs']; ?></strong></td>
				<td><strong><?php echo $hsall[1]['runs']; ?></strong></td>
				<td><strong><?php echo round($all[1]['bataverage'],2); ?></strong></td>
				<td><strong><?php echo $all[1]['100s']; ?></strong></td>
				<td><strong><?php echo $all[1]['50s']; ?></strong></td>
				<td><strong><?php echo $all[1]['fours']; ?></strong></td>
				<td><strong><?php echo $all[1]['sixes']; ?></strong></td>
				<td><strong><?php echo round($all[1]['srate'],2); ?></strong></td>
				<td><strong><?php echo $all[1]['catches']; ?></strong></td>
				<td><strong><?php echo $all[1]['stumpings']; ?></strong></td>	
			</tr>
		</table>
		</td>
		<?php
		}
		else { ?>
		<td style="padding-bottom:20px; ">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td style="font-size:18px; padding-top:20px; "><em>No statistics available to view.</em></td>
			</tr>
		</table>
		</td>		
		<?php 
		} ?>
	</tr>
	<tr>
		<td id="greytext" style="padding-top:40px;">Bowling Statistics</td>
	</tr>
	<tr>
		<td id="whitetext" style="font-size:18px; padding-bottom:10px;">Comparing 1st innings bowling performances with 2nd inning performances.</td>
	</tr>
	<tr>
		<?php if(count($check)>0){ ?>
		<td>
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="stats">
			<tr>
				<td>Innings</td>
				<td>M</td>
				<td>Balls</td>
				<td>Mdns</td>
				<td>Runs</td>
				<td>Wkts</td>
				<td>BB</td>
				<td>Ave</td>
				<td>5</td>
				<td>10</td>
				<td>SR</td>
				<td>Econ</td>
			</tr>
			<tr>				
				<?php
				if($all[1]['deliveries']=="-"){
					$bowlaveall = "-";
					$srall = "-";
					$econall = "-";
				}
				else {
					$bowlaveall = round($all[1]['bowlaverage'],2);
					$srall = round($all[1]['strikerate'],2);
					$econall = round($all[1]['rpo'],2);
					if($all[1]['wickets']=="0"){
						$bowlaveall = "-";
						$srall = "-";					
					}			
				}
				for($i=1;$i<=2;$i++){
				$innings = dbToArray("call getStatsTeam('where p.playerid=".$pid." and mtid=2 and matchinnings=".$i."','group by p.playerid','','');");
				if($innings[1]['deliveries']=="-"){
					$bowlave = "-";
					$sr = "-";
					$econ = "-";
				}
				else {
					$bowlave = round($innings[1]['bowlaverage'],2);
					$sr = round($innings[1]['strikerate'],2);				
					$econ = round($innings[1]['rpo'],2);
					if($innings[1]['wickets']==0){
						$bowlave = "-";
						$sr = "-";					
					}				
				}
				?>
				<td><?php if($i==1){ echo "1st"; } else { echo "2nd"; } ?></td>
				<td><?php echo $all[1]['matches']; ?></td>
				<td><?php echo $innings[1]['deliveries']; ?></td>
				<td><?php echo $innings[1]['maidens']; ?></td>
				<td><?php echo $innings[1]['runsconceded']; ?></td>
				<td><?php echo $innings[1]['wickets']; ?></td>
				<td>
				<?php
				$best = dbToArray("call getBestBowled('where p.playerid=".$pid." and mtid=2 and matchinnings=".$i."','','order by wickets desc, runsconceded asc', 'limit 1');");
				if($best[1]['bb']==""){
					$best[1]['bb'] = "-";
				}
				echo $best[1]['bb'];
				?>
				</td>
				<td><?php echo $bowlave; ?></td>
				<td><?php echo $innings[1]['fivewickets']; ?></td>
				<td><?php echo $innings[1]['tenwickets']; ?></td>
				<td><?php echo $sr; ?></td>
				<td><?php echo $econ; ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td><strong><?php echo  "Overall"; ?></strong></td>
				<td><strong><?php echo  $all[1]['matches']; ?></strong></td>
				<td><strong><?php echo  $all[1]['deliveries']; ?></strong></td>
				<td><strong><?php echo  $all[1]['maidens']; ?></strong></td>
				<td><strong><?php echo  $all[1]['runsconceded']; ?></strong></td>
				<td><strong><?php echo  $all[1]['wickets']; ?></strong></td>
				<td><strong><?php echo 	$bestall; ?></strong></td>
				<td><strong><?php echo  $bowlaveall; ?></strong></td>
				<td><strong><?php echo  $all[1]['fivewickets']; ?></strong></td>
				<td><strong><?php echo  $all[1]['tenwickets']; ?></strong></td>
				<td><strong><?php echo $srall; ?></strong></td>
				<td><strong><?php echo $econall; ?></strong></td>
			</tr>
		</table>	
		</td>
		<?php }
		else { ?>
		<td>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td style="font-size:18px; padding-top:20px; "><em>No statistics available to view.</em></td>
			</tr>
		</table>
		</td>
		<?php
		} ?>		
	</tr>
</table>
<?php
}
function select_players($teamid, $currentid, $id){ 
$selectplayer = dbToArray("call getPlayersForStats(".$teamid.",".$currentid.",'stats')"); 
?>
<table cellpadding="0" cellspacing="0" border="0" id="whitetext">
	<tr>
		<td style="padding-bottom:20px; font-size:14px; " ><strong>Click on the names below to view their stats.</strong>
		<?php
		if(count($selectplayer)>11){ ?>
		&nbsp;&nbsp;<img src="images/Question.png" onMouseOver="Tip('Use the scroll bar to scroll through the list of players.', BGCOLOR,'#ffffff', BORDERCOLOR,'#dddddd', DELAY,300, STICKY,false, CLOSEBTN,false, CLICKCLOSE,true, FOLLOWMOUSE, false, PADDING,16, SHADOW,true, SHADOWCOLOR,'#cccccc', SHADOWWIDTH,2, WIDTH,150, FIX, [this,-125,5]);" onMouseOut="UnTip();">
		<?php } ?>
		</td>
	</tr>
	<tr>
		<td style="font-size:16px; ">
		<div class="flexcroll" align="left" style="height:400px; ">
		<?php
		for($i=1;$i<=count($selectplayer);$i++){	
			echo "<a href='viewplayerstats.php?pid=".$selectplayer[$i]['userid']."&id=".$id."' style='cursor:pointer;'>".stripslashes($selectplayer[$i]['player'])."</a>";
			echo "<br><br>";
		}
		?>
		</div>
		</td>
</table>
<?php
}

function check_form_fields(){
	$error->general = 0;
	if($_REQUEST['pid']=="Select Player") $error->general = 1;
	return $error;
}
?>