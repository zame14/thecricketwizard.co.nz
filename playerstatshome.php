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
if(isset($_REQUEST['pid'])&&$_REQUEST['pid']<>""){
	$pid = $_REQUEST['pid'];
}
else {
	redirect_rel('statshome.php');
}

$sql = "call getPlayerStatsComp(".$pid.");";
$result = dbToArray($sql);
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px;">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" border="0" id="main" style="table-layout:fixed;">
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
								<td align="left" valign="middle" style="font-size:24px; padding-left:20px; padding-top:10px; padding-bottom:20px;" id="whitetext" colspan="3"><strong><?php echo stripslashes($result[1]['player']); ?></strong></td>
							</tr>
							<tr>
								<td align="left" style="font-size:18px; padding-left:20px; padding-bottom:20px; padding-right:10px;" id="whitetext"><?php 
								echo "Welcome to the statistics home page for individual players.  Below are the different options available for you to view."; ?>
								</td>
							</tr>
							<tr>
								<td width="100%" valign="top" style="padding-left:15px; padding-right:15px; padding-top:10px; padding-bottom:15px;">
								<?php statsMenu($pid); ?>
								</td>
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
							<tr>
								<td><?php select_players($user->teamid, $_REQUEST["pid"]); ?></td>
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
<?php
function statsMenu($pid){
?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px;"><img src="images/User.png" onClick="javascript:document.location='viewplayerstats.php?pid=<?php echo $pid ?>&id=competition'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px; "><strong><a href="viewplayerstats.php?pid=<?php echo $pid ?>&id=competition">Competition</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:10px; ">
				Click <a href="viewplayerstats.php?pid=<?php echo $pid ?>&id=competition">here</a> to view a breakdown of the players batting, bowling and fielding statistics by each competition your team competes in.
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="padding-bottom:10px; "><div class="line"> &nbsp;</div></td>
			</tr>
		</table>
		</td>
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/User.png" onClick="javascript:document.location='viewplayerstats.php?pid=<?php echo $pid ?>&id=season'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="viewplayerstats.php?pid=<?php echo $pid ?>&id=season">Season</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:10px;">
				Click <a href="viewplayerstats.php?pid=<?php echo $pid ?>&id=season"><strong></strong>here</a> to view a breakdown of the players batting, bowling and fielding statistics by season.
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="padding-bottom:10px; "><div class="line"> &nbsp;</div></td>
			</tr>
		</table>		
		</td>
	</tr>
	<tr>
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/User.png" onClick="javascript:document.location='viewplayerstats.php?pid=<?php echo $pid ?>&id=opposition'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="viewplayerstats.php?pid=<?php echo $pid ?>&id=opposition">Opposition</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:40px; ">
				Click <a href="viewplayerstats.php?pid=<?php echo $pid ?>&id=opposition">here</a> to view a breakdown of the players batting, bowling and fielding statistics by opposition.
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="padding-bottom:10px; "><div class="line"> &nbsp;</div></td>
			</tr>
		</table>		
		</td>
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/Stats 3.png" onClick="javascript:document.location='generategraph.php?pid=<?php echo $pid ?>&id=dismissals'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="generategraph.php?pid=<?php echo $pid ?>&id=dismissals">Mode of Dismissal</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:10px; ">
				Click <a href="generategraph.php?pid=<?php echo $pid ?>&id=dismissals">here</a> to generate a graph that shows a breakdown of the number of times a player has been dismissed by a certain mode of dismissal.
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-left:42px; padding-bottom:15px; "><a href="http://www.ebrueggeman.com/phpgraphlib" target="_blank"><img src="http://www.ebrueggeman.com/phpgraphlib/images/phpgraphlib_80x15_green.png" alt="PHPGraphLib - Click For Official Site" width="80" height="15" align="top" style="margin-right:10px; border:0" /></a></td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="padding-bottom:10px; "><div class="line"> &nbsp;</div></td>
			</tr>
		</table>		
		</td>
	</tr>
	<tr>
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/Stats 3.png" onClick="javascript:document.location='generategraph.php?pid=<?php echo $pid ?>&id=scores'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="generategraph.php?pid=<?php echo $pid ?>&id=scores">Scores</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:10px; ">
				Click <a href="generategraph.php?pid=<?php echo $pid ?>&id=scores">here</a> to generate a graph that shows a breakdown of the number of times a player has scored within a certain run range.
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-left:42px; padding-bottom:15px; "><a href="http://www.ebrueggeman.com/phpgraphlib" target="_blank"><img src="http://www.ebrueggeman.com/phpgraphlib/images/phpgraphlib_80x15_green.png" alt="PHPGraphLib - Click For Official Site" width="80" height="15" align="top" style="margin-right:10px; border:0" /></a></td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="padding-bottom:10px; "><div class="line"> &nbsp;</div></td>
			</tr>
		</table>		
		</td>
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/Stats 3.png" onClick="javascript:document.location='generategraph.php?pid=<?php echo $pid ?>&id=wkts'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="generategraph.php?pid=<?php echo $pid ?>&id=wkts">Wickets</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:10px; ">
				Click <a href="generategraph.php?pid=<?php echo $pid ?>&id=wkts">here</a> to generate a graph that shows a breakdown of the number of times a player has taken a certain number of wickets.
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-left:42px; padding-bottom:15px; "><a href="http://www.ebrueggeman.com/phpgraphlib" target="_blank"><img src="http://www.ebrueggeman.com/phpgraphlib/images/phpgraphlib_80x15_green.png" alt="PHPGraphLib - Click For Official Site" width="80" height="15" align="top" style="margin-right:10px; border:0" /></a></td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="padding-bottom:10px; "><div class="line"> &nbsp;</div></td>
			</tr>
		</table>		
		</td>
	</tr>
	<tr>
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/User.png" onClick="javascript:document.location='viewplayerstats.php?pid=<?php echo $pid ?>&id=boundaries'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="viewplayerstats.php?pid=<?php echo $pid ?>&id=boundaries">Boundaries</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:37px;">
				Click <a href="viewplayerstats.php?pid=<?php echo $pid ?>&id=boundaries">here</a> to view the percentage of runs scored in boundaries for each competition. 
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="padding-bottom:10px; "><div class="line"> &nbsp;</div></td>
			</tr>
		</table>			
		</td>
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px;"><img src="images/User.png" onClick="javascript:document.location='viewplayerstats.php?pid=<?php echo $pid ?>&id=innings'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="viewplayerstats.php?pid=<?php echo $pid ?>&id=innings">1st Innings vs 2nd Innings</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:15px; ">
				Click <a href="viewplayerstats.php?pid=<?php echo $pid ?>&id=innings">here</a> to compare first innings performances with second innings performances.
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="padding-bottom:10px; "><div class="line"> &nbsp;</div></td>
			</tr>
		</table>	
		</td>
	</tr>
	<tr>
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/Stats 3.png" onClick="javascript:document.location='generategraph.php?pid=<?php echo $pid ?>&id=runs'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="generategraph.php?pid=<?php echo $pid ?>&id=runs">Career Runs</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:10px;">
				Click <a href="generategraph.php?pid=<?php echo $pid ?>&id=runs">here</a> to generate a graph of the runs a player has scored over his/her career. 
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-left:42px; padding-bottom:15px; "><a href="http://www.ebrueggeman.com/phpgraphlib" target="_blank"><img src="http://www.ebrueggeman.com/phpgraphlib/images/phpgraphlib_80x15_green.png" alt="PHPGraphLib - Click For Official Site" width="80" height="15" align="top" style="margin-right:10px; border:0" /></a></td>
			</tr>
		</table>			
		</td>
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/Stats 3.png" onClick="javascript:document.location='generategraph.php?pid=<?php echo $pid ?>&id=wickets'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="generategraph.php?pid=<?php echo $pid ?>&id=wickets">Career Wickets</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:10px;">
				Click <a href="generategraph.php?pid=<?php echo $pid ?>&id=wickets">here</a> to generate a graph of the wickets a player has taken over his/her career. 
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-left:42px; padding-bottom:15px; "><a href="http://www.ebrueggeman.com/phpgraphlib" target="_blank"><img src="http://www.ebrueggeman.com/phpgraphlib/images/phpgraphlib_80x15_green.png" alt="PHPGraphLib - Click For Official Site" width="80" height="15" align="top" style="margin-right:10px; border:0" /></a></td>
			</tr>
		</table>
		</td>
	</tr>
	<?php
	if(isset($_REQUEST["p"])){
	//user has come from profile page, so need back button to return to the profile.
	?>
	<tr>
		<td colspan="2" align="center" style="padding-top:15px; "><input type="button" value="Back" onClick="javascript:document.location='playerprofile.php?id=<?php echo $pid ?>';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
	</tr>
	<?php } ?>
</table>
<?php
}

function select_players($teamid, $currentid){ 
$selectplayer = dbToArray("call getPlayersForStats(".$teamid.",".$currentid.",'stats')"); 
?>
<table cellpadding="0" cellspacing="0" border="0" id="whitetext">
	<tr>
		<td style="padding-bottom:20px; font-size:14px; " ><strong>Click on the names below to view other players.</strong>
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
			echo "<a href='playerstatshome.php?pid=".$selectplayer[$i]['userid']."' style='cursor:pointer;'>".stripslashes($selectplayer[$i]['player'])."</a>";
			echo "<br><br>";
		}
		?>
		</div>
		</td>
</table>
<?php
}
?>