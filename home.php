<?php
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
<style>
.aWhite {
	color:#FFFFFF;
	text-decoration:none;
}
.aWhite:hover {
	text-decoration:underline;
}
</style>
<body>
<?php
$user = getUserDetails($_SESSION['userid']);
//call unset session incase user didnot log out properly last time.
unsetSessions();
//print_r($_SESSION);
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px; ">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" border="0" style="background-image:url(images/homepage.jpg); background-repeat:no-repeat;" height="800" id="main">
			<tr>
				<td style="padding-left:10px; padding-top:5px;">&nbsp;</td>
				<td align="right" style="color:#FFFFFF; padding-right:10px; padding-top:5px; " width="50%">
				<?php loggedIn($user->firstname, $user->lastname, $user->teamname, $user->role); ?>
				</td>
			</tr>
			<tr height="40%">
				<td style="padding-top:100px;" id="indextext2" valign="top" align="center">Your Online Cricket Management <br>System</td>
				<td align="right" style="padding-right:50px; padding-top:90px; "><?php quicklinks($user->role,$_SESSION['userid']); ?></td>
			</tr>
			<tr height="60%">
				<td colspan="2" style="padding-left:20px; padding-right:20px; padding-top:10px;" valign="top">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#212121" height="100%" id="home">
					<tr>
						<td colspan="5" style="padding-left:20px; padding-top:10px; padding-bottom:20px; font-size:24px;"><?php echo "Hi ".$user->firstname;?></td>
					</tr>
					<tr>				
						<td style="padding-top:10px;" valign="top">
						<table cellpadding="5" cellspacing="0" border="0" width="100%" id="whitetext" height="100%">
							<tr>
								<td align="center" style="font-size:24px;" colspan="2"><strong><a class="aWhite" href="teamroster.php">Team Roster</a></strong></td>
							</tr>
							<?php
							if($user->role=="Team Admin"){ ?>					
							<tr>
								<td align="right"><img src="images/b1.gif"></td>
								<td style="font-size:18px;">Add players to team roster</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b1.gif"></td>
								<td style="font-size:18px;">View player profiles</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b1.gif"></td>
								<td style="font-size:18px;">Edit my profile</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b1.gif"></td>
								<td style="font-size:18px;">Reassign Team Administrator role</td>
							</tr>
							<?php						
							} 
							else { ?>
							<tr>
								<td align="right"><img src="images/b1.gif"></td>
								<td style="font-size:18px;">View my profile</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b1.gif"></td>
								<td style="font-size:18px;">Edit my profile</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b1.gif"></td>
								<td style="font-size:18px;">Enter my goals</td>
							</tr>	
							<tr>
								<td align="right"><img src="images/b1.gif"></td>
								<td style="font-size:18px;">View my team mates profiles</td>
							</tr>						
							<?php }?>
							<tr>
								<td colspan="2" align="center" style="padding-bottom:30px; padding-top:50px;"><input type="button" value="&nbsp;Enter&nbsp;" onClick="javascript:document.location='teamroster.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
							</tr>
						</table>			
						</td>
						<td height="100%" style="padding-top:10px; padding-bottom:10px;">
						<div class="line2">&nbsp;</div>
						</td>
						<td style="padding-top:10px;" valign="top">
						<table cellpadding="5" cellspacing="0" width="100%" border="0" id="whitetext" height="100%">
							<tr>
								<td align="center" style="font-size:24px;" colspan="2"><strong><a class="aWhite" href="matches.php">Fixtures</a></strong></td>
							</tr>
							<?php
							if($user->role=="Team Admin"){ ?>					
							<tr>
								<td align="right"><img src="images/b2.gif"></td>
								<td style="font-size:18px;">Enter match details</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b2.gif"></td>
								<td style="font-size:18px;">Enter player performances</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b2.gif"></td>
								<td style="font-size:18px;">View matches played</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b2.gif"></td>
								<td style="font-size:18px;">Edit/delete matches played</td>
							</tr>
							<?php						
							} 
							else { ?>
							<tr>
								<td align="right"><img src="images/b2.gif"></td>
								<td style="font-size:18px;">View matches played</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b2.gif"></td>
								<td style="font-size:18px;">View match scorecards</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b2.gif"></td>
								<td style="font-size:18px;">View match summary</td>
							</tr>				
							<?php } ?>
							<tr>
								<td colspan="2" align="center"style="padding-bottom:30px; padding-top:50px;"><input type="button" value="&nbsp;Enter&nbsp;" onClick="javascript:document.location='matches.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
							</tr>
						</table>			
						</td>
						<td height="100%" style="padding-top:10px; padding-bottom:10px;">
						<div class="line2">&nbsp;</div>
						</td>
						<td style="padding-top:10px;" valign="top">
						<table cellpadding="5" cellspacing="0" width="100%" border="0" id="whitetext" height="100%">
							<tr>
								<td align="center" style="font-size:24px;" colspan="2"><strong><a class="aWhite" href="statshome.php">Statistics</a></strong></td>
							</tr>			
							<tr>
								<td align="right"><img src="images/b3.gif"></td>
								<td style="font-size:18px;">View batting statistics</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b3.gif"></td>
								<td style="font-size:18px;">View bowling & fielding statistics</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b3.gif"></td>
								<td style="font-size:18px;">View players individual statistics</td>
							</tr>
							<tr>
								<td align="right"><img src="images/b3.gif"></td>
								<td style="font-size:18px;">View honours board</td>
							</tr>
							<tr>
								<td colspan="2" align="center" style="padding-bottom:30px; padding-top:50px;"><input type="button" value="&nbsp;Enter&nbsp;" onClick="javascript:document.location='statshome.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
							</tr>
						</table>					
						</td>
					</tr>
					<tr>
						<td style="padding-bottom:10px; padding-left:20px; "><a href="http://www.facebook.com/TheCricketWizard" target="_blank"><img src="images/Fbook.jpg" border="0"></a></td>
					</tr>
					<?php
					$last = dbToArray("call getMatchesPlayed('where t.teamid=".$user->teamid."','order by UNIX_TIMESTAMP(date) desc','limit 1');");
					if(count($last)>0){	?>			
					<tr>
						<td colspan="5" style="padding-left:20px; padding-top:20px; padding-bottom:10px;"><?php echo "<strong>Last updated:</strong> ". $last[1]['teamname']." vs ".$last[1]['game']." at ".$last[1]['venue'].", ".$last[1]['date']."&nbsp;&nbsp;&nbsp;<a class='a2' href='viewmatch.php?id=".$last[1]['matchid']."'>scorecard</a>"; ?></td>
					</tr>
				<?php } ?>			
				</table>
				</td>
			</tr>
			<tr>			
				<td colspan="3" align="center" style="padding-top:20px; "><?php ad("468x60",1); ?></td>
			</tr>
			<tr>
				<td colspan="3" align="center" style="padding-bottom:10px; padding-top:20px; "><?php footer(); ?></td>
			</tr>
		</table>
		</tr>
	</td>
