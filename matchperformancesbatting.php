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
			document.getElementById('battingorder').value = theorder; 
	    },
		onDragStart: function(table, row) {
			//$(#debugArea).html("Started dragging row "+row.id);
		}
	}); 
});
</script>
<body>
<script type="text/javascript" src="toolstips/wz_tooltip.js"></script>
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
if(isset($_SESSION['m']['cont_4'])){
	if($_SESSION['m']['cont_4'] <> 1){
		set_variables($result);
	}
}
else {
	set_variables($result);
}
if(isset($_REQUEST['cont_4'])){
	set_variables($result);
}
$error=0;
$errormsg = '';
//print_r($_SESSION);
/********************
Processing
********************/
if(isset($_REQUEST['battingaction'])){
	if($_REQUEST['battingaction']=="next"){
		$errorobj = check_form_fields($result,$_SESSION['m']['mt']);
		$error = $errorobj->general;
		$errormsg = $errorobj->message;
		if($error==0){
			redirect_rel('process.php?id=2&np=matchpartnerships.php');
		}
	}
	else if($_REQUEST['battingaction']=="exit"){
		unsetSessions();
		redirect_rel('matches.php');	
	}
}
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
										<td>&nbsp;|&nbsp;<a href="matchselectplayers.php">Back</a></td>
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
								<td align="center" style="padding-left:5px; padding-right:5px; "><?php steps(4); ?></td>
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
function performances1($teamid, $result,$error, $errormsg,$mtid){
$count = count($result);
?>
<form name="batting" action="matchperformancesbatting.php" method="post">
<input type="hidden" name="battingaction">
<input type="hidden" name="cont_4">
<input type="hidden" name="battingorder" id="battingorder" value="<?php echo (isset($_SESSION['m']['battingorder'])) ? $_SESSION['m']['battingorder'] : "" ?>">
<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td style="font-size:24px; padding-top:10px;" id="whitetext"><strong><?php echo "Enter batting performances"; ?></strong></td>
	</tr>
	<tr>
		<td style="padding-top:10px; padding-bottom:10px; font-size:18px;" id="whitetext">
		To put team into batting order, click and drag the players name to the position you want the player in.
		</td>
	</tr>
	<?php
	if($error==1){?>
	<tr>
		<td style="padding-bottom:20px; "><?php validation($errormsg); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td valign="top">
		<table cellpadding="5" cellspacing="0" width="100%" border="1" id="fixtures">
			<tr class="nodrop nodrag">
				<td>&nbsp;</td>
				<td>Runs</td>
				<td>Balls faced</td>
				<td>How out</td>
				<td>4s&nbsp;&nbsp;<img src="images/Question.png" onMouseOver="Tip('How many fours did the player hit?  E.g 3.', BGCOLOR,'#ffffff', BORDERCOLOR,'#dddddd', DELAY,300, STICKY,false, CLOSEBTN,false, CLICKCLOSE,true, FOLLOWMOUSE, false, PADDING,16, SHADOW,true, SHADOWCOLOR,'#cccccc', SHADOWWIDTH,2, WIDTH,150, FIX, [this,-125,5]);" onMouseOut="UnTip();"></td>
				<td>6s&nbsp;&nbsp;<img src="images/Question.png" onMouseOver="Tip('How many sixes did the player hit?  E.g 1.', BGCOLOR,'#ffffff', BORDERCOLOR,'#dddddd', DELAY,300, STICKY,false, CLOSEBTN,false, CLICKCLOSE,true, FOLLOWMOUSE, false, PADDING,16, SHADOW,true, SHADOWCOLOR,'#cccccc', SHADOWWIDTH,2, WIDTH,150, FIX, [this,-125,5]);" onMouseOut="UnTip();"></td>
			</tr>
			<?php
			$dismissal = dbToArray("call getDismissal('')");		
			for($i=1; $i<=$count; $i++){ 
			$name = "playerid".$i;
			?>			
			<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $result[$i]['playerid']; ?>">			
			<tr id="<?php echo $i; ?>">
				<td><?php echo stripslashes($result[$i]['player']); ?></td>	
				<td>
				<?php if(!isset($_SESSION['m']['runs'.$i])){ $_SESSION['m']['runs'.$i]=""; }
				print_textBox("runs".$i, $_SESSION['m']['runs'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['balls'.$i])){ $_SESSION['m']['balls'.$i]=""; }
				print_textBox("balls".$i, $_SESSION['m']['balls'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['dismissal'.$i])){ $_SESSION['m']['dismissal'.$i]=""; }
				print_dropDown("dismissal".$i, "Select mode of dismissal", $dismissal, $_SESSION['m']['dismissal'.$i], 'style= ""'); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['fours'.$i])){ $_SESSION['m']['fours'.$i]=""; }
				print_textBox("fours".$i, $_SESSION['m']['fours'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['sixes'.$i])){ $_SESSION['m']['sixes'.$i]=""; }
				print_textBox("sixes".$i, $_SESSION['m']['sixes'.$i], "size='4' maxlength='4'"); 
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

function performances2($teamid, $result,$error, $errormsg,$mtid){
$count = count($result);
if(isset($_REQUEST['battingaction'])){
	if($_REQUEST['battingaction']=="next"){
		$errorobj = check_form_fields($result,$mtid);
		$error = $errorobj->general;
		$errormsg = $errorobj->message;
		if($error==0){
			redirect_rel('process.php?id=2&np=matchperformancesbowling.php');
		}
	}
	else if($_REQUEST['battingaction']=="exit"){
		redirect_rel('home.php');
		unsetSessions();
	}
}
?>
<form name="batting" action="matchperformancesbatting.php" method="post">
<input type="hidden" name="battingaction">
<input type="hidden" name="cont_4">
<input type="hidden" name="battingorder" id="battingorder" value="<?php echo (isset($_SESSION['m']['battingorder'])) ? $_SESSION['m']['battingorder'] : "" ?>">
<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td style="font-size:24px; padding-top:10px;" id="whitetext"><strong><?php echo "Enter batting performances"; ?></strong></td>
	</tr>
	<tr>
		<td style="padding-top:10px; padding-bottom:10px; font-size:18px;" id="whitetext">
		To put team into batting order, click and drag the players name to the position you want the player in.
		</td>
	</tr>
	<?php
	if($error==1){?>
	<tr>
		<td style="padding-bottom:20px; "><?php validation($errormsg); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td valign="top">
		<table cellpadding="5" cellspacing="0" width="100%" border="1" id="fixtures">
			<tr class="nodrop nodrag">
				<td colspan="6"><strong>First Innings</strong></td>
			</tr>
			<tr class="nodrop nodrag">
				<td>&nbsp;</td>
				<td>Runs</td>
				<td>Balls faced</td>
				<td>How out</td>
				<td>4s&nbsp;&nbsp;<img src="images/Question.png" onMouseOver="Tip('How many fours did the player hit?  E.g 3.', BGCOLOR,'#ffffff', BORDERCOLOR,'#dddddd', DELAY,300, STICKY,false, CLOSEBTN,false, CLICKCLOSE,true, FOLLOWMOUSE, false, PADDING,16, SHADOW,true, SHADOWCOLOR,'#cccccc', SHADOWWIDTH,2, WIDTH,150, FIX, [this,-125,5]);" onMouseOut="UnTip();"></td>
				<td>6s&nbsp;&nbsp;<img src="images/Question.png" onMouseOver="Tip('How many sixes did the player hit?  E.g 1.', BGCOLOR,'#ffffff', BORDERCOLOR,'#dddddd', DELAY,300, STICKY,false, CLOSEBTN,false, CLICKCLOSE,true, FOLLOWMOUSE, false, PADDING,16, SHADOW,true, SHADOWCOLOR,'#cccccc', SHADOWWIDTH,2, WIDTH,150, FIX, [this,-125,5]);" onMouseOut="UnTip();"></td>
			</tr>
			<?php
			//echo $count;
			$dismissal = dbToArray("call getDismissal('')");		
			for($i=1; $i<=$count; $i++){ 
			$name = "playerid".$i;
			?>			
			<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $result[$i]['playerid']; ?>">			
			<tr id="<?php echo $i; ?>">
				<td><?php echo stripslashes($result[$i]['player']); ?></td>	
				<td>
				<?php if(!isset($_SESSION['m']['runs'.$i])){ $_SESSION['m']['runs'.$i]=""; }
				print_textBox("runs".$i, $_SESSION['m']['runs'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['balls'.$i])){ $_SESSION['m']['balls'.$i]=""; }
				print_textBox("balls".$i, $_SESSION['m']['balls'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['dismissal'.$i])){ $_SESSION['m']['dismissal'.$i]=""; }
				print_dropDown("dismissal".$i, "Select mode of dismissal", $dismissal, $_SESSION['m']['dismissal'.$i], 'style= ""'); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['fours'.$i])){ $_SESSION['m']['fours'.$i]=""; }
				print_textBox("fours".$i, $_SESSION['m']['fours'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['sixes'.$i])){ $_SESSION['m']['sixes'.$i]=""; }
				print_textBox("sixes".$i, $_SESSION['m']['sixes'.$i], "size='4' maxlength='4'"); 
				?></td>
			</tr>
			<?php } ?>
			<tr class="nodrop nodrag">
				<td colspan="6"><strong>Second Innings</strong></td>
			</tr>
			<tr class="nodrop nodrag">
				<td>&nbsp;</td>
				<td>Runs</td>
				<td>Balls faced</td>
				<td>How out</td>
				<td>4s&nbsp;&nbsp;<img src="images/Question.png" onMouseOver="Tip('How many fours did the player hit?  E.g 3.', BGCOLOR,'#ffffff', BORDERCOLOR,'#dddddd', DELAY,300, STICKY,false, CLOSEBTN,false, CLICKCLOSE,true, FOLLOWMOUSE, false, PADDING,16, SHADOW,true, SHADOWCOLOR,'#cccccc', SHADOWWIDTH,2, WIDTH,150, FIX, [this,-125,5]);" onMouseOut="UnTip();"></td>
				<td>6s&nbsp;&nbsp;<img src="images/Question.png" onMouseOver="Tip('How many sixes did the player hit?  E.g 1.', BGCOLOR,'#ffffff', BORDERCOLOR,'#dddddd', DELAY,300, STICKY,false, CLOSEBTN,false, CLICKCLOSE,true, FOLLOWMOUSE, false, PADDING,16, SHADOW,true, SHADOWCOLOR,'#cccccc', SHADOWWIDTH,2, WIDTH,150, FIX, [this,-125,5]);" onMouseOut="UnTip();"></td>
			</tr>
			<?php
			for($i=1; $i<=$count; $i++){ ?>
			<tr id="<?php echo $i; ?>">
				<td><?php echo stripslashes($result[$i]['player']); ?></td>	
				<td>
				<?php if(!isset($_SESSION['m']['runs2'.$i])){ $_SESSION['m']['runs2'.$i]=""; }
				print_textBox("runs2".$i, $_SESSION['m']['runs2'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['balls2'.$i])){ $_SESSION['m']['balls2'.$i]=""; }
				print_textBox("balls2".$i, $_SESSION['m']['balls2'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['dismissal2'.$i])){ $_SESSION['m']['dismissal2'.$i]=""; }
				print_dropDown("dismissal2".$i, "Select mode of dismissal", $dismissal, $_SESSION['m']['dismissal2'.$i], 'style= ""'); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['fours2'.$i])){ $_SESSION['m']['fours2'.$i]=""; }
				print_textBox("fours2".$i, $_SESSION['m']['fours2'.$i], "size='4' maxlength='4'"); 
				?></td>
				<td>
				<?php if(!isset($_SESSION['m']['sixes2'.$i])){ $_SESSION['m']['sixes2'.$i]=""; }
				print_textBox("sixes2".$i, $_SESSION['m']['sixes2'.$i], "size='4' maxlength='4'"); 
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
	$_REQUEST['runs'.$i] 		= set_param('runs'.$i);
	$_SESSION['m']['runs'.$i] 		= set_session_param('runs'.$i);
	$_SESSION['m']['runs'.$i] 		= $_REQUEST['runs'.$i];
	$_REQUEST['balls'.$i] 		= set_param('balls'.$i);
	$_SESSION['m']['balls'.$i] 		= set_session_param('balls'.$i);
	$_SESSION['m']['balls'.$i] 		= $_REQUEST['balls'.$i];
	$_REQUEST['dismissal'.$i] 		= set_param('dismissal'.$i);
	$_SESSION['m']['dismissal'.$i] 		= set_session_param('dismissal'.$i);
	$_SESSION['m']['dismissal'.$i] 		= $_REQUEST['dismissal'.$i];
	$_REQUEST['fours'.$i] 		= set_param('fours'.$i);
	$_SESSION['m']['fours'.$i] 		= set_session_param('fours'.$i);
	$_SESSION['m']['fours'.$i] 		= $_REQUEST['fours'.$i];
	$_REQUEST['sixes'.$i] 		= set_param('sixes'.$i);
	$_SESSION['m']['sixes'.$i] 		= set_session_param('sixes'.$i);
	$_SESSION['m']['sixes'.$i] 		= $_REQUEST['sixes'.$i];
	$_REQUEST['runs2'.$i] 		= set_param('runs2'.$i);
	$_SESSION['m']['runs2'.$i] 		= set_session_param('runs2'.$i);
	$_SESSION['m']['runs2'.$i] 		= $_REQUEST['runs2'.$i];
	$_REQUEST['balls2'.$i] 		= set_param('balls2'.$i);
	$_SESSION['m']['balls2'.$i] 		= set_session_param('balls2'.$i);
	$_SESSION['m']['balls2'.$i] 		= $_REQUEST['balls2'.$i];
	$_REQUEST['dismissal2'.$i] 		= set_param('dismissal2'.$i);
	$_SESSION['m']['dismissal2'.$i] 		= set_session_param('dismissal2'.$i);
	$_SESSION['m']['dismissal2'.$i] 		= $_REQUEST['dismissal2'.$i];
	$_REQUEST['fours2'.$i] 		= set_param('fours2'.$i);
	$_SESSION['m']['fours2'.$i] 		= set_session_param('fours2'.$i);
	$_SESSION['m']['fours2'.$i] 		= $_REQUEST['fours2'.$i];
	$_REQUEST['sixes2'.$i] 		= set_param('sixes2'.$i);
	$_SESSION['m']['sixes2'.$i] 		= set_session_param('sixes2'.$i);
	$_SESSION['m']['sixes2'.$i] 		= $_REQUEST['sixes2'.$i];
	$_REQUEST['playerid'.$i] 		= set_param('playerid'.$i);
	$_SESSION['m']['playerid'.$i] 		= set_session_param('playerid'.$i);
	$_SESSION['m']['playerid'.$i] 		= $_REQUEST['playerid'.$i];
	}
	$_REQUEST['battingorder'] 		= set_param('battingorder');
	$_SESSION['m']['battingorder'] 		= set_session_param('battingorder');
	$_SESSION['m']['battingorder'] 		= $_REQUEST['battingorder'];	
	$_REQUEST['cont_4'] 		= set_param('cont_4');
	$_SESSION['m']['cont_4'] 		= set_session_param('cont_4');
	$_SESSION['m']['cont_4'] 		= $_REQUEST['cont_4'];	
}

