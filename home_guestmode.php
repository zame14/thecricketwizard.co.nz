<?php
/********************
Includes
********************/
include_once("inc/db_functions.php");
include_once("inc/form_lib.php");
include_once("inc/cricket_lib.php");
//session_start();
//validate_usersession();
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
<?php
/********************
Validation
********************/
//$user = getUserDetails($_SESSION['userid']);
$colour = "#E8795E";
$text = "Guest Mode";
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
				<td bgcolor="#404040" style="padding-left:10px; padding-top:5px;"><a href="index.php" style="cursor:pointer; "><img src="images/headingloggedout.jpg" border="0"></a></td>
				<td align="right" bgcolor="#404040" style="color:#FFFFFF; padding-right:10px; padding-top:5px; " width="50%">
				<?php register_guest(); ?>
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
								<td valign="top" style="padding-left:20px; padding-right:20px; padding-top:20px; ">
								<?php welcome(); ?>
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
								<td align="center"><?php guest_menu(); ?></td>
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
				<td colspan="2" align="center" style="padding-bottom:10px; padding-top:20px; "><?php footer(1); ?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
<?php
function welcome() {
?>
<table cellpadding="0" cellspacing="0" width="100%" border="0" id="whitetext">
	<tr>
		<td align="left" style="padding-bottom:15px; "><?php ad("468x60",1,1); ?></td>
	</tr>
	<tr>
		<td style="font-size:18px; " colspan="2">
		Welcome to The Cricket Wizard.  <br><br>You are currently logged in as a guest.  Guest mode allows you
		to view batting, bowling, and fielding statistics of the teams currently registered.
		<br><br>If you would like to register your team and gain full access to The Cricket Wizard, click on the Register Now button.
		<br><br>If your team has been registered but you do not have a login, please contact your Team Administrator.
		<br><br><div id="goldtext">Viewing Statistics</div>
		Select a team from the menu below to view batting, bowling, and fielding statistics.<br><br>
		</td>
	</tr>
	<tr>
		<td><?php select_team(); ?></td>
		<td valign="top" style="padding-left:5px;"><?php ad("250x250",1,1); ?></td>
	</tr>
	<tr>
		<td align="center" style="padding-top:10px; padding-bottom:20px;" colspan="2"><input type="button" value="&nbsp;&nbsp;Exit&nbsp;&nbsp;" onClick="javascript:document.location='index.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
	</tr>
</table>
<?php
}
function select_team(){ 
$teams = dbToArray("call getTeamMenu('0')"); 
?>
<table cellpadding="0" cellspacing="0" border="0" id="whitetext" width="500">
	<tr>
		<td style="font-size:17px; padding-left:20px; ">
		<div class="flexcroll" align="left" style="height:400px; ">
		<?php
		for($i=1;$i<=count($teams);$i++){	
			if($teams[$i]['province']==""){
				$teamname = $teams[$i]['teamname']." &ndash; ".$teams[$i]['grade'];
			}
			else {
				$teamname = $teams[$i]['teamname']." (".$teams[$i]['province'].")"." &ndash; ".$teams[$i]['grade'];
			}
			echo "<a href='viewstats_guestmode.php?id=team&tid=".$teams[$i]['teamid']."' style='cursor:pointer;'>".stripslashes($teamname)."</a>";
			echo "<br><br>";
		}
		?>
		</div>
		</td>
</table>
<?php
}
?>