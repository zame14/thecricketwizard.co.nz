<?php
/********************
Includes
********************/
include_once("inc/db_functions.php");
include_once("inc/form_lib.php");
include_once("inc/cricket_lib.php");
session_start();
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
//print_r($_REQUEST);
/********************
Validation
********************/
$user = getUserDetails($_SESSION['userid']);
$colour = "#85C226";
$text = "Team Roster";
if(isset($_REQUEST['id']) && $user->role<>"Team Admin"){
	if($_REQUEST['id']<>$_SESSION['userid']){
		redirect_rel('home.php');	
	}
}

if(isset($_REQUEST['id']) || $_REQUEST['id'] <> ""){
	$playerid = $_REQUEST['id'];
	$sql = "call getPlayerProfile(".$playerid.")";
	$result = dbToArray($sql);
}
else {
	redirect_rel('playerprofile.php');
}

//need to check if this is Team Admin editing his/her profile.
//if it is need to make sure Team Admin has an email address.
$teamadminid = dbToArray("call getPlayers('WHERE roleid=1 AND teamid=".$user->teamid."','','');");
if(($playerid==$teamadminid[1]["userid"])&&$user->email==""){
	enter_email($user);
}

$error = 0;
$errornum = 0;
$erroremail = 0;
$errormsg = '';
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="edit"){
		$errorobj = check_form_fields($playerid,$teamadminid);
		$error = $errorobj->general;
		$errornum = $errorobj->numerror;
		$erroremail = $errorobj->emailerror;
		$errormsg = $errorobj->message;
		
		if($error==0 && $errornum==0 && $erroremail==0){
			if(!is_numeric($_REQUEST['playingroleid'])){
				$pr = dbToArray("call getPlayingRoles('WHERE playingRole=\'".$_REQUEST['playingroleid']."\'');");
				$_REQUEST['playingroleid'] = $pr[1]['playingroleid'];
			}
			if(!is_numeric($_REQUEST['batstyleid'])){
				$bt = dbToArray("call getBattingStyles('WHERE batstyle=\'".$_REQUEST['batstyleid']."\'');");
				$_REQUEST['batstyleid'] = $bt[1]['batstyleid'];
			}
			if(!is_numeric($_REQUEST['bowlstyleid'])){
				$bs = dbToArray("call getBowlingStyles('WHERE bowlstyle=\'".$_REQUEST['bowlstyleid']."\'');");
				$_REQUEST['bowlstyleid'] = $bs[1]['bowlstyleid'];
			}
			if($_REQUEST['playingroleid']==""){
				$_REQUEST['playingroleid'] = 0;
			}
			if($_REQUEST['batstyleid']==""){
				$_REQUEST['batstyleid'] = 0;
			}
			if($_REQUEST['bowlstyleid']==""){
				$_REQUEST['bowlstyleid'] = 0;
			}
			$sql3 = "call updatePlayerProfile(".$playerid.", '".addslashes($_REQUEST['firstname'])."', '".addslashes($_REQUEST['lastname'])."', '".addslashes($_REQUEST['nickname'])."',
			'".$_REQUEST['playernum']."','".$_REQUEST['playingroleid']."','".$_REQUEST['batstyleid']."','".$_REQUEST['bowlstyleid']."', '".addslashes($_REQUEST['email'])."')";
			$result3 = dbToArray($sql3);
			
			$sql4 = "call updatePersonalDetails(".$playerid.", '".$_REQUEST['address1']."', '".$_REQUEST['address2']."', '".$_REQUEST['suburb']."',
												'".$_REQUEST['city']."', '".$_REQUEST['phone']."', '".$_REQUEST['cellphone']."');";
			$result4 = dbToArray($sql4);		
				
			redirect_rel("process.php?id=1&np=playerprofile.php?pid=".$playerid."");
		}
	}
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
						<td rowspan="2" valign="top" bgcolor="#212121"; width="900" id="mainfrm" height="100%">
						<table cellpadding="0" cellspacing="0" width="100%" border="0" >
							<tr>
								<td align="left" valign="middle" style="font-size:24px; padding-left:20px; padding-top:10px;" id="whitetext" colspan="3"><strong>Edit profile</strong></td>
							</tr>
							<?php
							if($_SESSION['userid']==$playerid){
								$name = "your";
								$his = "your";
							}
							else {					
								$name = $result[1]['firstname']."'s";
								$his = "his";												
							}
							?>
							<tr>
								<td colspan="2" id="whitetext" style="font-size:18px; padding-bottom:10px; padding-left:20px; padding-top:10px;"><?php echo "To edit ".$name." profile, enter ".$his." details below and click Update.";?></td>
							</tr>
							<tr>
								<td style="padding-left:15px; padding-right:15px;" valign="top"><?php editProfile($result,$playerid,$error,$errornum,$erroremail,$errormsg,$user,$teamadminid); ?></td>
							</tr>
						</table>
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
function editProfile($result,$playerid,$error,$errornum,$erroremail,$errormsg,$user,$teamadminid){
if($error==1 || $errornum==1 || $erroremail==1){
	$result[1]['firstname'] = $_REQUEST['firstname'];
	$result[1]['lastname'] = $_REQUEST['lastname'];
	$result[1]['address1'] = $_REQUEST['address1'];
	$result[1]['address2'] = $_REQUEST['address2'];
	$result[1]['nickname'] = $_REQUEST['nickname'];
	$result[1]['suburb'] = $_REQUEST['suburb'];
	$result[1]['playernum'] = $_REQUEST['playernum'];
	$result[1]['city'] = $_REQUEST['city'];
	$result[1]['playingrole'] = $_REQUEST['playingroleid'];
	$result[1]['email'] = $_REQUEST['email'];
	$result[1]['batstyle'] = $_REQUEST['batstyleid'];
	$result[1]['phone'] = $_REQUEST['phone'];
	$result[1]['bowlstyle'] = $_REQUEST['bowlstyleid'];
	$result[1]['cellphone'] = $_REQUEST['cellphone'];
	if($_REQUEST['playingroleid']<>"" && $_REQUEST['playingroleid']>0) { 
		$pr = dbToArray("call getPlayingRoles('WHERE playingroleid=\'".$_REQUEST['playingroleid']."\'');");
		$result[1]['playingrole'] = $pr[1]["playingrole"];
	}
	if($_REQUEST['batstyleid']<>"" && $_REQUEST['batstyleid']>0) { 
		$bt = dbToArray("call getBattingStyles('WHERE batstyleid=\'".$_REQUEST['batstyleid']."\'');");
		$result[1]['batstyle'] = $bt[1]["batstyle"];
	}
	if($_REQUEST['bowlstyleid']<>"" && $_REQUEST['bowlstyleid']>0) { 
		$bs = dbToArray("call getBowlingStyles('WHERE bowlstyleid=\'".$_REQUEST['bowlstyleid']."\'');");
		$result[1]['bowlstyle'] = $bs[1]["bowlstyle"];
	}
}
?>
<form name="thefrm" action="editplayerprofile.php?id=<?php echo $playerid ?>" method="post">
<input type="hidden" name="action">
<input type="hidden" name="id">
<?php
if($error==1 || $errornum==1 || $erroremail==1){?>
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="padding-bottom:20px; "><?php validation($errormsg); ?></td>
	</tr>
</table>
<?php } ?>
<table cellpadding="5" cellspacing="0" border="1" width="100%" id="teamroster">
	<tr>
		<td style="padding-left:10px; padding-top:10px; padding-bottom:10px;" width="20%">First name<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td><?php print_textBox("firstname", $result[1]['firstname'], "size='40' maxlength='32'",$error);?></td>
	</tr>
	<tr>
		<td style="padding-left:10px; padding-top:10px; padding-bottom:10px;">Last name<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td><?php print_textBox("lastname", stripslashes($result[1]['lastname']), "size='40' maxlength='32'",$error);?></td>
	<tr>
		<td style="padding-left:10px; padding-bottom:10px; padding-top:10px;">Nickname</td>
		<td><?php print_textBox("nickname", $result[1]['nickname'], "size='40' maxlength='32'");?></td>
	</tr>
	<tr>
		<td style="padding-left:10px; padding-bottom:10px; padding-top:10px;">Player number</td>
		<td>
		<?php print_textBox("playernum", $result[1]['playernum'], "size='3' maxlength='3'"); 
		if($errornum==1){ echo "<script language='javascript'>document.thefrm.playernum.style.backgroundColor='#FFCCCC';</script>"; }
		?></td>
	</tr>		
	<tr>
		<?php 
		if(!isset($_REQUEST["playingroleid"])){
			($result[1]['playingroleid']>0) ? $playingroleid=$result[1]['playingroleid'] : $playingroleid=0;
		}
		else {
			($_REQUEST["playingroleid"]>0) ? $playingroleid = $_REQUEST["playingroleid"] : $playingroleid =0;
		}
		$playingrole = dbToArray("call getPlayingRoles('WHERE playingroleid<>".$playingroleid."');"); ?>
		<td style="padding-left:10px; padding-bottom:10px; padding-top:10px;">Playing role</td>
		<td><?php 
		if($result[1]['playingrole']<>""){
			print_dropDown("playingroleid", $result[1]['playingrole'], $playingrole,$result[1]['playingrole']);
		}
		else {
			print_dropDown("playingroleid", " ", $playingrole,'');
		} 
		?></td>
	</tr>
	<tr>
		<?php 
		if(!isset($_REQUEST["batstyleid"])){
			($result[1]['batstyleid']>0) ? $batstyleid=$result[1]['batstyleid'] : $batstyleid=0;
		}
		else {
			($_REQUEST["batstyleid"]>0) ? $batstyleid = $_REQUEST["batstyleid"] : $batstyleid =0;
		}
		$batstyle = dbToArray("call getBattingStyles('WHERE batstyleid<>".$batstyleid."');"); ?>
		<td style="padding-left:10px; padding-bottom:10px; padding-top:10px;">Batting style</td>
		<td><?php 
		if($result[1]['batstyle']<>""){
			print_dropDown("batstyleid",$result[1]['batstyle'], $batstyle, $result[1]['batstyle']); 
		}
		else {
			print_dropDown("batstyleid", " ", $batstyle, ''); 
		}
		?></td>
	</tr>
	<tr>
		<?php 
		if(!isset($_REQUEST["bowlstyleid"])){
			($result[1]['bowlstyleid']>0) ? $bowlstyleid=$result[1]['bowlstyleid'] : $bowlstyleid=0;
		}
		else {
			($_REQUEST["bowlstyleid"]>0) ? $bowlstyleid = $_REQUEST["bowlstyleid"] : $bowlstyleid =0;
		}
		$bowlstyle = dbToArray("call getBowlingStyles('WHERE bowlstyleid<>".$bowlstyleid."');"); ?>
		<td style="padding-left:10px; padding-bottom:10px; padding-top:10px;">Bowling style</td>
		<td><?php 
		if($result[1]['bowlstyle']<>""){
			
			print_dropDown("bowlstyleid", $result[1]['bowlstyle'], $bowlstyle, $result[1]['bowlstyle']); 
		}
		else {
			print_dropDown("bowlstyleid", " ", $bowlstyle, ''); 
		}
		?></td>
	</tr>
	<tr>
		<td colspan="2" style="padding-left:10px; padding-bottom:10px; padding-top:10px;" bgcolor="#000000"><strong>Personal Details</strong></td>
	</tr>
	<tr>
		<td style="padding-left:10px; padding-top:10px; padding-bottom:10px;" rowspan="2" valign="top">Address</td>
		<td><?php print_textBox("address1", $result[1]['address1'], "size='40' maxlength='32'");?></td>	
	</tr>
	<tr>
		<td><?php print_textBox("address2", $result[1]['address2'], "size='40' maxlength='32'");?></td>
	</tr>
	<tr>
		<td style="padding-left:10px; padding-top:10px; padding-bottom:10px;">Suburb</td>
		<td><?php print_textBox("suburb", $result[1]['suburb'], "size='40' maxlength='32'");?></td>			
	</tr>
	<tr>
		<td style="padding-left:10px; padding-top:10px; padding-bottom:10px;">City</td>
		<td><?php print_textBox("city", $result[1]['city'], "size='40' maxlength='32'");?></td>	
	</tr>
	<tr>
		<td style="padding-left:10px; padding-top:10px; padding-bottom:10px;">Email<?php if($playerid==$teamadminid[1]["userid"]){ echo "<font size='+1' color='#C80101';><sup>*</sup></font>"; } ?></td>
		<td><?php print_textBox("email", $result[1]['email'], "size='40' maxlength='32'");
		if($erroremail==1){ echo "<script language='javascript'>document.thefrm.email.style.backgroundColor='#FFCCCC';</script>"; }
		?></td>
	</tr>
	<tr>
		<td style="padding-left:10px; padding-top:10px; padding-bottom:10px;">Home phone</td>
		<td><?php print_textBox("phone", $result[1]['phone'], "size='40' maxlength='32'");?></td>	
	</tr>
	<tr>	
		<td style="padding-left:10px; padding-top:10px; padding-bottom:10px;">Mobile</td>
		<td><?php print_textBox("cellphone", $result[1]['cellphone'], "size='40' maxlength='32'");?></td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td colspan="2" align="center" style="padding-top:50px; "><input type="button" value="&nbsp;&nbsp;Update&nbsp;&nbsp;" onClick="UpdateProfile(<?php echo $playerid; ?>);" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">&nbsp;&nbsp;<input type="button" value="Cancel" onClick="javascript:document.location='playerprofile.php?id=<?php echo $playerid; ?>'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
	</tr>
</table>
</form>
<?php
}

