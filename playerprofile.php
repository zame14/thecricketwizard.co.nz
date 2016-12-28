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
//print_r($_REQUEST);
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
$error=0;
$pwderror=0;
$new=0;
$errormsg='';
$check=0;
$user = getUserDetails($_SESSION['userid']);
//$_REQUEST[id] - is the id of the profile being viewed.
if(isset($_REQUEST['id'])){
	//Check the user is viewing a profile from his/her team
	$check = dbToArray("call getPlayers('WHERE pt.teamid=".$user->teamid." AND pt.userid=".$_REQUEST["id"]."','','');");
	if($check[1]["userid"]==""){
		//not viewing correct team
		redirect_rel("teamroster.php");
	}
}
else {
	redirect_rel("teamroster.php");
}
//$profile = getProfileId($_SESSION['userid']);
$userprofile = getUserDetails($_REQUEST["id"]);
$colour = "#85C226";
$text = "Player Profile";

if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="updategoals"){
		$updategoals = dbToArray("call updateGoals(".$_SESSION['userid'].",'".addslashes($_REQUEST['goals'])."');");
	}
	else if($_REQUEST['action']=="resetpassword"){
		$updatepassword= dbToArray("call updatePassword(".$_REQUEST["id"].",'".$userprofile->teampassword."');");
		//redirect_rel('process.php?id=1&np=playerprofile.php');
		password_reset($userprofile->firstname);
	}
	else if($_REQUEST['action']=="changepassword"){
		$errorobj = check_password_fields($_REQUEST["id"],$_REQUEST['currentpassword']);
		$pwderror = $errorobj->general;
		$new = $errorobj->newpwd;
		$check = $errorobj->chk;
		$errormsg = $errorobj->msg;
		if($pwderror==0&&$new==0&&$check==0){
			$change = dbToArray("call updatePassword(".$_REQUEST["id"].",'".$_REQUEST['newpasswordin']."');");
			password_changed($user);
		}
	}
}
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px; ">
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
						<td rowspan="2" valign="top" bgcolor="#212121"; width="900" height="100%">
						<table cellpadding="0" cellspacing="0" width="100%" border="0" id="mainfrm" height="100%">
							<tr>
								<td align="left" valign="middle" style="padding-left:25px; padding-top:10px;"><?php heading($user, $userprofile); ?></td>
							</tr>
							<tr>
								<td style="padding-bottom:0px; padding-left:25px; padding-top:0px; padding-right:400px;">
								<div class="line"> &nbsp;</div>
								</td>
							</tr>
							<tr>
								<td style=" padding-left:5px; padding-right:5px;"valign="top">
								<?php profile($_REQUEST["id"], $userprofile->profilesetup, $user->role, $_SESSION['userid'], $pwderror, $check, $new, $userprofile, $errormsg); ?>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center" style="padding-bottom:10px; padding-top:20px; "><input type="button" value="Back" onClick="javascript:document.location='teamroster.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
							</tr>
						</table>
						</td>
						<td bgcolor="<?php echo $colour; ?>" width="30" height="10%" style="table-layout:fixed ">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="padding-top:10px; padding-left:5px; padding-right:5px;" width="160">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="center" style="padding-bottom:30px;"><?php mainmenu2(); ?></td>
							</tr>
							<?php
							//if there is only one player in the team, dont display menu
							$check = dbToArray("call getPlayers('WHERE pt.teamid=".$user->teamid."','','');");
							if(count($check)>1){ ?>
							<tr>
								<td><?php select_players($user->teamid, $_REQUEST["id"]); ?></td>
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
<script language="javascript">
function View(val){
	frm = document.whichview;
	frm.details.value = val;
	frm.submit();
}

function ResetPassword(theid){
	frm = document.thefrm;
	frm.action.value = "resetpassword";
	frm.id.value = theid;
	frm.submit();
}

function ChangePassword(theid){
	frm = document.thefrm;
	frm.action.value = "changepassword";
	frm.id.value = theid;
	frm.submit();
}
</script>
<?php
function profile($profileid, $setup, $role, $userid,$pwderror, $check, $new, $userprofile, $errormsg){
//data for player details.
$playerdetails = dbToArray("call getPlayerProfile(".$profileid.")");

if(isset($_REQUEST['goals'])&&$_REQUEST['goals']<>""){
	$goals = $_REQUEST['goals'];
}
else{
	$goals = $playerdetails[1]['goals'];
}

if($setup==1){
	//player has updated his/her profile
	($playerdetails[1]['nickname']=='' ) ? $nickname='&nbsp;' : $nickname = $playerdetails[1]['nickname'];
	($playerdetails[1]['playernum']=='' ) ? $playernum='&nbsp;' : $playernum = $playerdetails[1]['playernum'];
	($playerdetails[1]['batstyle']=='' ) ? $batstyle='&nbsp;' : $batstyle = $playerdetails[1]['batstyle'];
	($playerdetails[1]['bowlstyle']=='' ) ? $bowlstyle='&nbsp;' : $bowlstyle = $playerdetails[1]['bowlstyle'];
	($playerdetails[1]['playingrole']=='' ) ? $playingrole='&nbsp;' : $playingrole = $playerdetails[1]['playingrole'];
	($playerdetails[1]['email']=='' ) ? $email='&nbsp;' : $email = $playerdetails[1]['email'];
}
else {
	$nickname = '&nbsp';
	$playernum = '&nbsp';
	$batstyle = '&nbsp';
	$bowlstyle = '&nbsp';
	$playingrole = '&nbsp';
	$email = '&nbsp';
}

//data for honours
$honours = dbToArray("call getHonours('where u.userid=".$profileid."','order by m.date asc','')");


//data for stats
$stats = dbToArray("call getStatsTeam('where p.playerid=".$profileid."','group by competition','order by competition','');");
$count = count($stats);

$statsall = dbToArray("call getStatsTeam('where p.playerid=".$profileid."','group by p.playerid','','');");

$bball = dbToArray("call getBestBowled('where p.playerid=".$profileid."','','order by wickets desc, runsconceded asc','limit 1');");
if ($bball[1]['bb']<>""){
	$bestall = $bball[1]['bb'];		
}
else {
	$bestall = "-";
}
?>
<table cellpadding="5" cellspacing="0" border="0" width="100%" id="whitetext" style="font-size:18px;">
	<tr>
		<td width="30%" style=" padding-left:20px; color:#CCCCCC;">Nickname</td>
		<td width="60%" style=" padding-left:20px;"><?php echo $nickname; ?></td>
		<td rowspan="6" width="10%"><?php ad("250x250",1); ?></td>
	<tr>
		<td style=" padding-left:20px; color:#CCCCCC;">Playing No.</td>
		<td style=" padding-left:20px;"><?php echo $playernum; ?></td>
	</tr>
	<tr>
		<td style=" padding-left:20px; color:#CCCCCC;">Playing Role</td>
		<td style=" padding-left:20px;"><?php echo $playingrole; ?></td>
	</tr>
	<tr>
		<td style=" padding-left:20px; color:#CCCCCC;">Batting Style</td>
		<td style=" padding-left:20px;"><?php echo $batstyle; ?></td>
	</tr>
	<tr>
		<td style=" padding-left:20px; color:#CCCCCC;">Bowling Style</td>
		<td style=" padding-left:20px;"><?php echo $bowlstyle; ?></td>
	</tr>
	<tr>
		<td style=" padding-left:20px; color:#CCCCCC;">Honours</td>
		<td style=" padding-left:20px;">
		<?php if(count($honours)>=1){ ?>
		<table cellpadding="0" cellspacing="0" id="whitetext" border="0">
			<tr>
				<td style="font-size:18px;"><?php echo count($honours); ?></td>
				<td style="padding-left:10px; "><a class="a2" href="honours.php?pid=<?php echo $profileid ?>&p">View honours board</a></td>
			</tr>
		</table>
		<?php }
		else {
			echo count($honours);
		} ?>
		</td>
	</tr>
	<tr>
		<td colspan="3" style=" padding-left:20px; padding-top:20px;" id="goldtext">Batting & Fielding Averages</td>
	</tr>
	<tr>
		<td colspan="3" style="padding-left:15px; padding-right:15px; ">
		<table cellpadding="5" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td>Competition</td>
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
			if($count>0){
				for($i=1;$i<=$count;$i++){
					if($stats[$i]['innings']==0){
						$stats[$i]['notouts']="-";
						$stats[$i]['100s']="-";
						$stats[$i]['50s']="-";
						$stats[$i]['sixes']="-";
						$stats[$i]['fours']="-";
					}
				?>
				<tr>
					<td><?php echo $stats[$i]['competition']; ?></td>
					<td title="Matches"><?php echo $stats[$i]['matches']; ?></td>
					<td title="Innings"><?php echo $stats[$i]['innings']; ?></td>
					<td title="Not outs"><?php echo $stats[$i]['notouts']; ?></td> 
					<td title="Runs"><?php echo $stats[$i]['runs']; ?></td>
					<td title="Highest Score">
					<?php 
					if($stats[$i]['innings']<>0){
						$hs = dbToArray("call highestIndividualScore('WHERE m.compid=".$stats[$i]['compid']." AND p.playerid=".$stats[1]['playerid']."','order by runs desc');");
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
						if(($stats[$i]['innings']==$stats[$i]['notouts'])||($stats[$i]['innings']==0)){
							echo"-";
						}
						else {
							echo round($stats[$i]['bataverage'],2); 
						}
						?>
						</td>
					<td title="100s"><?php echo $stats[$i]['100s']; ?></td>
					<td title="50"><?php echo $stats[$i]['50s']; ?></td>
					<td title="4s"><?php echo $stats[$i]['fours']; ?></td>
					<td title="6s"><?php echo $stats[$i]['sixes']; ?></td>
					<td title="Batting strike rate">
					<?php
					if($stats[$i]['ballsfaced']<>"-"){
						$sr = dbToArray("call getStatsTeam('where p.playerid=".$profileid." AND bat.ballsfaced is not null AND m.compid=".$stats[$i]['compid']."','group by competition','order by competition','');");					
						echo round($sr[1]['srate'],2); 
					}
					else {
						echo "-";
					}
					?>
					</td>
					<td title="Catches"><?php echo $stats[$i]['catches']; ?></td>
					<td title="Stumpings"><?php echo $stats[$i]['stumpings']; ?></td>
				</tr>
				<?php 
				} 
				?>
				<tr>
					<td><strong><?php echo "Overall"; ?></strong></td>
					<td><strong><?php echo $statsall[1]['matches']; ?></strong></td>
					<td><strong><?php echo $statsall[1]['innings']; ?></strong></td>
					<td><strong><?php echo $statsall[1]['notouts']; ?></strong></td>
					<td><strong><?php echo $statsall[1]['runs']; ?></strong></td>
					<td><strong>
					<?php 
					$hsall = dbToArray("call highestIndividualScore('where p.playerid=".$profileid."','order by runs desc');");
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
					if($statsall[1]['innings']==$statsall[1]['notouts']){
						echo"-";
					}
					else {
						echo round($statsall[1]['bataverage'],2); 
					}
					?>
					</td>
					</strong>
					<td><strong><?php echo $statsall[1]['100s']; ?></strong></td>
					<td><strong><?php echo $statsall[1]['50s']; ?></strong></td>
					<td><strong><?php echo $statsall[1]['fours']; ?></strong></td>
					<td><strong><?php echo $statsall[1]['sixes']; ?></strong></td>
					<td><strong>
					<?php 
					if($statsall[1]['ballsfaced']<>"-"){
						$srall = dbToArray("call getStatsTeam('where p.playerid=".$profileid." AND bat.ballsfaced is not null','group by p.playerid','','');");					
						echo round($srall[1]['srate'],2); 
					}
					else {
						echo "-";
					}
					?>
					</strong></td>
					<td><strong><?php echo $statsall[1]['catches']; ?></strong></td>
					<td><strong><?php echo $statsall[1]['stumpings']; ?></strong></td>	
				</tr>
				<?php
				}
				else { ?>
				<tr>
					<td>&nbsp;</td>
					<td>0</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
				</tr>
				<?php
				}
				?>
		</table>		
		</td>
	</tr>
	<tr>
		<td colspan="3" style=" padding-left:20px; padding-top:20px;" id="goldtext">Bowling Averages</td>
	</tr>
	<tr>
		<td colspan="3" style="padding-left:15px; padding-right:15px; ">
		<table cellpadding="5" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td>Competition</td>
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
			if($count>0){
				$m = 1;
				for($i=1;$i<=$count;$i++){			
				?>
				<tr>
					<td>
					<?php
					echo $stats[$i]['competition'];
					if($stats[$i]['deliveries']=="-"){
						$stats[$i]['fivewickets'] = "-";
						$best = "-";
						$bowlave = "-";
						$sr = "-";
						$econ = "-";
						
					}
					else {
						$bb = dbToArray("call getBestBowled('WHERE m.compid=".$stats[$i]['compid']." AND p.playerid=".$stats[1]['playerid']."','','order by wickets desc, runsconceded asc','limit 1');");
						$best = $bb[1]['bb'];
						$bowlave = round($stats[$i]['bowlaverage'],2);
						$sr = round($stats[$i]['strikerate'],2);				
						$econ = round($stats[$i]['rpo'],2);
						if($stats[$i]['wickets']==0){
							$bowlave = "-";
							$sr = "-";					
						}
						$m++;		
					}			
					if($statsall[1]['deliveries']=="-"){
						$bowlaveall = "-";
						$srall = "-";
						$econall = "-";
					}
					else {
						$bowlaveall = round($statsall[1]['bowlaverage'],2);
						$srall = round($statsall[1]['strikerate'],2);
						$econall = round($statsall[1]['rpo'],2);
						if($statsall[1]['wickets']=="0"){
							$bowlaveall = "-";
							$srall = "-";					
						}			
					}
					?>
					</td>
					<td title="Matches"><?php echo $stats[$i]['matches']; ?></td>
					<td title="Deliveries"><?php echo $stats[$i]['deliveries']; ?></td>
					<td title="Maidens"><?php echo $stats[$i]['maidens']; ?></td>
					<td title="Runs conceded"><?php echo $stats[$i]['runsconceded']; ?></td>
					<td title="wickets"><?php echo $stats[$i]['wickets']; ?></td>
					<td title="Best bowling"><?php echo $best; ?></td>
					<td title="Bowling average"><?php echo $bowlave; ?></td>
					<td title="Five wicket bags"><?php echo $stats[$i]['fivewickets']; ?></td>
					<td title="Ten wickets in a match"><?php echo $stats[$i]['tenwickets']; ?></td>
					<td title="Bowling strike rate"><?php echo $sr; ?></td>
					<td title="Runs per over"><?php echo $econ; ?></td>
				</tr>
				<?php } 
				?>
				<tr>
					<td><strong><?php echo  "Overall"; ?></strong></td>
					<td><strong><?php echo  $statsall[1]['matches']; ?></strong></td>
					<td><strong><?php echo  $statsall[1]['deliveries']; ?></strong></td>
					<td><strong><?php echo 	$statsall[1]['maidens']; ?></strong></td>
					<td><strong><?php echo  $statsall[1]['runsconceded']; ?></strong></td>
					<td><strong><?php echo  $statsall[1]['wickets']; ?></strong></td>
					<td><strong><?php echo 	$bestall; ?></strong></td>
					<td><strong><?php echo  $bowlaveall; ?></strong></td>
					<td><strong><?php echo 	$statsall[1]['fivewickets']; ?></strong></td>
					<td><strong><?php echo  $statsall[1]['tenwickets']; ?></strong></td>
					<td><strong><?php echo  $srall; ?></strong></td>
					<td><strong><?php echo  $econall; ?></strong></td>
				</tr>
				<?php
				}
				else { ?>
				<tr>
					<td>&nbsp;</td>
					<td>0</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
				</tr>
				<?php
				}
				?>
		</table>			
		</td>
	</tr>
	<?php
	if($count>0){ ?>
	<tr>
		<td colspan="3" style="padding-left:20px; padding-top:10px;"><em>Click <a class="aWhite" href="playerstatshome.php?pid=<?php echo $profileid; ?>&p"><strong>here</strong></a> to view complete list statistics.</em></td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="3" style="padding-top:20px; padding-left:20px;" id="goldtext">Career Statistics</td>
	</tr>
	<tr>
		<td colspan="3" style="padding-left:20px;"><?php player_stats($profileid); ?></td>
	</tr>
	<tr>
		<td colspan="4" style="padding-top:20px; padding-left:20px; "><div class="line"> &nbsp;</div></td>
	</tr>
	<tr>
		<td colspan="3"><?php personal_details($profileid, $setup, $email); ?></td>
	</tr>
	<?php
	if($profileid==$userid){ ?>
	<tr>
		<td colspan="4" style="padding-top:20px; padding-left:20px; "><div class="line"> &nbsp;</div></td>
	</tr>	
	<tr>
		<td colspan="3"><?php goals($goals, $profileid); ?></td>
	</tr>
	<?php } 
	if($role=="Team Admin" || $profileid==$userid){
	?>
	<tr>
		<td colspan="4" style="padding-top:20px; padding-left:20px; "><div class="line"> &nbsp;</div></td>
	</tr>
	<tr>
		<td colspan="3" style="padding-left:20px; "><?php administration($profileid,$pwderror, $check, $new, $userprofile,$errormsg); ?></td>
	</tr>
	<?php } ?>
</table>
<?php
}


function player_stats($profileid){ ?>
<table cellpadding="0" cellspacing="0" width="100%" id="whitetext">
	<tr>
		<?php
		$debut = dbToArray("call getMatchesPlayedPlayer('where p.playerid=".$profileid."','order by UNIX_TIMESTAMP(date) asc','limit 1');");
		$last = dbToArray("call getMatchesPlayedPlayer('where p.playerid=".$profileid."','order by UNIX_TIMESTAMP(date) desc','limit 1');");
		?>
		<td style="font-size:18px; " width="15%">Debut match:</td>
		<td colspan="3">
		<?php 
		if(count($debut)>0){ ?>	
		<table cellpadding="0" cellspacing="0" width="100%" id="whitetext" border="0">
			<tr>
				<td style="font-size:18px; " width="55%"><?php echo $debut[1]['teamname']." vs ".$debut[1]['opponent']." at ".$debut[1]['venue'].", ".$debut[1]['date']."&nbsp;&nbsp;&nbsp;<a class=a2 style='font-size:12px;' href='viewmatch.php?id=".$debut[1]['matchid']."&pid=".$profileid."&p=1'>scorecard</a>"; ?></td>
			</tr>
		</table>
		<?php }
		else {
			echo "-";
		} ?>
		</td>
	</tr>
	<tr>
		<td style="font-size:18px;">Last match:</td>
		<td colspan="3">
		<?php 
		if(count($debut)>0){ ?>	
		<table cellpadding="0" cellspacing="0" width="100%" id="whitetext" border="0">
			<tr>
				<td style="font-size:18px; " width="55%"><?php echo $last[1]['teamname']." vs ".$last[1]['opponent']." at ".$last[1]['venue'].", ".$last[1]['date']."&nbsp;&nbsp;&nbsp;<a class=a2 style='font-size:12px;' href='viewmatch.php?id=".$last[1]['matchid']."&pid=".$profileid."&p=1'>scorecard</a>"; ?></td>		
				</td>
			</tr>
		</table>
		<?php }
		else {
			echo "-";
		} ?>
		</td>	
	</tr>
	<?php 
	if(count($debut)>0){ ?>	
	<tr>
		<td style="padding-top:15px; font-size:18px;" colspan="4"><em>Click <a class="aWhite" href="viewplayermatches.php?id=<?php echo $profileid; ?>&p"><strong>here</strong></a> to view complete list of matches played.</em></td>
	</tr>
	<?php } ?>
</table>
<?php 
}

function goals($goals,$profileid){
?>
	<table cellpadding="0" cellspacing="0" width="100%">	
		<tr>
			<td colspan="4" style=" padding-left:20px;">	
			<form action="playerprofile.php" name="profile" method="post">
			<input type="hidden" name="action">
			<input type="hidden" name="id">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" id="playerprofile">
				<tr>
					<td style="color:#FFFFFF;">
					<strong>Goals</strong> (these are private to you)<br><br>
					<textarea name="goals" cols="80" rows="10"><?php if($goals=="") { echo "Enter goals here..."; } else { echo $goals;  }?></textarea>
					</td>
				</tr>
				<tr>
					<td align="left" style="padding-top:10px;"><input type="button" value="Update" onClick="Goals(<?php echo $profileid; ?>);" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
				</tr>
			</table>		
			</form>
			</td>
		</tr>
	</table>
	<?php 
} 

function personal_details($profileid, $setup, $email){
$personaldetails = dbToArray("call getPersonalDetails(".$profileid.")");

if($setup==1){
	($personaldetails[1]['address1']=='' ) ? $address1='&nbsp;' : $address1 = $personaldetails[1]['address1'];
	($personaldetails[1]['address2']=='' ) ? $address2='&nbsp;' : $address2 = $personaldetails[1]['address2'];
	($personaldetails[1]['suburb']=='' ) ? $suburb='&nbsp;' : $suburb = $personaldetails[1]['suburb'];
	($personaldetails[1]['city']=='' ) ? $city='&nbsp;' : $city = $personaldetails[1]['city'];
	($personaldetails[1]['phone']=='' ) ? $phone='&nbsp;' : $phone = $personaldetails[1]['phone'];
	($personaldetails[1]['cellphone']=='' ) ? $cellphone='&nbsp;' : $cellphone = $personaldetails[1]['cellphone'];
}
else {
	$address1 = '&nbsp';
	$address2 = '&nbsp';
	$suburb = '&nbsp';
	$city = '&nbsp';
	$phone = '&nbsp';
	$cellphone = '&nbsp';
}
?>
<table cellpadding="5" cellspacing="0" width="100%" id="whitetext" style="font-size:18px;" border="0">
	<tr>
		<td colspan="2" style="padding-left:20px; padding-bottom:10px;"><strong>Personal Details</strong></td>
	</tr>
	<tr>
		<td style=" padding-left:20px; color:#CCCCCC;" rowspan="2" valign="top" width="15%" >Address</td>
		<td style=" padding-left:20px;" colspan="3"><?php echo $address1; ?></td>
	</tr>
	<tr>
		<td colspan="3"><?php echo $address2; ?></td>
	</tr>
	<tr>
		<td style=" padding-left:20px; color:#CCCCCC;">Suburb</td>
		<td style=" padding-left:20px;" colspan="3"><?php echo $suburb; ?></td>
	</tr>
	<tr>
		<td style=" padding-left:20px; color:#CCCCCC;">City</td>
		<td style=" padding-left:20px;" colspan="3"><?php echo $city; ?></td>
	</tr>
	<tr>
		<td style=" padding-left:20px; color:#CCCCCC;">Email</td>
		<td style=" padding-left:20px;" colspan="3"><?php echo $email; ?></td>
	</tr>
	<tr>
		<td style=" padding-left:20px; color:#CCCCCC;">Home phone</td>
		<td style=" padding-left:20px;" colspan="3"><?php echo $phone; ?></td>
	</tr>
	<tr>
		<td style=" padding-left:20px; color:#CCCCCC;">Mobile</td>
		<td style=" padding-left:20px;" colspan="3"><?php echo $cellphone; ?></td>
	</tr>
</table>
<?php
}

function administration($profileid,$pwderror, $check, $new, $userprofile,$errormsg) { ?>
<form name="thefrm" action="playerprofile.php" method="post">
<input type="hidden" name="action">
<input type="hidden" name="id">
<table cellpadding="5" cellspacing="0" width="100%" id="whitetext" style="font-size:18px;" border="0">
	<tr>
		<td><strong>Administration</strong></td>
	</tr>
	<?php
	if($profileid==$_SESSION["userid"]){ ?>
	<tr>
		<td>Use the form below to change your password.</td>
	</tr>
	<tr>
		<td style="padding-top:10px; ">
		<table border="0" id="whiteborder" bgcolor="#212121;" cellpadding="5">
		<?php
		if($pwderror==1||$check==1||$new==1){ ?>
			<tr>
				<td colspan="2"><?php validation($errormsg);?></td>
			</tr>
		<?php } ?>
		<tr>
			<td align="right">Current password:</td>
			<td><input type="password" name="currentpassword"></td>
		</tr>
		<tr>
			<td align="right">New password:</td>
			<td><input type="password" name="newpasswordin"></td>
		</tr>
		<tr>
			<td align="right">Re-enter password:</td>
			<td><input type="password" name="newpassword2"></td>
		</tr>
		<tr>
			<td colspan="2" align="right">
			<input type="button" value="Submit" onClick="ChangePassword(<?php echo $profileid; ?>);" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<?php
	}
	else { ?>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" border="0" id="whitetext" style="font-size:18px; ">
			<tr>
				<td colspan="3" style="padding-bottom:10px; ">If a player has forgotten his/her password, click the Reset button to reset it back to the default team password.</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="5" cellspacing="0" border="0" id="whiteborder" bgcolor="#212121;" style="font-size:18px;" width="450">
					<tr>
						<td width="45%" style="padding-bottom:5px; ">Username:</td>
						<td><strong><?php echo $userprofile->username; ?></strong></td>
					</tr>
					<tr>
						<td style="padding-bottom:5px; ">Default team password:</td>
						<td><strong><?php echo $userprofile->teampassword; ?></strong></td>									
					</tr>
					<tr>
						<td style="padding-bottom:5px; ">Activity count:</td>
						<?php
						$count = dbToArray("call getActivityCount(".$profileid.");");
						if(count($count)==0){
							$activitycount=0;
						}
						else {
							$activitycount = $count[1]['activity'];
						}
						?>
						<td><strong><?php echo $activitycount; ?></strong></td>									
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td align="left"><input type="button" value="Reset" alt="Reset password" style=" cursor:pointer; " onClick="ResetPassword(<?php echo $profileid; ?>);" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>					
					</tr>
				</table>	
				</td>
			</tr>
		</table>	
		</td>
	</tr>
	<?php
	}
	?>
</table>	
</form>
<?php
}
?>
<script language="javascript">
function Goals(theid){
	frm = document.profile;
	frm.action.value = "updategoals";
	frm.id.value=theid;
	frm.submit();
}
</script>


<?php
function heading($user, $userprofile){ ?>
<form action="editplayerprofile.php" name="editprofile" method="post">
<input type="hidden" name="id">
<table cellpadding="0" cellspacing="0" width="100%" border="0" id="whitetext">
<?php
if($user->role=="Team Admin" || $userprofile->userid == $_SESSION['userid']){?>
	<tr>
		<td style="font-size:24; "><strong><?php echo $userprofile->firstname." ".stripslashes($userprofile->lastname); ?></strong>
		&nbsp;&nbsp;<input type="button" value="Edit" onClick="editProfile(<?php echo $userprofile->userid; ?>)" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
		</td>
	</tr>
	<tr>
		<td style="font-size:20;"><strong><?php echo $user->teamname; ?></strong></td>
	</tr>
<?php
}
else { ?>
	<tr>
		<td style="font-size:24; "><strong><?php echo $userprofile->firstname." ".stripslashes($userprofile->lastname); ?></strong></td>
	</tr>
	<tr>
		<td style="font-size:20;"><strong><?php echo $user->teamname; ?></strong></td>
	</tr>
<?php } ?>
</table>
</form>
<script language="javascript">
function editProfile(theid){
	frm = document.editprofile;
	frm.id.value = theid;
	frm.submit();
}
</script>
<?php
}

function check_password_fields($id,$existingpwd){
	$error->general = 0;
	$error->newpwd = 0;
	$error->chk = 0;
	$error->msg = '';
	if($_REQUEST['currentpassword']=="") {
		$error->general = 1;
		$error->msg = "Please fill in all fields.";
	}
	else {
		$check = dbToArray("call checkPassword(".$id.",'".$existingpwd."');");
		if($check[1]['correctPassword']==0){
			$error->chk = 1;
			$error->msg = "The current password entered is not correct.";
		}	
	}
	if($_REQUEST['newpasswordin']=="") {
		$error->general = 1;
		$error->msg = "Please fill in all fields.";
	}
	if($_REQUEST['newpassword2']=="") {
		$error->general = 1;
		$error->msg = "Please fill in all fields.";
	}
	if($_REQUEST['newpasswordin']<>$_REQUEST['newpassword2']){
		$error->newpwd = 1;
		$error->msg = "The new passwords you entered do not match.";
	}
	return $error;
}

function password_changed($user){
?>
<table cellpadding="5" cellspacing="0" border="0" id="loginfailed" bgcolor="#CCCCCC" width="100%">
	<tr>
		<td style="padding-left:120px;">
		<?php
		echo "Hi ".$user->firstname.", your password has been changed.  Use your new password next time you log in.";
		?>
		</td>
	</tr>
</table>
<?php
}
function password_reset($firstname){
?>
<table cellpadding="5" cellspacing="0" border="0" id="loginfailed" bgcolor="#CCCCCC" width="100%">
	<tr>
		<td style="padding-left:120px;">
		<?php
		echo $firstname."'s password has been reset.";
		?>
		</td>
	</tr>
</table>
<?php
}
function select_players($teamid, $currentid){ 
$selectplayer = dbToArray("call getPlayersForStats(".$teamid.",".$currentid.",'profile')"); 
?>
<table cellpadding="0" cellspacing="0" border="0" id="whitetext">
	<tr>
		<td style="padding-bottom:20px; font-size:14px; " ><strong>Click on the names below to view other profiles.</strong>
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
			echo "<a href='playerprofile.php?id=".$selectplayer[$i]['userid']."' style='cursor:pointer;'>".stripslashes($selectplayer[$i]['player'])."</a>";
			echo "<br><br>";
		}
		?>
		</div>
		</td>
</table>
<?php
}
?>