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
$user = getUserDetails($_SESSION['userid']);
$colour = "#E8795E";
$text = "Statistics";
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
								<td align="left" valign="top" style="font-size:24px; padding-left:20px; padding-top:10px; padding-bottom:10px;" id="whitetext" colspan="3"><strong><?php echo "Partnership Records"; ?></strong></td>
							</tr>
							<tr>
								<td align="left" valign="top" style="font-size:18px; padding-left:20px; padding-bottom:10px; padding-right:10px;" id="whitetext" colspan="3">
								<?php 
								echo "The highest batting partnerships by wicket, for the ".$user->teamname." cricket team.<br><br>"; 
								echo "<div id='goldtext'>View scorecard</div>Click on each partnership to view the match scorecard.";
								?>
								</td>
							</tr>
							<tr>
								<td style="padding-left:20px; padding-right:20px; padding-bottom:10px; padding-top:10px;"><?php Partnerships($user->teamid); ?></td>
							</tr>
							<tr>
								<td colspan="3" align="center" style="padding-bottom:20px; padding-top:5px; "><input type="button" value="Back" onClick="javascript:document.location='statshome.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
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
function Partnerships($teamid){ 
$wicket = array(1 => "1st", 2 => "2nd", 3 => "3rd", 4 => "4th", 5 => "5th", 6 => "6th", 7 => "7th", 8 => "8th", 9 => "9th", 10 => "10th");
?>
<table cellpadding="5" cellspacing="0" width="100%" border="1" id="stats">
	<tr>
		<td width="60">Wicket</td>
		<td width="50">Runs</td>
		<td>Partners</td>
		<td>Opposition</td>
		<td>Venue</td>
		<td>Season</td>
	</tr>
	<?php
	for($i=1;$i<=10;$i++){ 
	$p = dbToArray("call getPartnerships('WHERE m.teamid=".$teamid." AND pa.wicket=".$i." AND partnership<>10101', 'ORDER BY pa.wicket,pa.partnership desc, m.date desc');");
	//Get the dismissal id of each batsman to check wheather partnership was unbroken.
	if($p[1]["wicket"]>0){
		$did1 = dbToArray("call getMatchPerformances('WHERE p.playerid=".$p[1]['p1id']." AND p.matchid=".$p[1]['matchid']." AND p.matchinnings=".$p[1]['inningsid']."','');"); 
		$did2 = dbToArray("call getMatchPerformances('WHERE p.playerid=".$p[1]['p2id']." AND p.matchid=".$p[1]['matchid']." AND p.matchinnings=".$p[1]['inningsid']."','');"); 
	}
	//if partnership was unbroken, display runs*
	//did 7 = not out
	($did1[1]['did']==7 && $did2[1]['did']==7) ? $runs = $p[1]["partnership"]."*" : $runs = $p[1]["partnership"];
	?>
	<tr>
		<td><?php echo $wicket[$i]; ?></td>
		<td><?php echo ($p[1]["wicket"]>0) ? "<a href='viewmatch.php?id=".$p[1]["matchid"]."&p=3'>".$runs."</a>" : "-"; ?></td>
		<td><?php echo ($p[1]["wicket"]>0) ? "<a href='viewmatch.php?id=".$p[1]["matchid"]."&p=3'>".$p[1]["player1"].", ".$p[1]["player2"]."</a>" : "-"; ?></td>
		<td><?php echo ($p[1]["wicket"]>0) ? "<a href='viewmatch.php?id=".$p[1]["matchid"]."&p=3'>vs ".$p[1]["opponent"]."</a>" : "-"; ?></td>
		<td><?php echo ($p[1]["wicket"]>0) ? "<a href='viewmatch.php?id=".$p[1]["matchid"]."&p=3'>".$p[1]["venue"]."</a>" : "-"; ?></td>
		<td><?php echo ($p[1]["wicket"]>0) ? "<a href='viewmatch.php?id=".$p[1]["matchid"]."&p=3'>".$p[1]["season"]."</a>" : "-"; ?></td>
	</tr>
	<?php
	}
	?>
</table>
<?php
}
?>