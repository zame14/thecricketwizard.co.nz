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
<script language="javascript">
	var secs;
	var timerID = null;
	var timerRunning = false;
	var delay = 1000;
	var redirect = '';
function InitializeTimer(){
    // Set the length of the timer, in seconds
    secs = 1
    StopTheClock()
    StartTheTimer()
}
function StopTheClock(){
    if(timerRunning)
    clearTimeout(timerID)
    timerRunning = false
}
function StartTheTimer(){
    if (secs==0) {
        StopTheClock()
		redirect = document.getElementById('page');
		//alert(document.location=redirect.value);
		document.location=redirect.value;
    }
    else {
        self.status = secs
        secs = secs - 1
        timerRunning = true
        timerID = self.setTimeout("StartTheTimer()", delay)
    }
}
</script>
<body onLoad="InitializeTimer()">
<?php
/********************
Validation
********************/
$user = getUserDetails($_SESSION['userid']);
if($_REQUEST['id']==1){
	$colour = "#85C226";
}
else if($_REQUEST['id']==2){
	$colour = "#F7C200";
}
else if($_REQUEST['id']==3){
	$colour = "#E8795E";
}
else {
	$colour = "#00000";
}
?>
<input type="hidden" name="page" id="page" value="<?php echo $_REQUEST['np']; ?>">
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
						<td valign="middle" align="right" style="padding-right:50px; padding-top:5px;"><?php echo "Processing..."; ?></td>
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
						<table cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#FFFFFF"; height="100%">
							<tr>
								<td colspan="5" width="100%" valign="middle" align="center"><img src="images/wait.gif"></td>
							</tr>
						</table>
						</td>
						<td bgcolor="<?php echo $colour; ?>" width="30" height="10%" style="table-layout:fixed ">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="padding-top:10px;">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="center"><img src="images/wizard2.jpg"></td>
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
				<td colspan="2" align="center" style="padding-bottom:10px; padding-top:20px; ">&nbsp;</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
