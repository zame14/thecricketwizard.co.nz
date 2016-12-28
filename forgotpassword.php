<?php
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
$display = 0;
$error = 0;
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="submit"){
		$errorobj = check_form_fields();
		$error = $errorobj->general;
		if($error==0){
			$sql="call forgottenPassword('".$_REQUEST['team']."','".$_REQUEST['grade']."','".$_REQUEST['email']."');";
			$result = dbToArray($sql);
			if($result[1]['status']==1){
				$display=1;
				//create new password.
				$newpassword = generate_randomKey(8);
				//update password
				$change = dbToArray("call updatePassword(".$result[1]['userid'].",'".$newpassword."');");
				//send email
				emails("forgotten",$_REQUEST['email'],$result[1]['firstname'],"",$result[1]['username'],$newpassword,"");
			}
			else if($result[1]['status']==0){
				$display=2;
			}
		}
	}
}
?>
<form name="thefrm" action="forgotpassword.php" method="post">
<input type="hidden" name="action">
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td style="padding-top:5px; padding-bottom:5px; ">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" height="100%" border="0" id="main">
			<tr>
				<td valign="top" style="padding-top:10px; padding-left:10px; "><img src="images/headingloggedout.jpg"></td>
			</tr>
			<tr>
				<td align="center" valign="top"><div class="line"> &nbsp;</div></td>
			</tr>
			<tr>
				<td style="padding-bottom:350px; padding-top:50px;">
				<?php 
				if($display==0){
					EnterDetails($error); 
				}
				else if($display==1){
					DetailsFound($result[1]['firstname']);
				}
				else if($display==2){
					DetailsNotFound();
				}
				?>
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
function SubmitMe(){
	frm = document.thefrm;
	frm.action.value="submit";
	frm.submit();
}
</script>
<?php
function EnterDetails($error){ 
?>
<table cellpadding="10" cellspacing="0" border="0" bgcolor="#212121" id="whiteborder" width="60%" align="center">
	<tr>
		<td colspan="2" style="font-size:22px;"><strong>Forgotten username or password</strong></td>
	</tr>
	<tr>
		<td colspan="2" style="padding-bottom:10px; font-size:18px; ">
		Please select your team from the drop down list below, enter your registered email address in the appropriate box
		and click Submit.
		</td>
	</tr>
	<?php
	if($error==1){ ?>
	<tr>
		<td colspan="2"><?php validation("Please fill in all fields."); ?></td>
	</tr>
	<?php
	}
	?>
	<tr>
	<?php
	$teams = dbToArray("call getListOfTeams();");
	?>
		<td style="font-size:18px;">Select team</td>
		<td><?php print_dropDown("team", "Choose", $teams,'style= "width:250;"'); ?></td>
	</tr>
	<tr>
	<?php
	$grades = dbToArray("call getListOfGrades();");
	?>
		<td style="font-size:18px;">Select grade</td>
		<td><?php print_dropDown("grade", "Choose", $grades,'style= "width:250;"'); ?></td>
	</tr>
	<tr>
		<td width="20%" style="font-size:18px;">Email address</td>
		<td width="80%"><?php print_textBox("email", '', "size='35' maxlength='50'");?></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
		<input type="button" value="Submit" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="SubmitMe();">&nbsp;&nbsp;
		<input type="button" value="Cancel" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="javascript:document.location='index.php'">
		</td>
	</tr>
</table>
<?php
}

function DetailsFound($firstname){
?>
<table cellpadding="10" cellspacing="0" border="0" bgcolor="#212121" id="whiteborder" width="60%" align="center">
	<tr>
		<td colspan="2" style="font-size:22px;"><strong>Forgotten username or password</strong></td>
	</tr>
	<tr>
		<td colspan="2" style="padding-bottom:10px; font-size:18px; ">
		<?php
		echo "Hi ".$firstname.",<br> Welcome back to The Cricket Wizard. An email has been sent with your login details.<br><br>
		Click the login button to return to the login screen.";
		?>
		</td>
	</tr>
	<tr>
		<td align="center"><input type="button" value="Login" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="javascript:document.location='index.php'"></td>
	</tr>
</table>
<?php
}


function DetailsNotFound(){
?>
<table cellpadding="10" cellspacing="0" border="0" bgcolor="#212121" id="whiteborder" width="60%" align="center">
	<tr>
		<td colspan="2" style="font-size:22px;"><strong>Forgotten username or password</strong></td>
	</tr>
	<tr>
		<td colspan="2" style="padding-bottom:10px; font-size:18px; ">
		Sorry, you are not registered with The Cricket Wizard.<br><br>
		To register your team and receive a Team Administrator login account, click the Register now button.<br><br>
		To try again, click the Try again button.
		</td>
	</tr>
	<tr>
		<td align="center">
		<input type="button" value="Register now" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="javascript:document.location='index.php'">&nbsp;&nbsp;
		<input type="button" value="Try again" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="javascript:document.location='forgotpassword.php'">
		</td>
	</tr>
</table>
<?php
}
function check_form_fields(){
	$error->general = 0;
	if($_REQUEST['team']=="" || $_REQUEST['email']=="" || $_REQUEST['team']=="Choose" || $_REQUEST['grade']=="" || $_REQUEST['grade']=="Choose") {
		$error->general = 1;
	}
	return $error;
}
?>