function check_form_fields($result,$mtid){
	$count = count($result);
	$error->general = 0;
	$error->message = '';
	for($i=1; $i<=$count; $i++){
		//user has entered runs scored.
		if($_REQUEST['runs'.$i]<>""){
			//check that it is a valid entry eg a number
			if(is_numeric($_REQUEST['runs'.$i])){
				//check if balls faced have been entered, if they have then check that they are valid
				if($_REQUEST['balls'.$i]<>""){ 
					if(is_numeric($_REQUEST['balls'.$i])){
						//if runs scored > 0 then balls faced cannot be 0
						if($_REQUEST['runs'.$i]>0 && $_REQUEST['balls'.$i]==0){
							$error->general = 1; 
							$error->message = $result[$i]['player']." cannot score ".$_REQUEST['runs'.$i]." runs off 0 balls faced, please check.";
							return $error;							
						}				
					}
					else {
						//not a number so display error
						$error->general = 1; 
						$error->message = "The balls faced for ".$result[$i]['player']." are not valid, please check.";
						return $error;							
					}
				}
				//check that user has selected mode of dismissal
				if($_REQUEST['dismissal'.$i]=="Select mode of dismissal"){
					$error->general = 1; 
					$error->message = "Please select how ".$result[$i]['player']." was dismissed.";
					return $error;			
				} 
				//check to see if user has enter has entered boundaries
				if($_REQUEST['fours'.$i]<>"" && $_REQUEST['sixes'.$i]<>""){
					//and check they are valid
					if(is_numeric($_REQUEST['fours'.$i]) && is_numeric($_REQUEST['sixes'.$i])){
						//check runs entered compared to the number of boundaries scored.
						$t4 = $_REQUEST['fours'.$i]*4;
						$t6 = $_REQUEST['sixes'.$i]*6;
					
						if(($t4+$t6)>$_REQUEST['runs'.$i]){
							$error->general = 1; 
							$error->message = "The boundaries ".$result[$i]['player']." has hit, equals more than the number of runs ".$result[$i]['firstname']." scored.  Please check."; 
							return $error;
						}
					}
					else {
						$error->general = 1; 
						$error->message = "The boundaries for ".$result[$i]['player']." are not valid, please check.";
						return $error;						
					}
				}
				else if($_REQUEST['fours'.$i]<>"" && $_REQUEST['sixes'.$i]==""){
					if(is_numeric($_REQUEST['fours'.$i])){
						$t4 = $_REQUEST['fours'.$i]*4;
					
						if($t4 > $_REQUEST['runs'.$i]){
							$error->general = 1; 
							$error->message = "The boundaries ".$result[$i]['player']." has hit, equals more than the number of runs ".$result[$i]['firstname']." scored.  Please check."; 
							return $error;
						}
					}
					else {
						$error->general = 1; 
						$error->message = "The boundaries for ".$result[$i]['player']." are not valid, please check.";
						return $error;								
					}					
				}
				else if($_REQUEST['fours'.$i]=="" && $_REQUEST['sixes'.$i]<>""){
					if(is_numeric($_REQUEST['sixes'.$i])){
						$t6 = $_REQUEST['sixes'.$i]*6;
					
						if($t6 > $_REQUEST['runs'.$i]){
							$error->general = 1; 
							$error->message = "The boundaries ".$result[$i]['player']." has hit, equals more than the number of runs ".$result[$i]['firstname']." scored.  Please check."; 
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
				//not a number so display error
				$error->general = 1; 
				$error->message = "The runs for ".$result[$i]['player']." are not valid, please check.";
				return $error;				
			}
		}
		else {
			if($_REQUEST['balls'.$i]<>"" || $_REQUEST['dismissal'.$i]<>"Select mode of dismissal" || $_REQUEST['fours'.$i]<>"" || $_REQUEST['sixes'.$i]<>""){
				$error->general = 1; 
				$error->message = "Please enter how many runs ".$result[$i]['player']." scored.";
				return $error;			
			}
		}
		if($mtid==2){
			//user has entered runs scored.
			if($_REQUEST['runs2'.$i]<>""){
				//check that it is a valid entry eg a number
				if(is_numeric($_REQUEST['runs2'.$i])){
					//check if balls faced have been entered, if they have then check that they are valid
					if($_REQUEST['balls2'.$i]<>""){ 
						if(is_numeric($_REQUEST['balls2'.$i])){
							//if runs scored > 0 then balls faced cannot be 0
							if($_REQUEST['runs2'.$i]>0 && $_REQUEST['balls2'.$i]==0){
								$error->general = 1; 
								$error->message = $result[$i]['player']." cannot score ".$_REQUEST['runs2'.$i]." runs off 0 balls faced, please check.";
								return $error;							
							}				
						}
						else {
							//not a number so display error
							$error->general = 1; 
							$error->message = "The balls faced for ".$result[$i]['player']." are not valid, please check.";
							return $error;							
						}
					}
					//check that user has selected mode of dismissal
					if($_REQUEST['dismissal2'.$i]=="Select mode of dismissal"){
						$error->general = 1; 
						$error->message = "Please select how ".$result[$i]['player']." was dismissed in the second innings.";
						return $error;			
					} 
					//check to see if user has enter has entered boundaries
					if($_REQUEST['fours2'.$i]<>"" && $_REQUEST['sixes2'.$i]<>""){
						//and check they are valid
						if(is_numeric($_REQUEST['fours2'.$i]) && is_numeric($_REQUEST['sixes2'.$i])){
							//check runs entered compared to the number of boundaries scored.
							$t4 = $_REQUEST['fours2'.$i]*4;
							$t6 = $_REQUEST['sixes2'.$i]*6;
						
							if(($t4+$t6)>$_REQUEST['runs2'.$i]){
								$error->general = 1; 
								$error->message = "The boundaries ".$result[$i]['player']." has hit, equals more than the number of runs ".$result[$i]['firstname']." scored.  Please check."; 
								return $error;
							}
						}
						else {
							$error->general = 1; 
							$error->message = "The boundaries for ".$result[$i]['player']." are not valid, please check.";
							return $error;						
						}
					}
					else if($_REQUEST['fours2'.$i]<>"" && $_REQUEST['sixes2'.$i]==""){
						if(is_numeric($_REQUEST['fours2'.$i])){
							$t4 = $_REQUEST['fours2'.$i]*4;
						
							if($t4 > $_REQUEST['runs2'.$i]){
								$error->general = 1; 
								$error->message = "The boundaries ".$result[$i]['player']." has hit, equals more than the number of runs ".$result[$i]['firstname']." scored.  Please check."; 
								return $error;
							}
						}
						else {
							$error->general = 1; 
							$error->message = "The boundaries for ".$result[$i]['player']." are not valid, please check.";
							return $error;								
						}					
					}
					else if($_REQUEST['fours2'.$i]=="" && $_REQUEST['sixes2'.$i]<>""){
						if(is_numeric($_REQUEST['sixes2'.$i])){
							$t6 = $_REQUEST['sixes2'.$i]*6;
						
							if($t6 > $_REQUEST['runs2'.$i]){
								$error->general = 1; 
								$error->message = "The boundaries ".$result[$i]['player']." has hit, equals more than the number of runs ".$result[$i]['firstname']." scored.  Please check."; 
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
					//not a number so display error
					$error->general = 1; 
					$error->message = "The runs for ".$result[$i]['player']." are not valid, please check.";
					return $error;				
				}
			}
			else {
				if($_REQUEST['balls2'.$i]<>"" || $_REQUEST['dismissal2'.$i]<>"Select mode of dismissal" || $_REQUEST['fours2'.$i]<>"" || $_REQUEST['sixes2'.$i]<>""){
					$error->general = 1; 
					$error->message = "Please enter how many runs ".$result[$i]['player']." scored in the second innings.";
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
	frm = document.batting;
	frm.battingaction.value="next";
	frm.cont_4.value=1;
	frm.submit();
}

function Exit(){
	ans = confirm("Are you sure you want to exit? Match details have not been completed.");
	if (ans == true) {
		frm = document.batting;
		frm.battingaction.value="exit";
		frm.submit();
	}
}
</script>