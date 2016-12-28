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
<link rel="stylesheet" href="inc/calendar.css">
</head>
<script type="text/javascript" src="inc/calendar_eu.js"></script>
<script language="javascript">
var basefolder='<?php echo HOSTNAME; ?>';
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
if(isset($_SESSION['m']['cont_2'])){
	if($_SESSION['m']['cont_2'] <> 1){
		set_variables();
	}
}
else {
	set_variables();
}
if(isset($_REQUEST['cont_2'])){
	set_variables();
}
$error=0;
$dateerror=0;

/***********
Processing
***********/
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=='next'){
		$errorobj = check_form_fields();
		$error = $errorobj->general;
		$dateerror = $errorobj->dateerror;
		if($error==0 && $dateerror==0){
			redirect_rel('process.php?id=2&np=matchselectplayers.php');
		}
	}
	else if($_REQUEST['action']=="exit"){
		unsetSessions();
		redirect_rel('matches.php');	
	}
}
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px;">
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
								<td align="left" valign="middle" style="font-size:24px; padding-left:20px; padding-top:10px;" id="whitetext" colspan="3"><strong><?php echo "Enter match details"; ?></strong></td>
							</tr>
							<tr>
								<td colspan="5" width="100%" valign="top" style="padding-left:5px; padding-right:5px;"><?php matchDetails($user->teamid,$error,$dateerror); ?></td>
							</tr>
						</table>
						</td>
						<td bgcolor="<?php echo $colour; ?>" width="30" height="10%" style="table-layout:fixed ">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="padding-top:10px;" width="175">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="center" style="padding-left:5px; padding-right:5px; "><?php steps(2); ?></td>
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
function matchDetails($teamid,$error,$dateerror){
$sql = "call getTeamDetails(".$teamid.")";
$result = dbToArray($sql);

if($error==1 || $dateerror==1){
	$_REQUEST["opponent"] = set_param("opponent");
	$_REQUEST["date"] = set_param("date");
	$_REQUEST["season"] = set_param("season");
	$_REQUEST["venue"] = set_param("venue");
}
?>
<form name="matchdetails" action="matchdetails.php" method="post">
<input type="hidden" name="action">
<input type="hidden" name="cont_2">
<input type="hidden" name="teamid" value="<?php echo $teamid; ?>"> 
<input type="hidden" name="teamname" value="<?php echo $result[1]['teamname']; ?>">
<table cellpadding="5" cellspacing="0" border="0" width="80%" id="standard">
	<?php
	if($error==1){?>
	<tr>
		<td style="padding-left:15px;" colspan="4" align="right"><?php validation("Please fill in all required fields."); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td width="40%" style="padding-left:15px;">Your team</td>
		<td>&nbsp;</td>
		<td width="40%">Opposition<font size="+1" color="#C80101";><sup>*</sup></font></td>
	</tr>
	<tr>
		<td style="padding-left:15px; color:#CCCCCC;"><?php echo $result[1]['teamname']; ?></td>
		<td width="10%"><strong>VS</strong></td>
		<td><?php print_textBox("opponent", $_SESSION['m']['opponent'], "size='32'",$error); ?></td>
		<script language="javascript">document.matchdetails.opponent.focus();</script>
	</tr>
	<tr>
		<td style="padding-left:15px; padding-top:20px;">Competition</td>
		<td>&nbsp;</td>
		<td style="padding-top:20px; ">Grade</td>
	</tr>
	<tr>
		<td style="padding-left:15px; color:#CCCCCC;">
		<?php 
		$comp = dbToArray("call getComps('where compid=".$_SESSION['m']['compid']."')");
		echo $comp[1]['competition'];
		?>
		<input type="hidden" name="mt" value="<?php echo $comp[1]['mtid']; ?>">
		<input type="hidden" name="comp" value="<?php echo $comp[1]['competition']; ?>">
		</td>
		<td>&nbsp;</td>
		<td style=" color:#CCCCCC;">
		<?php
		$grade = dbToArray("call getGrades('where gradeid=".$_SESSION['m']['gradeid']."')"); 
		echo $grade[1]['grade']; 
		?>
		<input type="hidden" name="grade" value="<?php echo $grade[1]['grade']; ?>">
		</td>
	</tr>		
	<tr>
		<td style="padding-left:15px; padding-top:20px;">Date (of 1st innings)<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td>&nbsp;</td>
		<td style="padding-top:20px;">Season (e.g. 2009/10)</td>
	</tr>
	<tr>
		<td style="padding-left:15px;">
		<?php print_textBox("date", $_SESSION['m']['date'], "size='32' maxlength='32'",$error); ?>&nbsp;&nbsp;
		<script language="JavaScript">
		new tcal ({
		// form name
		'formname': 'matchdetails',
		// input name
		'controlname': 'date'
		});
		</script>
		</td>
		<td>&nbsp;</td>
		<td><?php print_textBox("season", $_SESSION['m']['season'], "size='32'",$error); ?></td>
	</tr>
	<?php
	if($dateerror==1){ ?>
	<tr>
		<td colspan="2" style="padding-left:15px; "><?php validation("Please enter a valid date using format: dd-mm-yyyy."); ?></td>
	</tr>
	<?php }
	?>
	<tr>
		<td style="padding-left:15px; padding-top:20px;">Venue<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td>&nbsp;</td>
		<td style="padding-top:20px;">Result<font size="+1" color="#C80101";><sup>*</sup></font></td>
	</tr>
	<tr>
		<td style="padding-left:15px;"><?php print_textBox("venue", $_SESSION['m']['venue'], "size='32' maxlength='32'",$error); ?></td>	
		<td>&nbsp;</td>
		<td><?php Result($comp[1]['mtid'],$error); ?></td>
	</tr>
	<tr>
		<td style="padding-left:15px; padding-top:20px;">Match summary (optional)</td>
	</tr>
	<tr>
		<td style="padding-left:15px;" colspan="3"><?php print_textArea('summary', $_SESSION['m']['summary'], 'style= "width:600; height:150;"'); ?></td>
	</tr>
	<tr>
		<td style="padding-left:20px; padding-top:40px; ">
		<table cellpadding="0" cellspacing="0" id="whitetext">
			<tr>
				<td><input type="button" value="Next >>" onClick="Next();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold; height:35px;"></td>
				<td>&nbsp;|&nbsp;<a href="matchselectgrade.php">Back</a></td>
				<td>&nbsp;|&nbsp;<a href="#" onClick="Exit();">Exit</a></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
<script language="javascript">
function Next(){
	frm = document.matchdetails;
	frm.action.value="next";
	frm.cont_2.value=1;
	frm.submit();
}

function Exit(){
	ans = confirm("Are you sure you want to exit? Match details have not been completed.");
	if (ans == true) {
		frm = document.matchdetails;
		frm.action.value="exit";
		frm.submit();
	}
}
</script>
<?php
}

function Result($t1,$error){
	$t2 = 3;
	$result = dbToArray("call getResultTypes($t1, $t2)");
	print_dropDown("resultid", "Select result", $result, $_SESSION['m']['resultid'],'style= "width:220;"');
	if($error==1){
		echo "<script language='javascript'>document.matchdetails.resultid.style.backgroundColor='pink';</script>";
	}
}

function set_variables() {
	$_REQUEST['teamname'] 		= set_param('teamname');
	$_REQUEST['teamid'] 		= set_param('teamid');
	$_REQUEST['opponent'] 		= set_param('opponent');
	$_REQUEST['date'] 			= set_param('date');
	$_REQUEST['venue'] 			= set_param('venue');
	$_REQUEST['comp'] 			= set_param('comp');
	$_REQUEST['grade'] 			= set_param('grade');
	$_REQUEST['resultid'] 		= set_param('resultid');
	$_REQUEST['cont_2'] 			= set_param('cont_2');
	$_REQUEST['summary'] 			= set_param('summary');
	$_REQUEST['mt'] 			= set_param('mt');
	$_REQUEST['season'] 			= set_param('season');
	$_SESSION['m']['teamname'] 		= set_session_param('teamname');
	$_SESSION['m']['teamid'] 		= set_session_param('teamid');
	$_SESSION['m']['opponent'] 		= set_session_param('opponent');
	$_SESSION['m']['date'] 			= set_session_param('date');
	$_SESSION['m']['venue'] 			= set_session_param('venue');
	$_SESSION['m']['comp'] 			= set_session_param('comp');
	$_SESSION['m']['grade'] 			= set_session_param('grade');
	$_SESSION['m']['resultid'] 		= set_session_param('resultid');
	$_SESSION['m']['cont_2'] 			= set_session_param('cont_2');
	$_SESSION['m']['summary'] 		= set_session_param('summary');
	$_SESSION['m']['mt'] 		= set_session_param('mt');
	$_SESSION['m']['season'] 		= set_session_param('season');
	$_SESSION['m']['teamname'] 		= $_REQUEST['teamname'];
	$_SESSION['m']['teamid'] 		= $_REQUEST['teamid'];
	$_SESSION['m']['opponent'] 		= $_REQUEST['opponent'];
	$_SESSION['m']['date'] 			= $_REQUEST['date'];
	$_SESSION['m']['venue'] 			= $_REQUEST['venue'];
	$_SESSION['m']['comp'] 			= $_REQUEST['comp'];
	$_SESSION['m']['grade'] 			= $_REQUEST['grade'];
	$_SESSION['m']['resultid'] 		= $_REQUEST['resultid'];
	$_SESSION['m']['cont_2'] 			= $_REQUEST['cont_2'];
	$_SESSION['m']['summary'] 			= $_REQUEST['summary'];
	$_SESSION['m']['mt'] 			= $_REQUEST['mt'];
	$_SESSION['m']['season'] 			= $_REQUEST['season'];
}

function check_form_fields(){
	$error->general = 0;
	$error->message = '';
	$error->dateerror = 0;
	if($_REQUEST['opponent']=="") $error->general = 1;
	if($_REQUEST['date']=="") $error->general = 1;
	if($_REQUEST['season']=="") $error->general = 1;
	if($_REQUEST['venue']=="") $error->general = 1;
	if($_REQUEST['resultid']=="Select result") $error->general = 1;
	if($_REQUEST['date']<>""){
		if(!check_date($_REQUEST['date'])) $error->dateerror = 1;
	}
	return $error;
}

function check_date($date){
	//check format of the date
	if(preg_match("/^([0-9]{2})-([0-9]{2})-([0-9]{4})$/", $date, $parts)) {
		//check validation of the date
		if(checkdate($parts[2],$parts[1],$parts[3])){
			return true;
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}
?>