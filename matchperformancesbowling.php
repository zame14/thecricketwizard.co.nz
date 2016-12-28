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
<script type="text/javascript" src="inc/jquery-1.3.2.min.js">
</script>
<script type="text/javascript" src="inc/jquery.tablednd_0_5.js">
</script>
<script type="text/javascript">
$(document).ready(function() {

	// Initialise the first table (as before)
	$("#table-1").tableDnD();
	// Make a nice striped effect on the table
	
	$("#fixtures tr:even').addClass('alt')");

	// Initialise the second table specifying a dragClass and an onDrop function that will display an alert
	$("#fixtures").tableDnD({
	    onDragClass: "myDragClass",
	    onDrop: function(table, row) {
			
            var rows = table.tBodies[0].rows;
            var debugStr = "Row dropped was "+row.id+". New order: ";
			var theorder = "";
            for (var i=0; i<rows.length; i++) {
                debugStr += rows[i].id+" ";		
				theorder += rows[i].id+" ";		
            }
			document.getElementById('bowlingorder').value = theorder; 
	    },

		onDragStart: function(table, row) {
		}
	}); 
	
	
});
</script>
<body>
<?php
/********************
Validation
********************/
$user = getUserDetails($_SESSION['userid']);
$colour = "#F7C200";
$text = "Fixtures";
if($user->role<>"Team Admin"){
	redirect_rel('home.php');
}
$sql = "call getSelectedPlayers(".$user->teamid.",1)";
$result = dbToArray($sql);

