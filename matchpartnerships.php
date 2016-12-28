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
//print_r($_SESSION);
//echo "<br>";
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
$colour = "#F7C200";
$text = "Fixtures";
if($user->role<>"Team Admin"){
	redirect_rel('home.php');
}
$batsman = dbToArray("call getSelectedPlayers(".$user->teamid.",1)");


if(isset($_SESSION['m']['cont_5'])){
	if($_SESSION['m']['cont_5'] <> 1){
		set_variables($_SESSION['m']['mt']);
	}
}
else {
	set_variables($_SESSION['m']['mt']);
}
if(isset($_REQUEST['cont_5'])){
	set_variables($_SESSION['m']['mt']);
}

$error=0;
$errormsg = '';

/********************
Processing
********************/
if(isset($_REQUEST['partnershipaction'])){
	if($_REQUEST['partnershipaction']=="next"){
		$errorobj = check_form_fields($_SESSION['m']['mt']);
		$error = $errorobj->general;
		$errormsg = $errorobj->message;
		if($error==0){
			redirect_rel('process.php?id=2&np=matchperformancesbowling.php');
		}
	}
	else if($_REQUEST['partnershipaction']=="exit"){
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
								<?php partnerships($user->teamid, $batsman, $error, $errormsg,$_SESSION['m']['mt']); ?>
								</td>
							</tr>
							<tr>
								<td style="padding-left:20px; padding-top:40px; padding-bottom:20px; ">
								<table cellpadding="0" cellspacing="0" id="whitetext">
									<tr>
										<td><input type="button" value="Next >>" onClick="Next();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold; height:35px;"></td>
										<td>&nbsp;|&nbsp;<a href="matchperformancesbatting.php">Back</a></td>
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
								<td align="center" style="padding-left:5px; padding-right:5px; "><?php steps(5); ?></td>
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
function partnerships($teamid, $batsman,$error, $errormsg,$mtid){
$count = count($batsman);

$wicket = array(1 => "1st", 2 => "2nd", 3 => "3rd", 4 => "4th", 5 => "5th", 6 => "6th", 7 => "7th", 8 => "8th", 9 => "9th", 10 => "10th");
?>
<form name="partnerships" action="matchpartnerships.php" method="post">
<input type="hidden" name="partnershipaction">
<input type="hidden" name="cont_5">
<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td style="font-size:24px; padding-top:10px;" id="whitetext"><strong><?php echo "Enter batting partnerships"; ?></strong></td>
	</tr>
	<tr>
		<td style="padding-top:10px; padding-bottom:10px; font-size:18px;" id="whitetext">
		Enter the batting partnerships for each wicket by selecting the batsman from the drop down lists and entering how many runs their partnership was worth.
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
		<table cellpadding="5" cellspacing="0" width="90%" border="1" id="fixtures">
		<?php if($mtid==2) { ?>
			<tr>
				<td colspan="3"><strong>First Innings</strong></td>
			</tr>
		<?php } ?>
			<tr>
				<td>Wicket</td>
				<td>Partners</td>
				<td width="35%">Runs</td>
			</tr>
			<?php
			for($i=1;$i<=10;$i++){ ?>
			<tr>
				<input type="hidden" name="wicket<?php echo $i; ?>" value="<?php echo $i; ?>" />
				<td><?php echo $wicket[$i]; ?></td>
				<td>
				<?php 
				if(!isset($_SESSION['m']['batsmanid1_'.$i])){ $_SESSION['m']['batsmanid1_'.$i] =""; }
				print_dropDown("batsmanid1_".$i, "Select batsman 1", unstrip_array($batsman), $_SESSION['m']['batsmanid1_'.$i], 'style= ""'); 
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				if(!isset($_SESSION['m']['batsmanid2_'.$i])){ $_SESSION['m']['batsmanid2_'.$i] =""; }
				print_dropDown("batsmanid2_".$i, "Select batsman 2", unstrip_array($batsman), $_SESSION['m']['batsmanid2_'.$i], 'style= ""'); 
				?>
				</td>
				<td>
				<?php 
				if(!isset($_SESSION['m']['partnership'.$i])){ $_SESSION['m']['partnership'.$i] =""; }
				print_textBox("partnership".$i, $_SESSION['m']['partnership'.$i], "size='6' maxlength='6'"); ?>
				</td>
			</tr>
			<?php 
			} 
			if($mtid==2) { ?>
			<tr>
				<td colspan="3"><strong>Second Innings</strong></td>
			</tr>
			<tr>
				<td>Wicket</td>
				<td>Partners</td>
				<td width="35%">Runs</td>
			</tr>
			<?php
			for($i=1;$i<=10;$i++){ ?>
				<tr>
					<input type="hidden" name="wicket2<?php echo $i; ?>" value="<?php echo $i; ?>" />
					<td><?php echo $wicket[$i]; ?></td>
					<td>
					<?php 
					if(!isset($_SESSION['m']['batsmanid21_'.$i])){ $_SESSION['m']['batsmanid21_'.$i] =""; }
					print_dropDown("batsmanid21_".$i, "Select batsman 1", unstrip_array($batsman), $_SESSION['m']['batsmanid21_'.$i], 'style= ""'); 
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					if(!isset($_SESSION['m']['batsmanid22_'.$i])){ $_SESSION['m']['batsmanid22_'.$i] =""; }
					print_dropDown("batsmanid22_".$i, "Select batsman 2", unstrip_array($batsman), $_SESSION['m']['batsmanid22_'.$i], 'style= ""'); 
					?>
					</td>
					<td>
					<?php 
					if(!isset($_SESSION['m']['partnership2'.$i])){ $_SESSION['m']['partnership2'.$i] =""; }
					print_textBox("partnership2".$i, $_SESSION['m']['partnership2'.$i], "size='6' maxlength='6'"); ?>
					</td>
				</tr>
				<?php 
				}
			}
			?>
		</table>			
		</td>
	</tr>
</table>
</form>
<?php
}
function set_variables($mtid) {
	for($i=1; $i<=10; $i++){
		$_REQUEST['batsmanid1_'.$i] 		= set_param('batsmanid1_'.$i);
		$_SESSION['m']['batsmanid1_'.$i] 		= set_session_param('batsmanid1_'.$i);
		$_SESSION['m']['batsmanid1_'.$i] 		= $_REQUEST['batsmanid1_'.$i];
		$_REQUEST['batsmanid2_'.$i] 		= set_param('batsmanid2_'.$i);
		$_SESSION['m']['batsmanid2_'.$i] 		= set_session_param('batsmanid2_'.$i);
		$_SESSION['m']['batsmanid2_'.$i] 		= $_REQUEST['batsmanid2_'.$i];
		$_REQUEST['wicket'.$i] 		= set_param('wicket'.$i);
		$_SESSION['m']['wicket'.$i] 		= set_session_param('wicket'.$i);
		$_SESSION['m']['wicket'.$i] 		= $_REQUEST['wicket'.$i];
		$_REQUEST['partnership'.$i] 		= set_param('partnership'.$i);
		$_SESSION['m']['partnership'.$i] 		= set_session_param('partnership'.$i);
		$_SESSION['m']['partnership'.$i] 		= $_REQUEST['partnership'.$i];
		if($mtid==2){
			$_REQUEST['batsmanid21_'.$i] 		= set_param('batsmanid21_'.$i);
			$_SESSION['m']['batsmanid21_'.$i] 		= set_session_param('batsmanid21_'.$i);
			$_SESSION['m']['batsmanid21_'.$i] 		= $_REQUEST['batsmanid21_'.$i];
			$_REQUEST['batsmanid22_'.$i] 		= set_param('batsmanid22_'.$i);
			$_SESSION['m']['batsmanid22_'.$i] 		= set_session_param('batsmanid22_'.$i);
			$_SESSION['m']['batsmanid22_'.$i] 		= $_REQUEST['batsmanid22_'.$i];
			$_REQUEST['wicket2'.$i] 		= set_param('wicket2'.$i);
			$_SESSION['m']['wicket2'.$i] 		= set_session_param('wicket2'.$i);
			$_SESSION['m']['wicket2'.$i] 		= $_REQUEST['wicket2'.$i];
			$_REQUEST['partnership2'.$i] 		= set_param('partnership2'.$i);
			$_SESSION['m']['partnership2'.$i] 		= set_session_param('partnership2'.$i);
			$_SESSION['m']['partnership2'.$i] 		= $_REQUEST['partnership2'.$i];		
		}
		$_REQUEST['cont_5'] 		= set_param('cont_5');
		$_SESSION['m']['cont_5'] 		= set_session_param('cont_5');
		$_SESSION['m']['cont_5'] 		= $_REQUEST['cont_5'];
	}
}

function check_form_fields($mtid){
	$error->general = 0;
	$error->message = '';
	for($i=1; $i<=10; $i++){
		if($_REQUEST['batsmanid1_'.$i]<>"Select batsman 1" && $_REQUEST['batsmanid2_'.$i]<>"Select batsman 2"){ //user has selected both batsman
			$player1 = dbToArray("call getPlayers('where u.userid=".$_REQUEST['batsmanid1_'.$i]."','','');");
			$player2 = dbToArray("call getPlayers('where u.userid=".$_REQUEST['batsmanid2_'.$i]."','','');");
			//check that the same player has not been selected for the same partnership.
			if($_REQUEST['batsmanid1_'.$i]==$_REQUEST['batsmanid2_'.$i]){
				//same player selected, display error
				$error->general = 1;
				$error->message = stripslashes($player1[1]['player'])." cannot be selected twice for the same partnership.";
				return $error;			
			}
			//check that the partnership has been entered
			if($_REQUEST['partnership'.$i]<>""){ // user has entered partnership.  
				//check that it is a valid entry
				if(!is_numeric($_REQUEST['partnership'.$i])){
					//not a valid entry, display error
					$error->general = 1;
					$error->message = $_REQUEST['partnership'.$i]." is not a valid partnership entry.";
					return $error;
				}
			}
			else {
				//user has not entered how many runs the partnership was worth
				//$player1 = dbToArray("call getPlayers('where u.userid=".$_REQUEST[
				$error->general = 1;
				$error->message = "Please enter how many runs the partnership between ".stripslashes($player1[1]['player'])." and ".stripslashes($player2[1]['player'])." was worth.";
				return $error;
			}
		}
		else if($_REQUEST['batsmanid1_'.$i]<>"Select batsman 1" && $_REQUEST['batsmanid2_'.$i]=="Select batsman 2"){ //user has only selected batsman 1
			$player1 = dbToArray("call getPlayers('where u.userid=".$_REQUEST['batsmanid1_'.$i]."','','');");
			//display error
			$error->general = 1;
			$error->message = "Please select ". stripslashes($player1[1]['player'])."'s batting partner.";
			return $error;
		}
		else if($_REQUEST['batsmanid1_'.$i]=="Select batsman 1" && $_REQUEST['batsmanid2_'.$i]<>"Select batsman 2"){ //user has only selected batsman 2
			$player2 = dbToArray("call getPlayers('where u.userid=".$_REQUEST['batsmanid2_'.$i]."','','');");
			//display error
			$error->general = 1;
			$error->message = "Please select ". stripslashes($player2[1]['player'])."'s batting partner.";
			return $error;
		}
		else if($_REQUEST['batsmanid1_'.$i]=="Select batsman 1" && $_REQUEST['batsmanid2_'.$i]=="Select batsman 2"){
			//check that the partnership has been entered
			if($_REQUEST['partnership'.$i]<>""){ // user has entered partnership.  
				$error->general = 1;
				$error->message = "You have enter a partnership of ".$_REQUEST['partnership'.$i]." runs, but have not selected the batting partners.";
				return $error;
			}			
		}
		if($mtid==2){
			if($_REQUEST['batsmanid21_'.$i]<>"Select batsman 1" && $_REQUEST['batsmanid22_'.$i]<>"Select batsman 2"){ //user has selected both batsman
				$player1 = dbToArray("call getPlayers('where u.userid=".$_REQUEST['batsmanid21_'.$i]."','','');");
				$player2 = dbToArray("call getPlayers('where u.userid=".$_REQUEST['batsmanid22_'.$i]."','','');");
				//check that the same player has not been selected for the same partnership.
				if($_REQUEST['batsmanid21_'.$i]==$_REQUEST['batsmanid22_'.$i]){
					//same player selected, display error
					$error->general = 1;
					$error->message = stripslashes($player1[1]['player'])." cannot be selected twice for the same partnership.";
					return $error;			
				}
				//check that the partnership has been entered
				if($_REQUEST['partnership2'.$i]<>""){ // user has entered partnership.  
					//check that it is a valid entry
					if(!is_numeric($_REQUEST['partnership2'.$i])){
						//not a valid entry, display error
						$error->general = 1;
						$error->message = $_REQUEST['partnership2'.$i]." is not a valid partnership entry.";
						return $error;
					}
				}
				else {
					//user has not entered how many runs the partnership was worth
					//$player1 = dbToArray("call getPlayers('where u.userid=".$_REQUEST[
					$error->general = 1;
					$error->message = "Please enter how many runs the partnership between ".stripslashes($player1[1]['player'])." and ".stripslashes($player2[1]['player'])." was worth.";
					return $error;
				}
			}
			else if($_REQUEST['batsmanid21_'.$i]<>"Select batsman 1" && $_REQUEST['batsmanid22_'.$i]=="Select batsman 2"){ //user has only selected batsman 1
				$player1 = dbToArray("call getPlayers('where u.userid=".$_REQUEST['batsmanid21_'.$i]."','','');");
				//display error
				$error->general = 1;
				$error->message = "Please select ". stripslashes($player1[1]['player'])."'s batting partner.";
				return $error;
			}
			else if($_REQUEST['batsmanid21_'.$i]=="Select batsman 1" && $_REQUEST['batsmanid22_'.$i]<>"Select batsman 2"){ //user has only selected batsman 2
				$player2 = dbToArray("call getPlayers('where u.userid=".$_REQUEST['batsmanid22_'.$i]."','','');");
				//display error
				$error->general = 1;
				$error->message = "Please select ". stripslashes($player2[1]['player'])."'s batting partner.";
				return $error;
			}	
			else if($_REQUEST['batsmanid21_'.$i]=="Select batsman 1" && $_REQUEST['batsmanid22_'.$i]=="Select batsman 2"){
				//check that the partnership has been entered
				if($_REQUEST['partnership2'.$i]<>""){ // user has entered partnership.  
					$error->general = 1;
					$error->message = "You have enter a partnership of ".$_REQUEST['partnership2'.$i]." runs, but have not selected the batting partners.";
					return $error;
				}			
			}	
		}
	}
	return $error;
}
?>
<script language="javascript">
function Next(){
	frm = document.partnerships;
	frm.partnershipaction.value="next";
	frm.cont_5.value=1;
	frm.submit();
}

function Exit(){
	ans = confirm("Are you sure you want to exit? Match details have not been completed.");
	if (ans == true) {
		frm = document.partnerships;
		frm.partnershipaction.value="exit";
		frm.submit();
	}
}
</script>