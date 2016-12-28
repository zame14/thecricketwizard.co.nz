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
/********************
Validation
********************/
$user = getUserDetails($_SESSION['userid']);
$colour = "#E8795E";
$text = "Statistics";
$inserterror = 1;
$error=0;
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="add"){
		$errorobj = check_form_fields();
		$error = $errorobj->general;
		if($error==0){
			$insertmilestones = dbToArray("call insertMilestone(".$user->teamid.",".$_REQUEST['compid'].",'".$_REQUEST['season']."');");
			$inserterror = $insertmilestones[1]['status'];
			if($inserterror==1){
				redirect_rel('process.php?id=3&np=milestones.php');
			}
		}		
	}
	else if($_REQUEST['action']=="remove"){
		$remove = dbToArray("call deleteMilestones(".$_REQUEST['id'].");");
		redirect_rel('process.php?id=3&np=milestones.php');	
	}
}
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px; ">
		<table cellpadding="0" cellspacing="0" width="1024" align="center" bgcolor="#404040" border="0" height="800" id="main" style="table-layout:fixed;">
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
						<td rowspan="2" valign="top" bgcolor="#212121"; width="900" height="100%" id="mainfrm">
						<form name="thefrm" action="milestones.php" method="post">
						<input type="hidden" name="action">
						<input type="hidden" name="id">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td valign="top" align="left" valign="middle" style="font-size:24px; padding-left:20px; padding-top:10px; padding-bottom:10px;" id="whitetext" colspan="3"><strong><?php echo "Trophy Cabinet"; ?></strong></td>
							</tr>
							<?php
							if($user->role=="Team Admin"){ ?>
							<tr>
								<td style="padding-left:20px; padding-right:10px; font-size:18px;" id="whitetext" valign="top">
								Welcome to yout team's trophy cabinet.<br><br>
								<div id="goldtext">Add a new trophy</div>
								To add a trophy to your trophy cabinet, use the drop down lists below to select which competition your team won
								and which season, then click the Add button.<br><br>
								 <div id="goldtext">Removing a trophy</div>
								To remove a trophy, click the <img src="images/Cancel_small.png"> button. 
								</td>
							</tr>
							<tr>
								<td style=" padding-left:20px; padding-top:30px; padding-bottom:0px; " valign="top"><?php add_trophy($user,$error); ?></td>
							</tr>
							<?php 
							} 
							else {?>
							<tr>
								<td style="padding-left:20px; font-size:18px;" id="whitetext" valign="top">
								Below is a list of trophies your team has won.
								</td>
							</tr>
							<?php } ?>
							<tr>
								<td style="padding-left:20px; "><div class="line"> &nbsp;</div></td>
							</tr>
							<tr>
								<td id="greytext" style="padding-left:20px;" valign="top"><?php echo $user->teamname." cricket team"; ?></td>
							</tr>
							<tr>
								<td style="padding-left:20px; padding-top:10px;" valign="top"><?php milestones($user, $inserterror); ?></td>
							</tr>
							<tr>
								<td align="center" style="padding-top:150px;"><input type="button" value="Back" onClick="javascript:document.location='statshome.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
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
function milestones($user, $inserterror){
$m = dbToArray("call getMilestones(".$user->teamid.");");
?>
<table cellpadding="0" cellspacing="0" border="0" width="70%">
	<?php
	if(count($m)>0){
		if($inserterror==0){ ?>
		<tr>
			<td colspan="3" style="padding-bottom:20px; "><?php validation("Sorry, trophy already exists."); ?></td>
		</tr>
		<?php }
		for($i=1; $i<=count($m); $i++){?>
		<tr>
			<td width="70" style="padding-bottom:20px;"><img src="images/trophy.jpg"></td>
			<td id="goldtext" style="padding-bottom:20px;"><?php echo $m[$i]['season']." ".$m[$i]['competition']." CHAMPIONS"; ?></td>
			<td style="padding-bottom:20px;"><img src="images/Cancel.png" alt="Remove trophy" style="cursor:pointer" onClick="DeleteTrophy(<?php echo $m[$i]['mtid']; ?>);"></td>
		</tr>
		<?php
		}
	}
	else { ?>
		<tr>
			<td id="whitetext" style="font-size:18px;">Trophy cabinet is currently empty!</td>
		</tr>
	<?php
	}
	?>
</table>
<?php
}

function add_trophy($user,$error){ 
$comps = dbToArray("call getComps('where teamid=".$user->teamid."')");
$seasons = dbToArray("call getSeasons(".$user->teamid.");");
if($error==1){
	$compid = $_REQUEST['compid'];
	$season = $_REQUEST['season'];
}
else {
	$compid = '';
	$season = '';
}
?>
<table cellpadding="0" cellspacing="0" border="0" width="50%">
	<?php
	if($error==1){?>
	<tr>
		<td style="padding-bottom:20px;" colspan="3"><?php validation("Please select both the compeition and season.");?></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td><?php print_dropDown("compid", "Select competition", $comps, $compid); ?></td>
		<td><?php print_dropDown("season", "Select season", $seasons, $season); ?></td>
		<td><input type="button" value="Add" onClick="Add();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
	</tr>
</table>
<?php
}

function check_form_fields(){
	$error->general = 0;
	if($_REQUEST['compid']=="Select competition") $error->general = 1;
	if($_REQUEST['season']=="Select season") $error->general = 1;
	
	return $error;
}
?>
<script language="javascript">
function Add() {
	frm = document.thefrm;
	frm.action.value = "add";
	frm.submit();
}

function DeleteTrophy(theid){
	frm = document.thefrm;
	ans = confirm("Are you sure you want to remove this trophy from the system?");
	if (ans == true ) {
		frm.action.value = "remove";
		frm.id.value = theid;
		frm.submit();
	}	
}
</script>