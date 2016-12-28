<?php
session_start();
/********************
Includes
********************/
include_once("inc/db_functions.php");
include_once("inc/form_lib.php");
include_once("inc/cricket_lib.php");
//validate_usersession();
printcss();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>The Cricket Wizard</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="inc/calendar.css">
</head>
<body>
<?php
/********************
Validation
********************/
unsetSessions();
(isset($_SESSION['userid']) && $_SESSION['userid']<>"") ? $redirect = "home.php" : $redirect = "index.php";
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
				<?php //loggedIn($user->firstname, $user->lastname, $user->teamname, $user->role); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top" align="center" style="padding-top:50px; "><?php error_message($redirect); ?></td>
			</tr>
			<tr>
			<!-- 
			4th table row
			includes footer.
			-->		
				<td colspan="2" align="center" style="padding-bottom:10px; padding-top:20px; "><?php //footer(); ?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
<?php
//Functions
function error_message($redirect){ ?>
<table cellpadding="20" cellspacing="0" border="0" id="validation" style="font-size:18px;">
	<tr>
		<td>
		<?php
		echo "Opps, something went wrong!<br /><br />
		An error has occured on the page you requested.<br />
		An email has been sent notifying us of the problem, and we will look into it asap.<br /><br />
		If you would like to contact us regarding this error, please email <a href='mailto:admin@thecricketwizard.co.nz' style=color:#FF0000;>admin@thecricketwizard.co.nz</a>.<br /><br />
		Thank you.<br /><br />
		Click <a href=".$redirect." style=color:#FF0000;>here</a> to continue.";
		?>
		</td>
	</tr>
</table>

<?php
}
?>