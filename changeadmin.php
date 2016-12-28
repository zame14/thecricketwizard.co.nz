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
validate_usersession();
//print_r($_REQUEST);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>The Cricket Wizard</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php
$user = getUserDetails($_SESSION['userid']);
if($user->role != "Team Admin"){
	redirect_rel('home.php');
}
$players = dbToArray("call getPlayers('where teamid = ".$user->teamid."','','');");
$error = 0;
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="submit"){
		$errorobj = check_form_fields();
		$error = $errorobj->general;
		if($error==0){
			//update accounts
			$sql="call updateRoles(".$user->userid.",'".$_REQUEST['pid']."');";
			$result = dbToArray($sql);
			$update = dbToArray("call updateProfileId(".$user->userid.",".$_REQUEST['pid'].");");
			//log user out
			redirect_rel('process.php?id=1&np=index.php');
		}
	}
}
?>
<form name="thefrm" action="changeadmin.php" method="post">
<input type="hidden" name="action">
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td style="padding-top:5px; padding-bottom:5px; ">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" height="100%" id="main" border="0">
			<tr height="50">
				<td valign="top" style="padding-top:10px; padding-left:10px;"><img src="images/headingloggedout.jpg"></td>
			</tr>
			<tr>
				<td align="center" valign="top"><div class="line"> &nbsp;</div></td>
			</tr>
			<tr>
				<td style="padding-bottom:350px; padding-top:50px;">
				<?php 
				if(count($players)>1){
					//players available
					NewTeamAdmin($user,$error); 
				}
				else {
					NoPlayers();
				}
				?>
				</td>
			</tr>
			<tr>
				<td colspan="3" align="center" style="padding-bottom:10px; padding-top:20px; "><?php footer(); ?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
<script language="javascript">
function SubmitMe(){
	frm = document.thefrm;
	frm.action.value="submit";
	frm.submit();
}
</script>
</body>
</html>
<?php
function NewTeamAdmin($user,$error){ 
?>
<table cellpadding="10" cellspacing="0" border="0" bgcolor="#212121" id="whiteborder" width="60%" align="center">
	<tr>
		<td colspan="2" style="font-size:22px;"><strong>Team Administrator</strong></td>
	</tr>
	<?php
	if($error==1){?>
	<tr>
		<td><?php validation("Please select a player from the drop down list below to be your teams new Team Administrator.");?></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td colspan="2" style="padding-bottom:10px; font-size:18px; ">
		<?php
		echo "Selecting a new Team Administrator means you will no longer be able to insert new fixtures, add new players etc.
		Only the new Team Administrator that you are selecting will be able to do this.<br><br>When you click the submit button you will be automatically
		logged out of the system and the next time you log in you will have a player's access only.";  
		echo "<br><br>Select an existing user from the drop down list below to be the new Team Administrator for the ".$user->teamname." cricket team.";		
		?>
		</td>
	</tr>
	<tr>
		<td>
		<?php
		$select = dbToArray("call getPlayersForStats(".$user->teamid.",".$user->userid.",'profile')"); 
		print_dropDown("pid", "Select Player", $select, $user->userid,'style= "width:250;"');
		?>		
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center" style="padding-top:50px; ">
		<input type="button" value="Submit" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="SubmitMe();">&nbsp;&nbsp;
		<input type="button" value="Cancel" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="javascript:document.location='teamroster.php'">
		</td>
	</tr>
</table>
<?php
}
function NoPlayers(){ 
?>
<table cellpadding="10" cellspacing="0" border="0" bgcolor="#212121" id="whiteborder" width="60%" align="center">
	<tr>
		<td colspan="2" style="font-size:22px;"><strong>Sorry</strong></td>
	</tr>
	<tr>
		<td colspan="2" style="padding-bottom:10px; font-size:18px; ">
		There are no other players available to select as the new Team Administrator.<br>
		Please click <a href="addplayers.php">here</a> to add players to your team roster, then you will be able to reassign the Team Administrator role.
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center" style="padding-top:50px; ">
		<input type="button" value="Cancel" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;" onClick="javascript:document.location='teamroster.php'">
		</td>
	</tr>
</table>
<?php
}

function check_form_fields(){
	$error->general = 0;
	//user must enter both names
	if($_REQUEST['pid']=="Select Player"){
		$error->general = 1; 
		return $error;	
	}
}	
?>