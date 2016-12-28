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
//print_r($_REQUEST);
$user = getUserDetails($_SESSION['userid']);
$colour = "#F7C200";
$text = "Fixtures";
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
			<tr>
				<td colspan="2" valign="top" style="padding-top:20px; padding-left:20px; padding-right:20px;"><?php scorecard($_REQUEST['id'], $user); ?></td>
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
//Functions
function scorecard($mid, $user){ 
//get data
$match = dbToArray("call getMatch(".$mid.");");
?>
<table cellpadding="10" cellspacing="0" border="0" width="100%" id="fixtures">
	<tr>
		<td align="center" style="font-size:20px;">
		<?php 
		echo $match[1]['competition']." &ndash; ".$match[1]['grade']."<br />".$match[1]['date']."<br /><br />".$match[1]['thematch']." at ".$match[1]['venue']."<br /><br />Match result: ".$match[1]['result']; 
		?>
		</td>
	</tr>
	<tr>
		<td align="center"><div class="line"> &nbsp;</div></td>
	</tr>
	<tr>
		<td align="center" style="padding-left:60px; padding-right:60px; "><?php scorecard_details($match); ?></td>
	</tr>
	<?php
	$goto = "javascript:document.location='matches.php'";
	if(isset($_REQUEST["p"])&&$_REQUEST["p"]==1) {
		$goto = "javascript:document.location='playerprofile.php?id=".$_REQUEST["pid"]."'";
	}
	else if(isset($_REQUEST["p"])&&$_REQUEST["p"]==2){
		$goto = "javascript:document.location='viewplayermatches.php?id=".$_REQUEST["pid"]."'";
	}
	else if(isset($_REQUEST["p"])&&$_REQUEST["p"]==3){
		$goto = "javascript:document.location='partnerships.php'";
	}
	else if(isset($_REQUEST["p"])&&$_REQUEST["p"]==4){
		$goto = "javascript:document.location='century_partnerships.php'";
	}
	?>
	<tr>
		<td align="center" style="padding-top:40px; padding-bottom:10px;"><input type="button" value="Back" onClick="<?php echo $goto; ?>" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"><?php if($user->role=="Team Admin") { ?>&nbsp;|&nbsp;<a href="editmatch.php?id=<?php echo $match[1]['matchid'] ?>">Edit</a> <?php } ?></td>
	</tr>
</table>
<?php
}