function enter_email($user){
?>
<table cellpadding="5" cellspacing="0" border="0" id="loginfailed" bgcolor="#CCCCCC" width="100%">
	<tr>
		<td style="padding-left:120px; padding-right:100px;">
		<?php
		echo "Hi ".$user->firstname.", as a new Team Administrator you need to enter a valid email address before you
		can add new players to your team roster."; 
		?>
		</td>
	</tr>
</table>
<?php
}

function check_form_fields($playerid,$teamadminid){
	$error->general = 0;
	$error->numerror = 0;
	$error->emailerror = 0;
	$error->message = '';
	if($_REQUEST['firstname']=="" || $_REQUEST['lastname']==""){ 
		$error->general = 1;
		$error->message = "Please fill in all required fields.";
	}
	if($teamadminid[1]["userid"]==$playerid){
		if($_REQUEST['email']==""){
			$error->general = 1;
		}
	}
	if($_REQUEST['playernum']<>""){
		if(!is_numeric($_REQUEST['playernum'])){
			$error->numerror = 1;
			$error->message = "Please enter a valid player number.";
		}
	}
	if($_REQUEST['email']<>""){
		if(!validate_email($_REQUEST['email'])){
			$error->emailerror = 1;
			$error->message = "Please enter a valid email address.";
		}
	}
	return $error;
}
?>
<script language="javascript">
function UpdateProfile(theid){
	frm = document.thefrm;
	frm.action.value = "edit";
	frm.id.value=theid;
	frm.submit();
}
</script>