if(isset($_SESSION['m']['cont_6'])){
	if($_SESSION['m']['cont_6'] <> 1){
		set_variables($result);
	}
}
else {
	set_variables($result);
}
if(isset($_REQUEST['cont_6'])){
	set_variables($result);
}
$error=0;
$errormsg = '';
//print_r($_SESSION);
/*****************************
Processing
*****************************/
if(isset($_REQUEST['bowlingaction'])){
	if($_REQUEST['bowlingaction']=="next"){
		$errorobj = check_form_fields($result,$_SESSION['m']['mt']);
		$error = $errorobj->general;
		$errormsg = $errorobj->message;
		if($error==0){
			redirect_rel('process.php?id=2&np=matchperformancesfielding.php');
		}
	}
	else if($_REQUEST['bowlingaction']=="exit"){
		unsetSessions();
		redirect_rel('matches.php');
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
								<td valign="top" style="padding-left:20px; padding-right:20px;" colspan="2">
								<?php 
								if($_SESSION['m']['mt']==1){
									performances1($user->teamid, $result, $error, $errormsg,1); 
								}
								else if($_SESSION['m']['mt']==2){
									performances2($user->teamid, $result, $error, $errormsg,2); 
								}
								?>
								</td>
							</tr>
							<tr>
								<td style="padding-left:20px; padding-top:40px; padding-bottom:20px; ">
								<table cellpadding="0" cellspacing="0" id="whitetext">
									<tr>
										<td><input type="button" value="Next >>" onClick="Next();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold; height:35px;"></td>
										<td>&nbsp;|&nbsp;<a href="matchpartnerships.php">Back</a></td>
										<td>&nbsp;|&nbsp;<a href="#" onClick="Exit();">Exit</a></td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						</td>
						<td bgcolor="<?php echo $colour; ?>" width="30" height="10%" style="table-layout:fixed ">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="padding-top:10px;" width="175">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="center" style="padding-left:5px; padding-right:5px; "><?php steps(6); ?></td>
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
function performances1($teamid, $result, $error, $errormsg, $mtid){
$count = count($result);
?>
<form name="bowling" action="matchperformancesbowling.php" method="post">
<input type="hidden" name="bowlingaction">
<input type="hidden" name="cont_6">
<input type="hidden" name="bowlingorder" id="bowlingorder" value="<?php echo (isset($_SESSION['m']['bowlingorder'])) ? $_SESSION['m']['bowlingorder'] : "" ?>">
<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td style="font-size:24px; padding-top:10px;" id="whitetext"><strong><?php echo "Enter bowling performances"; ?></strong></td>
	</tr>
	<tr>
		<td style="padding-top:10px; padding-bottom:10px; font-size:18px;" id="whitetext">
		To put team into bowling order, click and drag the players name to the position you want the player in.
		</td>
	</tr>
	<?php
	if($error==1){?>
	<tr>
		<td style="padding-bottom:20px; "><?php validation($errormsg); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" width="100%" border="1" id="fixtures">	
			<tr class="nodrop nodrag">
				<td>&nbsp;</td>
				<td>Overs</td>
				<td>Maidens</td>
				<td>Runs conceded</td>
				<td>Wickets</td>
				<td>Wides</td>
				<td>No balls</td>
			</tr>
			<tr>
			</tr>			
			<?php
			for($i=1; $i<=$count; $i++){ 
			?>				
			<tr id="<?php echo $i; ?>">
				<td><?php echo stripslashes($result[$i]['player']); ?></td>
				<td>
				<?php if(!isset($_SESSION['m']['overs'.$i])){ $_SESSION['m']['overs'.$i]=""; }
				print_textBox("overs".$i, $_SESSION['m']['overs'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['maidens'.$i])){ $_SESSION['m']['maidens'.$i]=""; }
				print_textBox("maidens".$i, $_SESSION['m']['maidens'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['runsconceded'.$i])){ $_SESSION['m']['runsconceded'.$i]=""; }
				print_textBox("runsconceded".$i, $_SESSION['m']['runsconceded'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php
				$wickets = array();
				$wickets[1]['wickets']=0;$wickets[2]['wickets']=1;$wickets[3]['wickets']=2;$wickets[4]['wickets']=3;
				$wickets[5]['wickets']=4;$wickets[6]['wickets']=5;$wickets[7]['wickets']=6;$wickets[8]['wickets']=7;
				$wickets[9]['wickets']=8;$wickets[10]['wickets']=9;$wickets[11]['wickets']=10;
				if(!isset($_SESSION['m']['wickets'.$i])){ $_SESSION['m']['wickets'.$i]=0; }
				print_dropDown("wickets".$i, 0, $wickets, $_SESSION['m']['wickets'.$i]);
				?>
				</td>
				<td>
				<?php if(!isset($_SESSION['m']['wides'.$i])){ $_SESSION['m']['wides'.$i]=""; }
				print_textBox("wides".$i, $_SESSION['m']['wides'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['noballs'.$i])){ $_SESSION['m']['noballs'.$i]=""; }
				print_textBox("noballs".$i, $_SESSION['m']['noballs'.$i], "size='4' maxlength='4'"); 
				?></td>		
			</tr>
			<?php } ?>
		</table>			
		</td>
	</tr>
</table>
</form>
<?php
}
function performances2($teamid, $result, $error, $errormsg, $mtid){
$count = count($result);
if(isset($_REQUEST['bowlingaction'])){
	if($_REQUEST['bowlingaction']=="next"){
		$errorobj = check_form_fields($result,$mtid);
		$error = $errorobj->general;
		$errormsg = $errorobj->message;
		if($error==0){
			redirect_rel('process.php?id=2&np=matchperformancesfielding.php');
		}
	}
	else if($_REQUEST['bowlingaction']=="exit"){
		redirect_rel('home.php');
		unsetSessions();
	}
}
?>
<form name="bowling" action="matchperformancesbowling.php" method="post">
<input type="hidden" name="bowlingaction">
<input type="hidden" name="cont_6">
<input type="hidden" name="bowlingorder" id="bowlingorder" value="<?php echo (isset($_SESSION['m']['bowlingorder'])) ? $_SESSION['m']['bowlingorder'] : "" ?>">
<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td style="font-size:24px; padding-top:10px;" id="whitetext"><strong><?php echo "Enter bowling performances"; ?></strong></td>
	</tr>
	<tr>
		<td style="padding-top:10px; padding-bottom:10px; font-size:18px;" id="whitetext">
		To put team into bowling order, click and drag the players name to the position you want the player in.
		</td>
	</tr>
	<?php
	if($error==1){?>
	<tr>
		<td style="padding-bottom:20px; "><?php validation($errormsg); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" width="100%" border="1" id="fixtures">	
			<tr class="nodrop nodrag">
				<td colspan="7"><strong>First Innings</strong></td>
			</tr>		
			<tr class="nodrop nodrag">
				<td>&nbsp;</td>
				<td>Overs</td>
				<td>Maidens</td>
				<td>Runs conceded</td>
				<td>Wickets</td>
				<td>Wides</td>
				<td>No balls</td>
			</tr>
			<?php
			//$dismissal = dbToArray("call getDismissal()");		
			for($i=1; $i<=$count; $i++){ 
			//$name = "playerid".$i;
			?>				
			<tr id="<?php echo $i; ?>">
				<td><?php echo stripslashes($result[$i]['player']); ?></td>
				<td>
				<?php if(!isset($_SESSION['m']['overs'.$i])){ $_SESSION['m']['overs'.$i]=""; }
				print_textBox("overs".$i, $_SESSION['m']['overs'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['maidens'.$i])){ $_SESSION['m']['maidens'.$i]=""; }
				print_textBox("maidens".$i, $_SESSION['m']['maidens'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['runsconceded'.$i])){ $_SESSION['m']['runsconceded'.$i]=""; }
				print_textBox("runsconceded".$i, $_SESSION['m']['runsconceded'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php
				$wickets = array();
				$wickets[1]['wickets']=0;$wickets[2]['wickets']=1;$wickets[3]['wickets']=2;$wickets[4]['wickets']=3;
				$wickets[5]['wickets']=4;$wickets[6]['wickets']=5;$wickets[7]['wickets']=6;$wickets[8]['wickets']=7;
				$wickets[9]['wickets']=8;$wickets[10]['wickets']=9;$wickets[11]['wickets']=10;
				if(!isset($_SESSION['m']['wickets'.$i])){ $_SESSION['m']['wickets'.$i]=0; }
				print_dropDown("wickets".$i, 0, $wickets, $_SESSION['m']['wickets'.$i]);
				?>
				</td>
				<td>
				<?php if(!isset($_SESSION['m']['wides'.$i])){ $_SESSION['m']['wides'.$i]=""; }
				print_textBox("wides".$i, $_SESSION['m']['wides'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['noballs'.$i])){ $_SESSION['m']['noballs'.$i]=""; }
				print_textBox("noballs".$i, $_SESSION['m']['noballs'.$i], "size='4' maxlength='4'"); 
				?></td>		
			</tr>
			<?php } ?>
			<tr class="nodrop nodrag">
				<td colspan="7"><strong>Second Innings</strong></td>
			</tr>
			<tr class="nodrop nodrag">
				<td>&nbsp;</td>
				<td>Overs</td>
				<td>Maidens</td>
				<td>Runs conceded</td>
				<td>Wickets</td>
				<td>Wides</td>
				<td>No balls</td>
			</tr>
			<?php
			for($i=1; $i<=$count; $i++){ ?>
			<tr id="<?php echo $i; ?>">
				<td><?php echo stripslashes($result[$i]['player']); ?></td>
				<td>
				<?php if(!isset($_SESSION['m']['overs2'.$i])){ $_SESSION['m']['overs2'.$i]=""; }
				print_textBox("overs2".$i, $_SESSION['m']['overs2'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['maidens2'.$i])){ $_SESSION['m']['maidens2'.$i]=""; }
				print_textBox("maidens2".$i, $_SESSION['m']['maidens2'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['runsconceded2'.$i])){ $_SESSION['m']['runsconceded2'.$i]=""; }
				print_textBox("runsconceded2".$i, $_SESSION['m']['runsconceded2'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php
				$wickets2 = array();
				$wickets2[1]['wickets2']=0;$wickets2[2]['wickets2']=1;$wickets2[3]['wickets2']=2;$wickets2[4]['wickets2']=3;
				$wickets2[5]['wickets2']=4;$wickets2[6]['wickets2']=5;$wickets2[7]['wickets2']=6;$wickets2[8]['wickets2']=7;
				$wickets2[9]['wickets2']=8;$wickets2[10]['wickets2']=9;$wickets2[11]['wickets2']=10;
				if(!isset($_SESSION['m']['wickets2'.$i])){ $_SESSION['m']['wickets2'.$i]=0; }
				print_dropDown("wickets2".$i, 0,$wickets2,$_SESSION['m']['wickets2'.$i]);
				?>
				</td>
				<td>
				<?php if(!isset($_SESSION['m']['wides2'.$i])){ $_SESSION['m']['wides2'.$i]=""; }
				print_textBox("wides2".$i, $_SESSION['m']['wides2'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['noballs2'.$i])){ $_SESSION['m']['noballs2'.$i]=""; }
				print_textBox("noballs2".$i, $_SESSION['m']['noballs2'.$i], "size='4' maxlength='4'"); 
				?></td>	
			</tr>
			<?php } ?>			
		</table>			
		</td>
	</tr>
</table>
</form>
<?php
}
function set_variables($result) {
	$count = count($result);
	for($i=1; $i<=$count; $i++){
	$_REQUEST['overs'.$i] 		= set_param('overs'.$i);
	$_SESSION['m']['overs'.$i] 		= set_session_param('overs'.$i);
	$_SESSION['m']['overs'.$i] 		= $_REQUEST['overs'.$i];
	$_REQUEST['maidens'.$i] 		= set_param('maidens'.$i);
	$_SESSION['m']['maidens'.$i] 		= set_session_param('maidens'.$i);
	$_SESSION['m']['maidens'.$i] 		= $_REQUEST['maidens'.$i];
	$_REQUEST['wickets'.$i] 		= set_param('wickets'.$i);
	$_SESSION['m']['wickets'.$i] 		= set_session_param('wickets'.$i);
	$_SESSION['m']['wickets'.$i] 		= $_REQUEST['wickets'.$i];
	$_REQUEST['runsconceded'.$i] 		= set_param('runsconceded'.$i);
	$_SESSION['m']['runsconceded'.$i] 		= set_session_param('runsconceded'.$i);
	$_SESSION['m']['runsconceded'.$i] 		= $_REQUEST['runsconceded'.$i];
	$_REQUEST['wides'.$i] 		= set_param('wides'.$i);
	$_SESSION['m']['wides'.$i] 		= set_session_param('wides'.$i);
	$_SESSION['m']['wides'.$i] 		= $_REQUEST['wides'.$i];
	$_REQUEST['noballs'.$i] 		= set_param('noballs'.$i);
	$_SESSION['m']['noballs'.$i] 		= set_session_param('noballs'.$i);
	$_SESSION['m']['noballs'.$i] 		= $_REQUEST['noballs'.$i];
	$_REQUEST['overs2'.$i] 		= set_param('overs2'.$i);
	$_SESSION['m']['overs2'.$i] 		= set_session_param('overs2'.$i);
	$_SESSION['m']['overs2'.$i] 		= $_REQUEST['overs2'.$i];
	$_REQUEST['maidens2'.$i] 		= set_param('maidens2'.$i);
	$_SESSION['m']['maidens2'.$i] 		= set_session_param('maidens2'.$i);
	$_SESSION['m']['maidens2'.$i] 		= $_REQUEST['maidens2'.$i];
	$_REQUEST['wickets2'.$i] 		= set_param('wickets2'.$i);
	$_SESSION['m']['wickets2'.$i] 		= set_session_param('wickets2'.$i);
	$_SESSION['m']['wickets2'.$i] 		= $_REQUEST['wickets2'.$i];
	$_REQUEST['runsconceded2'.$i] 		= set_param('runsconceded2'.$i);
	$_SESSION['m']['runsconceded2'.$i] 		= set_session_param('runsconceded2'.$i);
	$_SESSION['m']['runsconceded2'.$i] 		= $_REQUEST['runsconceded2'.$i];
	$_REQUEST['wides2'.$i] 		= set_param('wides2'.$i);
	$_SESSION['m']['wides2'.$i] 		= set_session_param('wides2'.$i);
	$_SESSION['m']['wides2'.$i] 		= $_REQUEST['wides2'.$i];
	$_REQUEST['noballs2'.$i] 		= set_param('noballs2'.$i);
	$_SESSION['m']['noballs2'.$i] 		= set_session_param('noballs2'.$i);
	$_SESSION['m']['noballs2'.$i] 		= $_REQUEST['noballs2'.$i];
	}
	$_REQUEST['bowlingorder'] 		= set_param('bowlingorder');
	$_SESSION['m']['bowlingorder'] 		= set_session_param('bowlingorder');
	$_SESSION['m']['bowlingorder'] 		= $_REQUEST['bowlingorder'];	
	$_REQUEST['cont_6'] 		= set_param('cont_6');
	$_SESSION['m']['cont_6'] 		= set_session_param('cont_6');
	$_SESSION['m']['cont_6'] 		= $_REQUEST['cont_6'];
}

function check_form_fields($result,$mtid){
	$count = count($result);
	$error->general = 0;
	$error->message = '';
	for($i=1; $i<=$count; $i++){
		//user has entered overs
		if($_REQUEST['overs'.$i]<>""){
			//check that overs entered are valid
			if(is_numeric($_REQUEST['overs'.$i])){
				//check if maidens entered and are valid
				if($_REQUEST['maidens'.$i]<>""){
					if(is_numeric($_REQUEST['maidens'.$i])){
						//cannot bowl more maidens than overs
						if($_REQUEST['maidens'.$i] > $_REQUEST['overs'.$i]){
							$error->general = 1; 
							$error->message = $result[$i]['player']." cannot bowl more maidens than overs, please check.";
							return $error;							
						}
					}
					else {
						$error->general = 1; 
						$error->message = "The maidens for ".$result[$i]['player']." are not valid, please check.";
						return $error;									
					}
				}
				//check if runs conceded have been entered, and are valid.
				if($_REQUEST['runsconceded'.$i]<>""){
					if(is_numeric($_REQUEST['runsconceded'.$i])){
						//if overs = maidens, then runs conceded must be 0.
						if($_REQUEST['overs'.$i] == $_REQUEST['maidens'.$i] && $_REQUEST['runsconceded'.$i] <> 0){
							$error->general = 1; 
							$error->message = $result[$i]['player']." has bowled ".$_REQUEST['overs'.$i]." over(s) and bowled ".$_REQUEST['maidens'.$i]." maidens, which means runs conceded must be 0.  Please check";
							return $error;				
						}
						//if runs conceded = 0, the maidens bowled must = the number of overs bowled.
						if($_REQUEST['runsconceded'.$i]==0 && $_REQUEST['overs'.$i]<>$_REQUEST['maidens'.$i]){
							if($_REQUEST['overs'.$i]>=1){
								$error->general = 1; 
								$error->message = $result[$i]['player']." has conceded 0 runs from ".$_REQUEST['overs'.$i]." overs, which means the number of maidens bowled must match the number of overs bowled.  Please check.";
								return $error;
							}								
						}
					}
					else {
						$error->general = 1; 
						$error->message = "The runs conceded for ".$result[$i]['player']." are not valid, please check.";
						return $error;							
					}
				}
				else {
					$error->general = 1; 
					$error->message = "Please enter how many runs ".$result[$i]['player']." went for.";
					return $error;						
				}
				//check to see if user has enter has entered extras
				if($_REQUEST['wides'.$i]<>"" && $_REQUEST['noballs'.$i]<>""){
					//and check they are valid
					if(is_numeric($_REQUEST['wides'.$i]) && is_numeric($_REQUEST['noballs'.$i])){
						//check runs conceded compared to the number of extras.				
						if(($_REQUEST['wides'.$i]+$_REQUEST['noballs'.$i])>$_REQUEST['runsconceded'.$i]){
							$error->general = 1; 
							$error->message = "The extras ".$result[$i]['player']." conceded, equals more than the number of runs ".$result[$i]['firstname']." conceded.  Please check."; 
							return $error;
						}
					}
					else {
						$error->general = 1; 
						$error->message = "The extras for ".$result[$i]['player']." are not valid, please check.";
						return $error;						
					}
				}
				else if($_REQUEST['wides'.$i]<>"" && $_REQUEST['noballs'.$i]==""){
					if(is_numeric($_REQUEST['wides'.$i])){								
						if($_REQUEST['wides'.$i] > $_REQUEST['runsconceded'.$i]){
							$error->general = 1; 
							$error->message = "The extras ".$result[$i]['player']." conceded, equals more than the number of runs ".$result[$i]['firstname']." conceded.  Please check."; 
							return $error;
						}
					}
					else {
						$error->general = 1; 
						$error->message = "The extras for ".$result[$i]['player']." are not valid, please check.";
						return $error;								
					}					
				}
				else if($_REQUEST['wides'.$i]=="" && $_REQUEST['noballs'.$i]<>""){
					if(is_numeric($_REQUEST['noballs'.$i])){					
						if($_REQUEST['noballs'.$i] > $_REQUEST['runsconceded'.$i]){
							$error->general = 1; 
							$error->message = "The extras ".$result[$i]['player']." conceded, equals more than the number of runs ".$result[$i]['firstname']." conceded.  Please check."; 
							return $error;		
						}			
					}
					else {
						$error->general = 1; 
						$error->message = "The boundaries for ".$result[$i]['player']." are not valid, please check.";
						return $error;								
					}
				}
			}
			else {
				$error->general = 1; 
				$error->message = "The overs for ".$result[$i]['player']." are not valid, please check.";
				return $error;				
			}
		}
		else {
			if($_REQUEST['maidens'.$i]<>"" || $_REQUEST['runsconceded'.$i]<>"" || $_REQUEST['wides'.$i]<>"" || $_REQUEST['noballs'.$i]<>""){
				$error->general = 1; 
				$error->message = "Please enter how many overs ".$result[$i]['player']." bowled.";
				return $error;			
			}
		}
		if($mtid==2){
			//user has entered overs
			if($_REQUEST['overs2'.$i]<>""){
				//check that overs entered are valid
				if(is_numeric($_REQUEST['overs2'.$i])){
					//check if maidens entered and are valid
					if($_REQUEST['maidens2'.$i]<>""){
						if(is_numeric($_REQUEST['maidens2'.$i])){
							//cannot bowl more maidens than overs
							if($_REQUEST['maidens2'.$i] > $_REQUEST['overs2'.$i]){
								$error->general = 1; 
								$error->message = $result[$i]['player']." cannot bowl more maidens than overs, please check.";
								return $error;							
							}
						}
						else {
							$error->general = 1; 
							$error->message = "The maidens for ".$result[$i]['player']." are not valid, please check.";
							return $error;									
						}
					}
					//check if runs conceded have been entered, and are valid.
					if($_REQUEST['runsconceded2'.$i]<>""){
						if(is_numeric($_REQUEST['runsconceded2'.$i])){
							//if overs = maidens, then runs conceded must be 0.
							if($_REQUEST['overs2'.$i] == $_REQUEST['maidens2'.$i] && $_REQUEST['runsconceded2'.$i] <> 0){
								$error->general = 1; 
								$error->message = $result[$i]['player']." has bowled ".$_REQUEST['overs2'.$i]." overs and bowled ".$_REQUEST['maidens2'.$i]." maidens, which means runs conceded must be 0.  Please check";
								return $error;								
							}
							//if runs conceded = 0, the maidens bowled must = the number of overs bowled.
							if($_REQUEST['runsconceded2'.$i]==0 && $_REQUEST['overs2'.$i]<>$_REQUEST['maidens2'.$i]){
								if($_REQUEST['overs2'.$i]>=1){
									$error->general = 1; 
									$error->message = $result[$i]['player']." has conceded 0 runs from ".$_REQUEST['overs2'.$i]." overs, which means the number of maidens bowled must match the number of overs bowled.  Please check.";
									return $error;	
								}							
							}
						}
						else {
							$error->general = 1; 
							$error->message = "The runs conceded for ".$result[$i]['player']." are not valid, please check.";
							return $error;							
						}
					}
					else {
						$error->general = 1; 
						$error->message = "Please enter how many runs ".$result[$i]['player']." went for in the second innings.";
						return $error;						
					}
					//check to see if user has enter has entered extras
					if($_REQUEST['wides2'.$i]<>"" && $_REQUEST['noballs2'.$i]<>""){
						//and check they are valid
						if(is_numeric($_REQUEST['wides2'.$i]) && is_numeric($_REQUEST['noballs2'.$i])){
							//check runs conceded compared to the number of extras.				
							if(($_REQUEST['wides2'.$i]+$_REQUEST['noballs2'.$i])>$_REQUEST['runsconceded2'.$i]){
								$error->general = 1; 
								$error->message = "The extras ".$result[$i]['player']." conceded, equals more than the number of runs ".$result[$i]['firstname']." conceded.  Please check."; 
								return $error;
							}
						}
						else {
							$error->general = 1; 
							$error->message = "The extras for ".$result[$i]['player']." are not valid, please check.";
							return $error;						
						}
					}
					else if($_REQUEST['wides2'.$i]<>"" && $_REQUEST['noballs2'.$i]==""){
						if(is_numeric($_REQUEST['wides2'.$i])){								
							if($_REQUEST['wides2'.$i] > $_REQUEST['runsconceded2'.$i]){
								$error->general = 1; 
								$error->message = "The extras ".$result[$i]['player']." conceded, equals more than the number of runs ".$result[$i]['firstname']." conceded.  Please check."; 
								return $error;
							}
						}
						else {
							$error->general = 1; 
							$error->message = "The extras for ".$result[$i]['player']." are not valid, please check.";
							return $error;								
						}					
					}
					else if($_REQUEST['wides2'.$i]=="" && $_REQUEST['noballs2'.$i]<>""){
						if(is_numeric($_REQUEST['noballs2'.$i])){					
							if($_REQUEST['noballs2'.$i] > $_REQUEST['runsconceded2'.$i]){
								$error->general = 1; 
								$error->message = "The extras ".$result[$i]['player']." conceded, equals more than the number of runs ".$result[$i]['firstname']." conceded.  Please check."; 
								return $error;		
							}			
						}
						else {
							$error->general = 1; 
							$error->message = "The boundaries for ".$result[$i]['player']." are not valid, please check.";
							return $error;								
						}
					}
				}
				else {
					$error->general = 1; 
					$error->message = "The overs for ".$result[$i]['player']." are not valid, please check.";
					return $error;				
				}
			}
			else {
				if($_REQUEST['maidens2'.$i]<>"" || $_REQUEST['runsconceded2'.$i]<>"" || $_REQUEST['wides2'.$i]<>"" || $_REQUEST['noballs2'.$i]<>""){
					$error->general = 1; 
					$error->message = "Please enter how many overs ".$result[$i]['player']." bowled in the second innings.";
					return $error;			
				}
			}		
		}
		if($i==$count && $error->general==0){
			return $error;
		}
	}
}
?>
<script language="javascript">
function Next(){
	frm = document.bowling;
	frm.bowlingaction.value="next";
	frm.cont_6.value=1;
	frm.submit();
}
function Exit(){
	ans = confirm("Are you sure you want to exit? Match details have not been completed.");
	if (ans == true) {
		frm = document.bowling;
		frm.bowlingaction.value="exit";
		frm.submit();
	}
}
</script>