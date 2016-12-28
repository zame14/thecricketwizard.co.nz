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
<?php
/********************
Validation
********************/
$user = getUserDetails($_SESSION['userid']);
$colour = "#E8795E";
$text = "Statistics";
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px; ">
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
								<td align="left" valign="middle" style="font-size:24px; padding-left:20px; padding-top:10px; padding-bottom:10px;" id="whitetext" colspan="3"><strong><?php echo $user->teamname; ?></strong></td>
							</tr>
							<?php 
							$mp = dbToArray("call getMatchesPlayed('where m.teamid=".$user->teamid."','','')");
							if(count($mp)>0){ ?>					
							<tr>
								<td align="left" style="font-size:18px; padding-left:20px; padding-bottom:20px; padding-right:10px;" id="whitetext"><?php 
								echo "Welcome to the statistics home page for the ".$user->teamname." cricket team.  Below are the different options available for you to view.<br><br>
								To view your individual statistics, click <a href='playerstatshome.php?pid=".$user->userid."'>here</a>"; ?>
								</td>
							</tr>
							<tr>
								<td width="100%" valign="top" style="padding-left:15px; padding-right:15px; padding-top:10px;">
								<?php statsMenu(); ?>
								</td>
							</tr>
							<?php 
							}
							else { ?>
							<tr>
								<td style="font-size:18px; padding-left:15px; padding-bottom:20px;" id="whitetext"><em>No statistics available to view.</em></td>
							</tr>
							<?php } ?>
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
<?php
function statsMenu(){
?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px;"><img src="images/View.png" onClick="javascript:document.location='viewstats.php?id=team'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px; "><strong><a class="aWhite" href="viewstats.php?id=team">Team Statistics</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:32px; ">
				Click <a href="viewstats.php?id=team">here</a> to view your team's winning percentage, top run scorer, leading wicket taker, most capped player, plus much more...
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
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/View.png" onClick="javascript:document.location='viewstats.php?id=batting'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="viewstats.php?id=batting">Batting & Fielding Statistics</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:10px;">
				Click <a href="viewstats.php?id=batting">here</a> to view your team's batting and fielding stats.  See who has scored the most runs, has the best batting average, taken the most catches, plus much more...
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
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/View.png" onClick="javascript:document.location='viewstats.php?id=bowling'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="viewstats.php?id=bowling">Bowling Statistics</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:10px; ">
				Click <a href="viewstats.php?id=bowling">here</a> to view your team's bowling stats.  See who has taken the most wickets, has the best bowling average, the best RPO, plus much more...
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="padding-bottom:10px;"><div class="line"> &nbsp;</div></td>
			</tr>
		</table>		
		</td>
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/View.png" onClick="javascript:document.location='partnerships.php'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="partnerships.php">Partnership Records</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:54px; ">
				Click <a href="partnerships.php">here</a> to view your team's highest batting partnerships.
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
				<td rowspan="2" valign="top" style="padding-right:10px;"><img src="images/Security.png" onClick="javascript:document.location='honours.php'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="honours.php">Honours Board</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:10px; ">
				Click <a href="honours.php">here</a> to view your team's honours board.  A player must score a hundred or more runs, or take six or more wickets.
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
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/View.png" onClick="javascript:document.location='century_partnerships.php'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="century_partnerships.php">Century Partnerships</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:32px; ">
				Click <a href="century_partnerships.php">here</a> to view all your team's hundred run batting partnerships.
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
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/Star.png" onClick="javascript:document.location='achievements.php?id=games'" style="cursor:pointer;"></td>
				<td width="100%" style="font-size:20px; padding-bottom:5px;"><strong><a href="achievements.php?id=games">Top Achievements</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:5px; padding-bottom:20px; ">
				Click <a href="achievements.php?id=games">here</a> to view the top achievements achieved by the players in your team.  These achievements include fifty or more games, a thousand or more runs, a hundred or more wickets plus much more...
				</td>
			</tr>
		</table>	
		</td>	
		<td width="50%" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" id="whitetext">
			<tr>
				<td rowspan="2" valign="top" style="padding-right:10px; "><img src="images/Thumb Up.png" onClick="javascript:document.location='milestones.php'" style="cursor:pointer;"></td>
				<td width="90%" style="font-size:20px; padding-bottom:5px;"><strong><a href="milestones.php">Trophy Cabinet</a></strong></td>
			</tr>
			<tr>
				<td style="font-size:18px; padding-right:10px; padding-bottom:31px; ">
				Click <a href="milestones.php">here</a> to view your team's trophy cabinet of the competitions your team has won.
				</td>
			</tr>
		</table>	
		</td>
	</tr>
</table>
<?php
}
?>