</table>

<?php
function quicklinks($role, $id){ 
$matches = dbToArray("call getStatsPlayer(".$id.");");
?>
<table cellpadding="5" cellspacing="0" id="whitetext" border="0">
	<tr>
		<td colspan="2"><strong>Quick Links</strong></td>
	</tr>
	<tr>
		<td><img src="images/Man.png" onClick="javascript:document.location='playerprofile.php?pid=<?php echo $id; ?>'" style="cursor:pointer"></td>
		<td width="80%"><a class="aWhite" href="playerprofile.php?id=<?php echo $id; ?>">View my profile</a></td>
	</tr>
	<tr>
		<td><img src="images/Write3.png" onClick="javascript:document.location='editplayerprofile.php?id=<?php echo $id; ?>'" style="cursor:pointer"></td>
		<td><a class="aWhite" href="editplayerprofile.php?id=<?php echo $id; ?>">Edit my profile</a></td>
	</tr>
	<?php
	if($role=="Team Admin"){ ?>
	<tr>
		<td><img src="images/Plus.png" onClick="javascript:document.location='addplayers.php'" style="cursor:pointer"></td>
		<td><a class="aWhite" href="addplayers.php">Add new player(s)</a></td>
	</tr>
	<tr>
		<td><img src="images/Plus.png" onClick="javascript:document.location='matchselectcomp.php'" style="cursor:pointer"></td>
		<td><a class="aWhite" href="matchselectcomp.php">Enter new match</a></td>
	</tr>
	<?php
		if($matches>0){ ?>
		<tr>
			<td><img src="images/View.png" onClick="javascript:document.location='playerstatshome.php?pid=<?php echo $id; ?>'" style="cursor:pointer"></td>
			<td><a class="aWhite" href="playerstatshome.php?pid=<?php echo $id; ?>">View my stats</a></td>
		</tr>
		<?php
		}
		else {
		?>
		<tr>
			<td><img src="images/View.png" onClick="javascript:document.location='viewstats.php?id=team'" style="cursor:pointer"></td>
			<td><a class="aWhite" href="viewstats.php?id=team">View team stats</a></td>
		</tr>	
	<?php }
	} 
	else { ?>
	<tr>
		<td><img src="images/View.png" onClick="javascript:document.location='playerstatshome.php?pid=<?php echo $id; ?>'" style="cursor:pointer"></td>
		<td><a class="aWhite" href="playerstatshome.php?pid=<?php echo $id; ?>">View my stats</a></td>
	</tr>
	<tr>
		<td><img src="images/View.png" onClick="javascript:document.location='viewstats.php?id=batting'" style="cursor:pointer"></td>
		<td><a class="aWhite" href="viewstats.php?id=batting">View batting stats</a></td>
	</tr>	
	<tr>
		<td><img src="images/View.png" onClick="javascript:document.location='viewstats.php?id=bowling'" style="cursor:pointer"></td>
		<td><a class="aWhite" href="viewstats.php?id=bowling">View bowling stats</a></td>
	</tr>	
	<?php } ?>
</table>
<?php
}
?>
</body>
</html>
