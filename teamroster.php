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
<script type="text/javascript" src="toolstips/wz_tooltip.js"></script>
<?php
/********************
Validation
********************/
$user = getUserDetails($_SESSION['userid']);
$colour = "#85C226";
$text = "Team Roster";

//Processing
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="retire"){
		$retire = dbToArray("call updateRetiredStatus(".$_REQUEST['id'].",".$_REQUEST['retirestatus'].");");
		redirect_rel('process.php?id=1&np=teamroster.php');
	}
	else if($_REQUEST['action']=="remove"){
		$remove = dbToArray("call removePlayer(".$_REQUEST['id'].");");
		redirect_rel('process.php?id=1&np=teamroster.php');
	}
	else if($_REQUEST['action']=="hide"){
		$update = dbToArray("call updateShowHide(".$user->teamid.",1);");
		redirect_rel('process.php?id=1&np=teamroster.php');
	}
	else if($_REQUEST['action']=="show"){
		$update = dbToArray("call updateShowHide(".$user->teamid.",0);");
		redirect_rel('process.php?id=1&np=teamroster.php');
	}
}
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px; ">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" border="0" height="800" id="main" style="table-layout:fixed; ">
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
						<td align="right" style="padding-right:50px; padding-top:5px;"><?php echo $text; ?></td>
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
								<td align="left" valign="middle" style="font-size:24px; padding-left:15px; padding-top:10px;" id="whitetext" colspan="3">
								<?php
								if($user->role=="Team Admin"){
									if($user->private==0) {
										//display hide button
										//echo "<strong>".$user->teamname."</strong><span style='background-image:url(images/hide.gif); margin-left:10px; background-repeat:no-repeat; width:20px; background-position:center; cursor:pointer;' onClick='javascript:ShowHide(\"hide\");'></span>";
										echo "<strong>".$user->teamname."&nbsp;&nbsp;<img src='images/hide.gif' onClick='javascript:ShowHide(\"hide\");' style='cursor:pointer;'>"; 
									}
									else {
										//display show button
										echo "<strong>".$user->teamname."&nbsp;&nbsp;<img src='images/show.gif' onClick='javascript:ShowHide(\"show\");' style='cursor:pointer;'>"; 
									}
								}
								else {
									echo "<strong>".$user->teamname."</strong>";
								}								
								?>
								</td>
							</tr>
							<?php
							if($user->role=="Team Admin"){ ?>
							<tr>
								<td align="right" valign="middle" colspan="2" style="padding-right:20px; "><input type="button" value="Add new player(s)" onClick="javascript:document.location='addplayers.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
							</tr>
							<?php } ?>
							<tr>
								<td style="padding-left:15px; padding-right:15px; padding-top:10px;" valign="top"><?php teamRoster($user->teamid, $_SESSION['userid'], $user->role); ?></td>
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
<?php
function teamRoster($teamid, $userid, $role){
$sql = "call getTeamRoster('where t.teamid=".$teamid." AND deleted=0','order by retired, firstname, lastname','')";
$result = dbToArray($sql);	
$count = count($result);
$rows = $count;
if(!isset($_GET['pagenum'])){
	$pagenum = 1;
}
else {
	$pagenum = $_GET['pagenum'];
}

$page_rows = 11;
$last = ceil($rows/$page_rows);
if ($pagenum < 1) { 
	$pagenum = 1; 
} 
elseif ($pagenum > $last) { 
	$pagenum = $last; 
} 
$max = 'limit ' .($pagenum - 1) * $page_rows .',' .$page_rows;
if($count<>0){
	$sql1 = "call getTeamRoster('where t.teamid=".$teamid." AND deleted=0','order by retired, firstname, lastname','".$max."')";
	$result1 = dbToArray($sql1);	
	$count1 = count($result1);
}
?>
<form name="teamroster" action="teamroster.php" method="post">
<input type="hidden" name="action">
<input type="hidden" name="id">
<input type="hidden" name="retirestatus">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td id="whitetext" style="font-size:18px; padding-bottom:20px;">
		Welcome to your team roster.  Below is a list of players available for your team.<br><br>
		
		<?php
		if($role=="Team Admin"){ ?>
			<div id="goldtext">Add new player(s)</div>
			To add new player(s) to your team roster, click the Add new player(s) button.<br><br>
			<div id="goldtext">View player profiles</div>
			To view a players profile, click on the players name.<br><br>
			<div id="goldtext">Retire players</div>
			To retire a player click the <img src="images/Forbidden_small.png"> button.  A retired player means you cannot select this player for a match, but their
			previous performances remain in the system.  To bring a player out of retirement, click the <img src="images/refresh_small.png"> button.<br><br>
			<div id="goldtext">Delete players</div>
			To permanently remove a player from the system, click the <img src="images/Cancel_small.png"> button.  This will remove all the players previous performances.<br><br>
			<div id="goldtext">Show/Hide team in Guest Mode &nbsp;&nbsp;<img src="images/Question.png" onMouseOver="Tip('Guest Mode is where anyone can enter the site and view batting, bowling and fielding statistics of the teams registered.', BGCOLOR,'#ffffff', BORDERCOLOR,'#dddddd', DELAY,300, STICKY,false, CLOSEBTN,false, CLICKCLOSE,true, FOLLOWMOUSE, false, PADDING,16, SHADOW,true, SHADOWCOLOR,'#cccccc', SHADOWWIDTH,2, WIDTH,200, FIX, [this,-125,5]);" onMouseOut="UnTip();"></div>
			To make your team private and not accessible in Guest Mode, click the <img src="images/hide.gif"> button.  To make your team public again, click the <img src="images/show.gif"> button.<br><br>			
			<div id="goldtext">Change Team Administrator</div>
			To select a new person to be the Team Administrator for your team, click the <img src="images/Warning_small.png"> button.
		<?php 
		}
		else { ?>
		 	<div id="goldtext">View player profiles</div>
			To view a players profile, click on the players name.<br><br>
			<div id="goldtext">Updating your profile</div>
			To update your profile, click on your name below and click the Edit button.
		<?php
		}
		$sqladmin = "call getTeamRoster('where u.roleid=1 AND t.teamid=".$teamid."','','')";
		$resultadmin = dbToArray($sqladmin);			
		echo "<br><br><b>Your Team Administrator &ndash; ".$resultadmin[1]['firstname']." ".$resultadmin[1]['lastname']."</b>";
		?> 		
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="teamroster">
			<tr>
				<td colspan="4" align="right">
				<?php
				if ($pagenum == 1){
					echo "&nbsp;";
				} 
				else {
				echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
				echo " ";
				$previous = $pagenum-1;
				echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a> ";
				}		
				if ($pagenum == $last) {
				} 
				else {
				$next = $pagenum+1;
				echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
				echo " ";
				echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
				} 		
				?>
				</td>
			</tr>
			<tr>
				<td>Name</td>
				<td>Caps</td>
				
				<?php
				if($role=="Team Admin"){
				?>			
				<td>Playing role</td>
				<td style=" padding-left:10px; font-size:14px;">&nbsp;</td>
				<?php
				} 
				else { ?>
				<td colspan="2">Playing role</td><?php
				}?>
			</tr>
			<?php
			//$count = count($result);
			for($i=1; $i<=$count1; $i++){ 
			$sql2 = "call getStatsPlayer(".$result1[$i]['userid'].")";
			$result2 = dbToArray($sql2);				
			?>
			<input type="hidden" id="<?php echo $result1[$i]['userid']; ?>" value="<?php echo $result1[$i]['firstname'].' '.stripslashes($result1[$i]['lastname']); ?>" >
			<tr>
				<td><a href="playerprofile.php?id=<?php echo $result1[$i]['userid'] ?>" style="cursor:pointer;";><?php echo $result1[$i]['firstname'].' '.stripslashes($result1[$i]['lastname']); ?></a></td>
				<td><?php echo $result2[1]['matches']; ?> </td>
				<td><?php
				if($result1[$i]['retired']==0){
					$sql3 = "call getPlayerProfile(".$result1[$i]['userid'].")";
					$result3 = dbToArray($sql3);				
					if($result3[1]['playingrole']<>""){
						echo $result3[1]['playingrole']; 
					}
					else {
						echo "<i>Need to update profile</i>";
					}
				}
				else {
					echo "<i>Retired</i>";
				}
				?></td>				
				<?php
				if($role=="Team Admin"){
				?>		
				<td align="left" style=" padding-left:10px;">
				<?php
				if($resultadmin[1]['userid']<>$result1[$i]['userid']){
					if($result1[$i]['retired']==0){?>
						<img src="images/Forbidden.png" alt="Retire" style="cursor:pointer;" onClick="RetirePlayer(<?php echo $result1[$i]['userid'];?>,1);">
					<?php }
					else { ?>
						<img src="images/Refresh.png" alt="Bring out of retirement" style="cursor:pointer" onClick="RetirePlayer(<?php echo $result1[$i]['userid'];?>,0);">
					<?php } ?>
				&nbsp;&nbsp;<img src="images/Cancel.png" alt="Remove" style="cursor:pointer;"onClick="RemovePlayer(<?php echo $result1[$i]['userid'];?>);">
				<?php }
				else { ?>
					<img src="images/Warning.png" alt="Change" style="cursor:pointer;"onClick="javascript:document.location='changeadmin.php'">
				<?php 
				} ?>
				</td>
				<?php } ?>			
			</tr>
			<?php
			} 
			if($count<>0){?>
			<tr>
				<td colspan="5" align="right"><?php echo "Page ".$pagenum." of ".$last; ?></td>
			</tr>	
		<?php } ?>
		</table>
		</td>
	</tr>
</table>
</form>
<script language="javascript">
function RetirePlayer(theid,thestatus){
	frm = document.teamroster;
	var player = document.getElementById(theid).value;
	if(thestatus==1){
		ans = confirm("Are you sure you want to retire " + player + "?");
	}
	else {
		ans = confirm("Are you sure you want to bring " + player + " out of retirement?");
	}
	if (ans == true ) {
		frm.action.value = "retire";
		frm.id.value = theid;
		frm.retirestatus.value = thestatus;
		frm.submit();
	}		
}
function RemovePlayer(theid){
	frm = document.teamroster;
	var player = document.getElementById(theid).value;
	ans = confirm("Are you sure you want to permanently remove " + player + " from the system?");
	if (ans == true ) {
		frm.action.value = "remove";
		frm.id.value = theid;
		frm.submit();
	}	
}
function ShowHide(v){
	frm = document.teamroster;
	frm.action.value = v;
	frm.submit();
}
</script>
<?php
}
?>