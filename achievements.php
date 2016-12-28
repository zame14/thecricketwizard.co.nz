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
								<td align="left" valign="top" style="font-size:24px; padding-left:20px; padding-top:10px; padding-bottom:0px;" id="whitetext" colspan="3"><strong><?php echo "Top Achievements"; ?></strong></td>
							</tr>
							<tr>
								<td align="left" valign="top" style="font-size:18px; padding-left:20px; padding-bottom:10px; padding-right:10px;" id="whitetext" colspan="3">
								Welcome to the Top Achievements page.<br>Select from the menu on the right to view the different achievements.
								</td>
							</tr>
							<tr>
								<td style="padding-left:20px;" valign="top">
								<?php
								$options = array();
								$options[1]['achievement'] = "matches";
								$options[2]['achievement'] = "runs";
								$options[3]['achievement'] = "wickets";
								$options[4]['achievement'] = "allrounder";
								$options[5]['achievement'] = "dismissals";
								
								if(isset($_REQUEST['id'])){
									$default = $_REQUEST['id'];
								}
								else {
									$default="";
								}
								?>
								<table cellpadding="0" cellspacing="0" border="0" width="100%">
									<tr>
										<td width="50%" valign="top" style="padding-right:20px;"><?php achievements($default, $user->teamid,$user->teamname); ?></td>
										<td width="10%" height="100%"><div class="line2">&nbsp;</div></td>							
										<td width="30%" valign="top"><?php menu(); ?></td>
									</tr>
								</table>					
								</td>
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
<script language="javascript">
function View() {
	frm = document.thefrm;
	frm.submit();
}
</script>
<?php
function achievements($id, $teamid, $teamname){
	if($id=="games"){
		$sql = "call getStatsTeam('where t.teamid=".$teamid."','group by p.playerid', 'order by matches desc','');";
		$result = dbToArray($sql);
		$text1 = "Players who have played 50 games for ".$teamname;
		$text2 = "No players have played 50 games.";
		$count=count($result);
		$p = array();
		$c = 0;
		for($i=1; $i<=$count; $i++){
			if($result[$i]['matches']>=50){
				if($result[$i]['matches']>=100){
					$p[$i] = "<strong>".$result[$i]['player']."&nbsp;&nbsp;&ndash;&nbsp;&nbsp;".$result[$i]['matches']."</strong>";
				}
				else {
					$p[$i] = $result[$i]['player']."&nbsp;&nbsp;&ndash;&nbsp;&nbsp;".$result[$i]['matches'];
				}
				$c++;
			}
		}
	}
	else if($id=="runs"){
		$sql = "call getStatsTeam('where t.teamid=".$teamid."','group by p.playerid','order by sum(bat.runs) desc','');";
		$result = dbToArray($sql);
		$text1 = "Players who have scored 1000 for ".$teamname;
		$text2 = "No players have scored 1000 runs.";
		$count=count($result);
		$p = array();
		$c = 0;
		for($i=1; $i<=$count; $i++){
			if($result[$i]['runs']>=1000){
				$p[$i] = $result[$i]['player']."&nbsp;&nbsp;&ndash;&nbsp;&nbsp;".$result[$i]['runs'];
				$c++;
			}
		}
	}
	else if($id=="wickets"){
		$sql = "call getStatsTeam('where t.teamid=".$teamid."','group by p.playerid','order by sum(bowl.wickets) desc','');";
		$result = dbToArray($sql);
		$text1 = "Players who have taken 100 wickets for ".$teamname;
		$text2 = "No players have taken 100 wickets.";
		$count=count($result);
		$p = array();
		$c = 0;
		for($i=1; $i<=$count; $i++){
			if($result[$i]['wickets']>=100){
				$p[$i] = $result[$i]['player']."&nbsp;&nbsp;&ndash;&nbsp;&nbsp;".$result[$i]['wickets'];
				$c++;
			}
		}
	}
	else if($id=="runs_wickets"){
		$sql = "call getStatsTeam('where t.teamid=".$teamid."','group by p.playerid','order by player','');";
		$result = dbToArray($sql);
		$text1 = "Players who have scored 1000 runs and taken 100 wickets for ".$teamname;
		$text2 = "No players have score a 1000 runs and taken 100 wickets.";
		$count=count($result);
		$p = array();
		$c = 0;
		for($i=1; $i<=$count; $i++){
			if(($result[$i]['runs']>=1000 && $result[$i]['wickets']>=100)){
				$c++;
				$p[$c] = $result[$i]['player']."&nbsp;&nbsp;&ndash;&nbsp;&nbsp;".$result[$i]['runs']." runs ".$result[$i]['wickets']." wickets";	
			}
		}
	}
	else if($id=="dismissals"){
		$sql = "call getStatsTeam('where t.teamid=".$teamid."','group by p.playerid','order by dismissals desc','');";
		$result = dbToArray($sql);
		$text1 = "Players who have scored 1000 runs and taken 50 dismissals for ".$teamname;
		$text2 = "No players have scored 1000 runs and taken 50 dismissals.";
		$count=count($result);
		$p = array();
		$c = 0;
		for($i=1; $i<=$count; $i++){
			if(($result[$i]['runs']>=1000 && $result[$i]['dismissals']>=50)){
				$p[$i] = $result[$i]['player']."&nbsp;&nbsp;&ndash;&nbsp;&nbsp;".$result[$i]['dismissals'];
				$c++;
			}
		}
	}
	else {
		redirect_rel('statshome.php');
	}
	if($id){		
	?>
	<table cellpadding="5" cellspacing="0" border="0" id="standard" width="100%">
		<tr>
			<td style="font-size:18px; padding-bottom:20px;"><strong><?php echo $text1; ?></strong></td>
		</tr>
		<?php
		if($c>0){
			for($i=1; $i<=$c; $i++){ ?>		
			<tr>
				<td>
				<?php echo $p[$i]; ?>
				</td>
			</tr>
			<?php } 
		}
		else { ?>
			<tr>
				<td><?php echo $text2; ?></td>
			</tr>	
			<?php		
		}?>
	</table>
	<?php
	}
	else {
	echo "error";
	}
}

function menu(){
?>
<table cellpadding="5" cellspacing="0" border="0" id="whitetext">
	<tr>
		<td colspan="2"><strong>Achievements Menu</strong></td>
	</tr>
	<tr>
		<td><img src="images/Star.png"></td>
		<td><a href="achievements.php?id=games">50 games</a></td>
	</tr>
	<tr>
		<td><img src="images/Star.png"></td>
		<td><a href="achievements.php?id=runs">1000 runs</a></td>
	</tr>
	<tr>
		<td><img src="images/Star.png"></td>
		<td><a href="achievements.php?id=wickets">100 wickets</a></td>
	</tr>
	<tr>
		<td><img src="images/Star.png"></td>
		<td><a href="achievements.php?id=runs_wickets">1000 runs &amp; 100 wickets</a></td>
	</tr>
	<tr>
		<td><img src="images/Star.png"></td>
		<td><a href="achievements.php?id=dismissals">1000 runs &amp; 50 dismissals</a></td>
	</tr>
</table>
<?php
}
?>