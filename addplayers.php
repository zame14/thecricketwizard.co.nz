<?php
session_start();
/********************
Includes
********************/
include_once("inc/db_functions.php");
include_once("inc/form_lib.php");
include_once("inc/cricket_lib.php");
include_once("inc/emails.php");
validate_usersession();
printcss();
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
/********************
Validation
********************/
//print_r($_SESSION);
$user = getUserDetails($_SESSION['userid']);
$colour = "#85C226";
$text = "Team Roster";
if($user->role != "Team Admin"){
	redirect_rel('home.php');
}
//Team admin must have email address before adding new players.
if($user->email==""){
	redirect_rel("editplayerprofile.php?id=".$_SESSION['userid']."");
}
$error=0;
$errormsg = '';
$send=0;
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="add"){
		$errorobj = check_form_fields();
		$error = $errorobj->general;
		$errormsg = $errorobj->message;
		if($error==0){
			$newuserid = 0;
			$roleid = 2;
			//Need a unique insert id number, so we know what players were inserted at the same time.
			$uin = generate_randomKey(10);
			for($i=1; $i<=11; $i++){
				if($_REQUEST['firstname'.$i]<>""){
					//$password = generate_randomKey(6);
					$tpassword = dbToArray("call getTeamPassword(".$user->teamid.");");
					$logonname = generate_logonName($_REQUEST["firstname".$i], $_REQUEST["lastname".$i]);
					$sql="call insertUpdateUser(".$newuserid.",'".addslashes($_REQUEST['firstname'.$i])."','".addslashes($_REQUEST['lastname'.$i])."','','".$tpassword[1]['teampassword']."',".$roleid.",'".$logonname."','".$uin."');";
					$result =dbToArray($sql);
						
					$sql2="call insertPlayerTeam(".$result[1][1]['userid'].",".$user->teamid.");";
					$result2 =dbToArray($sql2);			
					
					$send=1;
				}
			}
			if($send==1){
				//send email to Team Admin with login details for all the new players added.
				$player = dbToArray("call getPlayers('where uin=\"".$uin."\"','','');");
				emails("addplayers",$user->email,$user->firstname,$user->teamname,'',$tpassword[1]['teampassword'],$player);
				redirect_rel('process.php?id=1&np=teamroster.php');
			}
			else {
				$error=1;
				$errormsg = "Please enter the name of your new player.";
			}
		}
	}
}
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px; ">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" border="0" height="800" id="main">
			<tr height="50">
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
						<td rowspan="2" valign="top" bgcolor="#212121"; width="900" height="100%" id="mainfrm">
						<form name="addplayer" action="addplayers.php" method="post">
						<input type="hidden" name="action">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="left" valign="middle" style="font-size:24px; padding-left:20px; padding-top:10px;" id="whitetext" colspan="3"><strong>Add new player(s)</strong></td>
							</tr>
							<tr>
								<td colspan="2" id="whitetext" style="font-size:18px; padding-bottom:10px; padding-left:20px; padding-top:20px;">
								To add players to your team roster, enter their names below and click Submit.  You can add one to eleven players at a time.<br><br>
								Note: Your Team Administrator account is automatically added to your team roster.
								</td>
							</tr>
							<tr>
								<td style="padding-left:15px; padding-right:15px; padding-top:10px;" align="center"><?php addPlayers($error,$errormsg); ?></td>
							</tr>
							<tr>
								<td colspan="2" valign="bottom" align="center" style="padding-bottom:10px; padding-top:10px;"><input type="button" value="Submit" onClick="Add();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">&nbsp;&nbsp;<input type="button" value="Back" onClick="javascript:document.location='teamroster.php'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
							</tr>
						</table>
						</form>
						</td>
						<td bgcolor="<?php echo $colour; ?>" width="30" height="10%" style="table-layout:fixed ">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="padding-top:10px;">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="center"><?php mainmenu2(); ?></td>
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
				<td colspan="2" align="center" style="padding-bottom:10px; padding-top:20px; "><?php footer(); ?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
<?php
function addPlayers($error,$errormsg){
if($error==1){?>
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="padding-bottom:20px; "><?php validation($errormsg); ?></td>
	</tr>
</table>
<?php } ?>
<table cellpadding="5" cellspacing="0" border="1" width="80%" id="teamroster">
	<tr>
		<td>&nbsp;</td>
		<td>Firstname<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td width="300" style="padding-left:15px; ">Lastname<font size="+1" color="#C80101";><sup>*</sup></font></td>
	</tr>
	<?php
	for($i=1; $i<=11; $i++){ 
		if($error==1){
			$firstname = $_REQUEST['firstname'.$i];
			$lastname = $_REQUEST['lastname'.$i];
		}
		else {
			$firstname = '';
			$lastname = '';
		}
	?>
	<tr>
		<td style="padding-bottom:10px; "><?php echo $i; ?></td>
		<td style="padding-bottom:10px;"><?php print_textBox("firstname".$i, $firstname, "size='40' maxlength='40'"); ?></td>
		<td style="padding-bottom:10px; padding-left:15px;"><?php print_textBox("lastname".$i, $lastname, "size='40' maxlength='40'"); ?></td>
	</tr>
	<?php } ?>
</table>
<script language="javascript">
function Add(){
	frm = document.addplayer;
	frm.action.value="add";
	frm.submit();
}
</script>
<?php
}
function check_form_fields(){
	$error->general = 0;
	$error->message = '';
	for($i=1; $i<=11; $i++){
		if($_REQUEST['firstname'.$i]<>"" && $_REQUEST['lastname'.$i]==""){
			$error->general = 1; 
			$error->message = "Please enter both firstname and lastname when entering a new player.";
			return $error;	
		}
		if($_REQUEST['firstname'.$i]=="" && $_REQUEST['lastname'.$i]<>""){
			$error->general = 1; 
			$error->message = "Please enter both firstname and lastname when entering a new player.";
			return $error;	
		}
		if($i==11 && $error->general==0){
			return $error;
		}
	}
}	
?>