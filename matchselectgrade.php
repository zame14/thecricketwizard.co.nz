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
$colour = "#F7C200";
$text = "Fixtures";
if($user->role<>"Team Admin"){
	redirect_rel('home.php');
}
if(isset($_SESSION['m']['cont_1'])){
	if($_SESSION['m']['cont_1'] <> 1){
		set_variables();
	}
}
else {
	set_variables();
}
if(isset($_REQUEST['cont_1'])){
	set_variables();
}
$error=0;
$errormsg='';

/************************************************
Processing
************************************************/
if(isset($_REQUEST['gradeaction'])){
	if($_REQUEST['gradeaction']=="next"){
		$errorobj = check_form_fields();
		$error = $errorobj->general;
		$errormsg = $errorobj->message;
		if($error==0){
			if($_REQUEST['newgrade'] <> ""){
				$check = dbToArray("call adminCheck(".$user->teamid.",'".$_REQUEST['newgrade']."','grade');");
				if($check[1]['status']==0){		
					//new comp needs to be added to database
					$gradeid = 0;
					$sql="call insertGrade(".$gradeid.", '".addslashes($_REQUEST['newgrade'])."',".$user->teamid.");";
					$result =dbToArray($sql);
					$_SESSION['m']['gradeid'] = $result[1]['gradeid'];
				}
				else {
					$error=1;
					$errormsg="The new grade you entered already exists.";				
				}
			}
			if($error==0){
				redirect_rel('process.php?id=2&np=matchdetails.php');
			}
		}	
	}
	else if($_REQUEST['gradeaction']=="exit"){
		unsetSessions();
		redirect_rel('matches.php');
		
	}
}
if($error==1){
	$_REQUEST["newgrade"] = set_param("newgrade");
	$newgrade = $_REQUEST["newgrade"];
}
else {
	$newgrade="";
}
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px;">
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
								<td align="left" valign="middle" style="font-size:24px; padding-left:20px; padding-top:10px;" id="whitetext" colspan="3"><strong><?php echo "Select grade"; ?></strong></td>
							</tr>
							<tr>
								<td colspan="5" width="100%" valign="top" style="padding-left:5px; padding-right:5px;"><?php selectGrade($user->teamid,$error,$errormsg,$newgrade); ?></td>
							</tr>
						</table>
						</td>
						<td bgcolor="<?php echo $colour; ?>" width="30" height="10%" style="table-layout:fixed ">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="padding-top:10px;" width="175">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="center" style="padding-left:5px; padding-right:5px; "><?php steps(1); ?></td>
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
function selectGrade($teamid,$error,$errormsg,$newgrade){
?>
<form name="selectgrade" action="matchselectgrade.php" method="post">
<input type="hidden" name="gradeaction">
<input type="hidden" name="cont_1">
<table cellpadding="0" cellspacing="0" width="100%" border="0" id="standard">
	<tr>
		<td style=" padding-left:20px; padding-bottom:10px; padding-top:20px; " colspan="2">Select which grade the match was played in from the drop list below.</td>
	</tr>
	<tr>
		<?php $grade = dbToArray("call getGrades('where teamid=".$teamid."')"); ?>
		<td style=" padding-left:20px; padding-bottom:10px; padding-top:10px;"><?php print_dropDown("gradeid", "Select grade", $grade, $_SESSION['m']['gradeid']); ?></td>
		<td rowspan="2"><?php 
		if($error==1){
			validation($errormsg); 
		}
		else {
			echo "&nbsp";
		}
		?></td>	
	</tr>
	<tr>
		<td style=" padding-left:20px; padding-bottom:20px; padding-top:20px;" colspan="2"><b>OR</b></td>
	</tr>
	<tr>
		<td style=" padding-left:20px; padding-bottom:10px; padding-top:0px;" colspan="2">Enter a new grade</td>
	</tr>
	<tr>
		<td colspan="2"><?php newGrade($teamid,$newgrade); ?></td>
	</tr>
	<tr>
		<td style="padding-left:20px; padding-top:80px; ">
		<table cellpadding="0" cellspacing="0" id="whitetext">
			<tr>
				<td><input type="button" value="Next >>" onClick="Next();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold; height:35px;"></td>
				<td>&nbsp;|&nbsp;<a href="matchselectcomp.php">Back</a></td>
				<td>&nbsp;|&nbsp;<a href="#" onClick="Exit();">Exit</a></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
<?php
}

function newGrade($teamid,$newgrade){
?>
<table cellpadding="0" cellspacing="0" border="0" width="80%">
	<tr>
		<td style="padding-left:20px; padding-bottom:30px; padding-top:10px; color:#FFFFFF; font-size:18px;">Grade name</td>
		<td style="padding-bottom:20px; "><?php print_textBox("newgrade", $newgrade, "size='50' maxlength='50'"); ?></td>
	</tr>
</table>
<?php
}

function set_variables() {
	$_REQUEST['gradeid'] 	= set_param('gradeid');
	$_REQUEST['cont_1'] 		= set_param('cont_1');
	$_SESSION['m']['gradeid'] 	= set_session_param('gradeid');
	$_SESSION['m']['cont_1'] 		= set_session_param('cont_1');
	$_SESSION['m']['gradeid'] 	= $_REQUEST['gradeid'];
	$_SESSION['m']['cont_1'] 		= $_REQUEST['cont_1'];
}

function check_form_fields(){
	$error->general = 0;
	$error->message = '';
	if(($_REQUEST['gradeid']=="Select grade") && ($_REQUEST['newgrade']=="")){
		$error->general = 1;
		$error->message = "Please select a grade from the drop down list, OR enter a new grade.";
	}
	else if(($_REQUEST['gradeid']<>"Select grade") && ($_REQUEST['newgrade']<>"")){
		$error->general = 1;
		$error->message = "Please select either a grade from the drop down list, OR enter a new grade, not both.";				
	}	
	return $error;
}
?>
<script language="javascript">

function Next(){
	frm = document.selectgrade;
	frm.gradeaction.value="next";
	frm.cont_1.value=1;
	frm.submit();
}
function Exit(){
	ans = confirm("Are you sure you want to exit? Match details have not been completed.");
	if (ans == true) {
		frm = document.selectgrade;
		frm.gradeaction.value="exit";
		frm.submit();
	}
}
</script>