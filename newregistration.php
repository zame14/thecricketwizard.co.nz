<?php
session_start();
/********************
Includes
********************/
include_once("inc/db_functions.php");
include_once("inc/form_lib.php");
include_once("inc/cricket_lib.php");
include_once("inc/emails.php");
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
$user = getUserDetails($_REQUEST['id']);
if(isset($_REQUEST['uk'])){
	if($_REQUEST['uk']<>$user->uniquekey) {
		redirect_rel('index.php');
	}
}
//print_r($_REQUEST);
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="enter"){
		//automaically log new user in.
		$sessionid = session_id();
		$login = dbToArray("call UserLogon_auto(".$user->userid.",'".$sessionid."');");
		$_SESSION['userid'] = $_REQUEST['id'];
		$_SESSION['sessionid']=session_id();
		redirect_rel('home.php');
	}
}
?>
<form name="thefrm" action="newregistration.php" method="post">
<input type="hidden" name="action">
<input type="hidden" name="id" value="<?php echo $user->userid; ?>">
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td style="padding-top:5px; padding-bottom:5px; ">
		<table cellpadding="0" cellspacing="0" width="90%" align="center" bgcolor="#404040" height="100%" border="0" id="main">
			<tr>
				<td valign="top" style="padding-top:10px; padding-left:10px; "><img src="images/headingloggedout.jpg"></td>
			</tr>
			<tr>
				<td align="center" valign="top"><div class="line"> &nbsp;</div></td>
			</tr>
			<tr>
				<td style="padding-bottom:350px; padding-top:50px;">
				<table cellpadding="10" cellspacing="0" border="0" bgcolor="#212121" id="whiteborder" width="60%" align="center">
					<tr>
						<td colspan="2" style="font-size:22px;"><strong>Your registration was successful!</strong></td>
					</tr>
					<tr>
						<td colspan="2" style="padding-bottom:10px; font-size:18px; ">
						<?php
						echo "Hi ".$user->firstname.",<br><br>
						Thank you for registering your team with The Cricket Wizard. <br>
						The login details for your new Team Administrator account have been emailed to you.<br><br>
						
						As the Team Administrator you are responsible for:<br>
						&bull; Setting up your team roster,<br>
						&bull; Managing your team roster,<br>
						&bull; Submitting match results, and<br>
						&bull; Submitting individual player performances<br>
						<br>
						Players batting, bowling and fielding statstics will be automatically calculated from the results you enter.
						<br><br>
						To enter The Cricket Wizard and start managing your cricket team, click the Enter site button.";
						?>
						</td>
					</tr>
					<tr>
					<tr>
						<td align="center">
						<input type="button" value="Enter site" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="Enter();">
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td align="center" style="padding-bottom:10px; padding-top:5px; " colspan="3"><?php footer(1); ?></td>
			</tr>	
		</table>
		</td>
	</tr>
</table>
</form>
</body>
</html>
<script language="javascript">
function Enter(){
	frm = document.thefrm;
	frm.action.value="enter";
	frm.submit();
}
</script>