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
								<td align="left" valign="top" style="font-size:24px; padding-left:20px; padding-top:10px; padding-bottom:10px;" id="whitetext" colspan="3"><strong><?php echo "Century Partnerships"; ?></strong></td>
							</tr>
							<tr>
								<td align="left" valign="top" style="font-size:18px; padding-left:20px; padding-bottom:10px; padding-right:10px;" id="whitetext" colspan="3">
								<?php 
								echo "A list of hundred run partnerships.<br /><br />"; 
								echo "<div id='goldtext'>View scorecard</div>Click on each partnership to view the match scorecard.";
								?>
								</td>
							</tr>
							<tr height="80%">
								<td style="padding-left:20px; padding-right:20px; padding-bottom:10px; padding-top:10px;" valign="top"><?php Partnerships($user->teamid); ?></td>
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
	$p = dbToArray("call getPartnerships('WHERE m.teamid=".$teamid." AND partnership>=100 AND partnership<>10101', 'ORDER BY m.date desc');");
	if(count($p)>0)
	{
		for($i=1;$i<=count($p);$i++){ 
		
		//Get the dismissal id of each batsman to check wheather partnership was unbroken.
		if($p[$i]["wicket"]>0){
			$did1 = dbToArray("call getMatchPerformances('WHERE p.playerid=".$p[$i]['p1id']." AND p.matchid=".$p[$i]['matchid']." AND p.matchinnings=".$p[$i]['inningsid']."','');"); 
			$did2 = dbToArray("call getMatchPerformances('WHERE p.playerid=".$p[$i]['p2id']." AND p.matchid=".$p[$i]['matchid']." AND p.matchinnings=".$p[$i]['inningsid']."','');"); 
		}
		//if partnership was unbroken, display runs*
		//did 7 = not out
		($did1[1]['did']==7 && $did2[1]['did']==7) ? $runs = $p[$i]["partnership"]."*" : $runs = $p[$i]["partnership"];
		?>
		<tr>
			<td><?php echo $p[$i]['wicket']; ?></td>
			<td><?php echo "<a href='viewmatch.php?id=".$p[$i]["matchid"]."&p=4'>".$runs."</a>"; ?></td>
			<td><?php echo "<a href='viewmatch.php?id=".$p[$i]["matchid"]."&p=4'>".stripslashes($p[$i]["player1"]).", ".stripslashes($p[$i]["player2"])."</a>"; ?></td>
			<td><?php echo "<a href='viewmatch.php?id=".$p[$i]["matchid"]."&p=4'>vs ".$p[$i]["opponent"]."</a>"; ?></td>
			<td><?php echo "<a href='viewmatch.php?id=".$p[$i]["matchid"]."&p=4'>".$p[$i]["venue"]."</a>"; ?></td>
			<td><?php echo "<a href='viewmatch.php?id=".$p[$i]["matchid"]."&p=4'>".$p[$i]["season"]."</a>"; ?></td>
		</tr>
		<?php
		}
	}
	else
	{
		?>
		<tr>
			<td colspan="6"><em>No century partnerships to view.</em></td>
		</tr>
		<?php
	}
	?>
</table>
<?php
}
?>