function scorecard_details($match){ 
$show_bowling=0; // need to check if there was any bowling.
//get data
//first innings data
//batting & fielding Data
$p1 = dbToArray("call getMatchPerformances('where p.matchid =".$match[1]['matchid']." and p.matchinnings=1', 'order by isbatnull, bat.batorder, player.firstname, player.lastname');");
//bowling data
$b1 = dbToArray("call getMatchPerformances('where p.matchid =".$match[1]['matchid']." and p.matchinnings=1', 'order by isbowlnull, bowl.bowlorder, player.firstname, player.lastname');");

//check if there was any bowling

for($o=1; $o <= sizeof($b1); $o++){
	if($b1[$o]['deliveries']<>'') {
		$show_bowling=1;
		break;
	}
}


//partnerships data
$pship = dbToArray("call getPartnerships('WHERE m.matchid=".$match[1]['matchid']." AND inningsid=1','ORDER BY wicket');");

$wicket = array(1 => "1st", 2 => "2nd", 3 => "3rd", 4 => "4th", 5 => "5th", 6 => "6th", 7 => "7th", 8 => "8th", 9 => "9th", 10 => "10th");
?>
<table cellpadding="5" cellspacing="0" border="0" width="100%" id="scorecard">
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" border="0" width="100%">
			<?php
			if($match[1]['mtid']==2){
				//get second innings data
				$p2 = dbToArray("call getMatchPerformances('where p.matchid =".$match[1]['matchid']." and p.matchinnings=2', 'order by isbatnull, bat.batorder, player.firstname, player.lastname');");
				
				$b2 = dbToArray("call getMatchPerformances('where p.matchid =".$match[1]['matchid']." and p.matchinnings=2', 'order by isbowlnull, bowl.bowlorder, player.firstname, player.lastname');");
				
				$pship2 = dbToArray("call getPartnerships('WHERE m.matchid=".$match[1]['matchid']." AND inningsid=2','ORDER BY wicket');");				
				//check if there was any bowling
				
				$show_bowling_2=0;				
				for($n=1; $n <= sizeof($b2); $n++){
					if($b2[$n]['deliveries']<>''){
						$show_bowling_2=1;
						break;
					}
				}						
				//display innings heading
				?>
				<tr>
					<td colspan="15" bgcolor="#E4E4E4"><strong>FIRST INNINGS</strong></td>
				</tr>
			<?php
			}
			?>
			<tr>
				<td><strong>Batting</strong></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td title="Balls faced"><strong>BF</strong></td>
				<td title="Strike rate"><strong>SR</strong></td>
				<td title="fours"><strong>4</strong></td>
				<td title="sixes"><strong>6</strong></td>
				<td width="20">&nbsp;</td>
				<td title="Catches"><strong>C</strong></td>
				<td title="Stumpings"><strong>S</strong></td>
				<td title="Byes"><strong>B</strong></td>
				<td rowspan="<?php echo (sizeof($p1)+1); ?>" height="100%"><div class="line2"> &nbsp;</div></td>
				<td colspan="3"><strong>Batting partnerships</strong></td>
			</tr>
			<?php
			for($i=1; $i <= sizeof($p1); $i++){
				// just want to get first letter of players first name.
				$firstname = $p1[$i]['firstname'];
				?>
				<tr>
					<td width="150" style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $firstname[0]." ".stripslashes($p1[$i]['lastname']); ?></td>
					<td width="120" style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo (isset($p1[$i]['dismissal'])) ? $p1[$i]['dismissal'] : "dnb"; ?></td>
					<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo (isset($p1[$i]['runs'])) ? $p1[$i]['runs'] : "&nbsp;"; ?></td>
					<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($p1[$i]['ballsfaced']<>"-") ? $p1[$i]['ballsfaced'] : "&nbsp;"; ?></td>
					<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($p1[$i]['ballsfaced']<>"-") ? round(($p1[$i]['runs']/$p1[$i]['ballsfaced'])*100,2) : "&nbsp;"; ?></td>
					<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($p1[$i]['boundaries']<>"-") ? $p1[$i]['boundaries'] : "&nbsp;"; ?></td>
					<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($p1[$i]['sixes']<>"-") ? $p1[$i]['sixes'] : "&nbsp;"; ?></td>
					<td width="20" style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; ">&nbsp;</td>
					<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo (isset($p1[$i]['catches']) && $p1[$i]['catches']<>0) ? $p1[$i]['catches'] : "&nbsp;"; ?></td>
					<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($p1[$i]['stumpings']>0) ? $p1[$i]['stumpings'] : "&nbsp;"; ?></td>
					<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($p1[$i]['byes']>0) ? $p1[$i]['byes'] : "&nbsp;"; ?></td>
					<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($i <= 10) ? $wicket[$i] :""; ?></td>
					<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; ">
					<?php
					//check to see if partnerships have been recorded
					if($i <= 10){
						if(count($pship)>0){
							if($pship[$i]['partnership']<>10101){
								//check to see if partnership was unbroken.
								$player1 = dbToArray("call getMatchPerformances('WHERE p.playerid=".$pship[$i]['p1id']." AND p.matchid=".$pship[1]['matchid']." AND p.matchinnings=1','');"); 
								$player2 = dbToArray("call getMatchPerformances('WHERE p.playerid=".$pship[$i]['p2id']." AND p.matchid=".$pship[1]['matchid']." AND p.matchinnings=1','');"); 
								echo ($player1[1]['did']==7 && $player2[1]['did']==7) ? $pship[$i]["partnership"]."*" : $pship[$i]["partnership"];
							}
							else {
								echo "-";
							}
						}
						else {
							echo "-";
						}
					}
					?>
					</td>
					<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; ">
					<?php
					//check to see if partnerships have been recorded
					if($i <= 10){
						if(count($pship)>0){
							echo ($pship[$i]['partnership']<>10101) ? stripslashes($pship[$i]["player1"]).", ".stripslashes($pship[$i]["player2"]) : "&nbsp;";
						}
						else {
							echo "-";
						}
					}
					?>
					</td>					
				</tr>
			<?php
			}
			?>
			<tr>
				<td colspan="15" align="center"><div class="line"> &nbsp;</div></td>
			</tr>
			<?php
			if($show_bowling==1){ ?>
			<tr>
				<td colspan="15">
				<table cellpadding="5" cellspacing="0" border="0">
					<tr>
						<td><strong>Bowling</strong></td>
						<td title="Overs"><strong>O</strong></td>
						<td title="Maidens"><strong>M</strong></td>
						<td title="Runs conceded"><strong>R</strong></td>
						<td title="Wickets"><strong>W</strong></td>
						<td width="20">&nbsp;</td>
						<td title="Wides"><strong>Wd</strong></td>
						<td title="No balls"><strong>Nb</strong></td>
						<td width="20">&nbsp;</td>
						<td title="Bowling average"><strong>Ave</strong></td>
						<td title="Strike rate"><strong>SR</strong></td>
						<td title="Runs per over"><strong>RPO</strong></td>
					</tr>
					<?php
					for($i=1; $i <= sizeof($p1); $i++){
						if(isset($b1[$i]['deliveries'])) {
							//only want to return those that bowled
							$firstname = $b1[$i]['firstname'];
							?>
							<tr>
								<td width="150" style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $firstname[0]." ".stripslashes($b1[$i]['lastname']); ?></td>
								<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $b1[$i]['overs']; ?></td>
								<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $b1[$i]['maidens']; ?></td>
								<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $b1[$i]['runsconceded']; ?></td>
								<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $b1[$i]['wickets']; ?></td>
								<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; ">&nbsp;</td>
								<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $b1[$i]['wides']; ?></td>
								<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $b1[$i]['noballs']; ?></td>
								<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; ">&nbsp;</td>
								<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($b1[$i]['wickets']>0) ? round($b1[$i]['runsconceded']/$b1[$i]['wickets'],1) : "-"; ?></td>
								<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($b1[$i]['wickets']>0) ? round($b1[$i]['deliveries']/$b1[$i]['wickets'],1) : "-"; ?></td>
								<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo round($b1[$i]['runsconceded']/($b1[$i]['deliveries']/6),1); ?></td>
							</tr>
							<?php
						}
					}
					?>
				</table>
				</td>
			</tr>
			<?php
			}
			else { ?>
			<tr>
				<td colspan="15"><?php echo ($match[1]['mtid']==1) ? "No bowling performances were completed." : "No first innings bowling performances were completed."; ?></td>
			</tr>
			<?php
			}
			if($match[1]['mtid']==2){
				//display innings heading
				?>
				<tr>
					<td colspan="15" align="center"><div class="line"> &nbsp;</div></td>
				</tr>				
				<tr>
					<td bgcolor="#E4E4E4" colspan="15"><strong>SECOND INNINGS</strong></td>
				</tr>
				<tr>
					<td><strong>Batting</strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td title="Balls faced"><strong>BF</strong></td>
					<td title="Strike rate"><strong>SR</strong></td>
					<td title="fours"><strong>4</strong></td>
					<td title="sixes"><strong>6</strong></td>
					<td width="20">&nbsp;</td>
					<td title="Catches"><strong>C</strong></td>
					<td title="Stumpings"><strong>S</strong></td>
					<td title="Byes"><strong>B</strong></td>
					<td rowspan="<?php echo (sizeof($p2)+1); ?>" height="100%"><div class="line2"> &nbsp;</div></td>
					<td colspan="3"><strong>Batting partnerships</strong></td>
				</tr>
				<?php
				for($i=1; $i <= sizeof($p2); $i++){
					// just want to get first letter of players first name.
					$firstname = $p2[$i]['firstname'];
					?>
					<tr>
						<td width="150" style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $firstname[0]." ".stripslashes($p2[$i]['lastname']); ?></td>
						<td width="120" style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo (isset($p2[$i]['dismissal'])) ? $p2[$i]['dismissal'] : "dnb"; ?></td>
						<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo (isset($p2[$i]['runs'])) ? $p2[$i]['runs'] : "&nbsp;"; ?></td>
						<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($p2[$i]['ballsfaced']<>"-") ? $p2[$i]['ballsfaced'] : "&nbsp;"; ?></td>
						<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($p2[$i]['ballsfaced']<>"-") ? round(($p2[$i]['runs']/$p2[$i]['ballsfaced'])*100,2) : "&nbsp;"; ?></td>
						<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($p2[$i]['boundaries']<>"-") ? $p2[$i]['boundaries'] : "&nbsp;"; ?></td>
						<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($p2[$i]['sixes']<>"-") ? $p2[$i]['sixes'] : "&nbsp;"; ?></td>
						<td width="20" style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; ">&nbsp;</td>
						<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo (isset($p2[$i]['catches']) && $p2[$i]['catches']<>0) ? $p2[$i]['catches'] : "&nbsp;"; ?></td>
						<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($p2[$i]['stumpings']>0) ? $p2[$i]['stumpings'] : "&nbsp;"; ?></td>
						<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($p2[$i]['byes']>0) ? $p2[$i]['byes'] : "&nbsp;"; ?></td>
						<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($i <= 10) ? $wicket[$i] :""; ?></td>
						<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; ">
						<?php
						//check to see if partnerships have been recorded
						if($i <= 10){
							if(count($pship2)>0){
								if($pship2[$i]['partnership']<>10101){
									//check to see if partnership was unbroken.
									$player1 = dbToArray("call getMatchPerformances('WHERE p.playerid=".$pship2[$i]['p1id']." AND p.matchid=".$pship2[1]['matchid']." AND p.matchinnings=2','');"); 
									$player2 = dbToArray("call getMatchPerformances('WHERE p.playerid=".$pship2[$i]['p2id']." AND p.matchid=".$pship2[1]['matchid']." AND p.matchinnings=2','');"); 
									echo ($player1[1]['did']==7 && $player2[1]['did']==7) ? $pship2[$i]["partnership"]."*" : $pship2[$i]["partnership"];
								}
								else {
									echo "-";
								}
							}
							else {
								echo "-";
							}
						}
						?>
						</td>
						<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; ">
						<?php
						//check to see if partnerships have been recorded
						if($i <= 10) {
							if(count($pship2)>0){
								echo ($pship2[$i]['partnership']<>10101) ? stripslashes($pship2[$i]["player1"]).", ".stripslashes($pship2[$i]["player2"]) : "&nbsp;";
							}
							else {
								echo "-";
							}
						}
						?>
						</td>					
					</tr>
				<?php
				}
				?>
				<tr>
					<td colspan="15" align="center"><div class="line"> &nbsp;</div></td>
				</tr>
				<?php
				if($show_bowling_2==true){ ?>
				<tr>
					<td colspan="15">
					<table cellpadding="5" cellspacing="0" border="0">
						<tr>
							<td>Bowling</td>
							<td title="Overs"><strong>O</strong></td>
							<td title="Maidens"><strong>M</strong></td>
							<td title="Runs conceded"><strong>R</strong></td>
							<td title="Wickets"><strong>W</strong></td>
							<td width="20">&nbsp;</td>
							<td title="Wides"><strong>Wd</strong></td>
							<td title="No balls"><strong>Nb</strong></td>
							<td width="20">&nbsp;</td>
							<td title="Bowling average"><strong>Ave</strong></td>
							<td title="Strike rate"><strong>SR</strong></td>
							<td title="Runs per over"><strong>RPO</strong></td>
						</tr>
						<?php
						for($i=1; $i <= sizeof($b2); $i++){
							if(isset($b2[$i]['deliveries'])) {
								//only want to return those that bowled
								$firstname = $b2[$i]['firstname'];
								?>
								<tr>
									<td width="150" style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $firstname[0]." ".stripslashes($b2[$i]['lastname']); ?></td>
									<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $b2[$i]['overs']; ?></td>
									<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $b2[$i]['maidens']; ?></td>
									<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $b2[$i]['runsconceded']; ?></td>
									<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $b2[$i]['wickets']; ?></td>
									<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; ">&nbsp;</td>
									<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $b2[$i]['wides']; ?></td>
									<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo $b2[$i]['noballs']; ?></td>
									<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; ">&nbsp;</td>
									<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($b2[$i]['wickets']>0) ? round($b2[$i]['runsconceded']/$b2[$i]['wickets'],1) : "-"; ?></td>
									<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo ($b2[$i]['wickets']>0) ? round($b2[$i]['deliveries']/$b2[$i]['wickets'],1) : "-"; ?></td>
									<td style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color:#DCE9D6; "><?php echo round($b2[$i]['runsconceded']/($b2[$i]['deliveries']/6),1); ?></td>
								</tr>
								<?php
							}
						}
						?>
					</table>
					</td>
				</tr>
				<?php
				}
				else { ?>
				<tr>
					<td colspan="15">No second innings bowling performances were completed.</td>
				</tr>
				<?php
				}
			}
			if($match[1]['summary']<> "") { ?>
			<tr>
				<td colspan="15" align="center"><div class="line"> &nbsp;</div></td>
			</tr>
			<tr>
				<td colspan="15"><strong>Match summary</strong></td>
			</tr>			
			<tr>
				<td colspan="15"><?php echo $match[1]['summary']; ?></td>
			</tr>
			<?php
			}
			?>
		</table>
		</td>
	</tr>
</table>
<?php
}
?>