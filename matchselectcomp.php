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
<script type="text/javascript" src="toolstips/wz_tooltip.js"></script>
<?php
/********************
Validation
********************/
$user = getUserDetails($_SESSION['userid']);
$colour = "#F7C200";
$text = "Fixtures";
if($user->role != "Team Admin"){
	redirect_rel('home.php');
}
//call this incase the table is not empty
if(!isset($_SESSION['m']['cont'])){
	$d = dbToArray("call deleteSelectedPlayers(".$user->teamid.")");
}

if($user->role<>"Team Admin"){
	redirect_rel('home.php');
}

if(isset($_SESSION['m']['cont'])){
	if($_SESSION['m']['cont'] <> 1){
		set_variables();
	}
}
else {
	set_variables();
}
if(isset($_REQUEST['cont'])){
	set_variables();
}
$error=0;
$errormsg='';

/************************************************
Processing
************************************************/
if(isset($_REQUEST['compaction'])){
	if($_REQUEST['compaction']=="next"){
		$errorobj = check_form_fields();
		$error = $errorobj->general;
		$errormsg = $errorobj->message;
		if($error==0) {
			if($_REQUEST['newcomp'] <> ""){
				//new comp needs to be added to database, but check that they are not trying to add an existing competition
				$check = dbToArray("call adminCheck(".$user->teamid.",'".$_REQUEST['newcomp']."','comp');");
				if($check[1]['status']==0){
					$compid = 0;
					$sql="call insertComp(".$compid.", '".addslashes($_REQUEST['newcomp'])."',".$user->teamid.",".$_REQUEST['mtid'].");";
					$result =dbToArray($sql);
					$_SESSION['m']['compid'] = $result[1]['competitionid'];			
				}
				else {
					$error=1;
					$errormsg="The new competition you entered already exists.";
				}
			}
			if($error==0) {
				redirect_rel('process.php?id=2&np=matchselectgrade.php');	
			}		
		}
	}
	else if($_REQUEST['compaction']=="exit"){
		unsetSessions();
		redirect_rel('matches.php');		
	}
}
if($error==1){
	$_REQUEST["newcomp"] = set_param("newcomp");
	$_REQUEST["mtid"] = set_param("mtid");
	$newcomp = $_REQUEST["newcomp"];
	$mtid = $_REQUEST["mtid"];
}
else {
	$newcomp="";
	$mtid="";
}
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
								<td align="left" valign="middle" style="font-size:24px; padding-left:25px; padding-top:10px;" id="whitetext" colspan="3"><strong><?php echo "Select competition"; ?></strong></td>
							</tr>
							<tr>
								<td colspan="5" width="100%" valign="top" style="padding-left:5px; padding-right:5px;"><?php selectCompetition($user->teamid,$error,$errormsg,$newcomp,$mtid); ?></td>
							</tr>
						</table>
						</td>
						<td bgcolor="<?php echo $colour; ?>" width="30" height="10%" style="table-layout:fixed ">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="padding-top:10px;" width="175">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="center" style="padding-left:5px; padding-right:5px; "><?php steps(0); ?></td>
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
function selectCompetition($teamid,$error,$errormsg,$newcomp,$mtid){
?>
<form name="selectcomp" action="matchselectcomp.php" method="post">
<input type="hidden" name="compaction">
<input type="hidden" name="mtid" id="mtid">
<input type="hidden" name="cont">
<table cellpadding="0" cellspacing="0" width="100%" border="0" id="standard">
	<tr>
		<td style=" padding-left:20px; padding-bottom:10px; padding-top:20px; " colspan="2">Select which competition the match was played in from the drop down list below.</td>
	</tr>
	<tr>
		<?php $comp = dbToArray("call getComps('where teamid=".$teamid."')"); ?>
		<td style=" padding-left:20px; padding-bottom:10px; padding-top:10px;"><?php print_dropDown("compid", "Select competition", $comp, $_SESSION['m']['compid']); ?></td>
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
		<td style=" padding-left:20px; padding-bottom:10px; padding-top:0px;" colspan="2">Enter a new competition&nbsp;&nbsp;
		<img src="images/Question.png" onMouseOver="Tip('Enter the name of the competition your team competes in.  For example: Senior Grade One Day, Hawke Cup, Prems Two Day.', BGCOLOR,'#ffffff', BORDERCOLOR,'#dddddd', DELAY,300, STICKY,false, CLOSEBTN,false, CLICKCLOSE,true, FOLLOWMOUSE, false, PADDING,16, SHADOW,true, SHADOWCOLOR,'#cccccc', SHADOWWIDTH,2, WIDTH,150, FIX, [this,-125,5]);" onMouseOut="UnTip();"></td>
	</tr>
	<tr>
		<td colspan="2"><?php newCompetition($teamid,$newcomp,$mtid,$errormsg); ?></td>
	</tr>
	<tr>
		<td style="padding-left:20px; padding-top:80px; ">
		<table cellpadding="0" cellspacing="0" id="whitetext">
			<tr>
				<td><input type="button" value="Next >>" onClick="Next();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold; height:35px;"></td>
				<td>&nbsp;|&nbsp;<a href="#" onClick="Exit();">Exit</a></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
<?php
}

function newCompetition($teamid,$newcomp,$mtid,$errormsg){
?>
<table cellpadding="0" cellspacing="0" border="0" width="80%">
	<tr>
		<td style="padding-left:20px; padding-bottom:10px; padding-top:10px; font-size:18px;" id="whitetext">Competition name</td>
		<td><?php 
		$e=0;
		if($errormsg=="Please enter the name of the new competition."){$e=1;}
		print_textBox("newcomp", $newcomp, "size='50' maxlength='50'",$e); ?></td>
	</tr>
	<tr>
		<td colspan="2" style="padding-left:20px; padding-bottom:10px; padding-top:10px; font-size:18px;" id="whitetext">Choose the match format (Only do this when you are entering a new competition)</td>
	</tr>
	<tr>
		<?php $type = dbToArray("call getMatchType()"); ?>
		<td colspan="2" style="padding-left:19px; padding-bottom:20px; padding-top:10px;"><?php print_radioGroup('matchtype', $type, $mtid,"onClick='game_type_value(this.value);'"); ?></td>
	</tr>
</table>
<?php
}

function set_variables() {
	$_REQUEST['compid'] 	= set_param('compid');
	$_REQUEST['cont'] 		= set_param('cont');
	$_SESSION['m']['compid'] 	= set_session_param('compid');
	$_SESSION['m']['cont'] 		= set_session_param('cont');
	$_SESSION['m']['compid'] 	= $_REQUEST['compid'];
	$_SESSION['m']['cont'] 		= $_REQUEST['cont'];
}

function check_form_fields(){
	//$error = 0;
	$error->general = 0;
	$error->message = '';
	if($_REQUEST['compid']=="Select competition"){
		if($_REQUEST['newcomp']=="" && $_REQUEST['mtid']==""){
			$error->general = 1;
			$error->message = "Please select a competition from the drop down list, OR enter a new competition.";
		}
		else if($_REQUEST['newcomp']<>"" && $_REQUEST['mtid']==""){
			$error->general = 1;
			$error->message = "Please choose the match format for the new competition.";		
		}
		else if($_REQUEST['newcomp']=="" && $_REQUEST['mtid']<>""){
			$error->general = 1;
			$error->message = "Please enter the name of the new competition.";		
		}
	}
	else {
		if($_REQUEST['newcomp']<>"" || $_REQUEST['mtid']<>""){
			$error->general = 1;
			$error->message = "Please select either a competition from the drop down list, OR enter a new competition, not both.";				
		}
	}	
	return $error;
}
?>
<script language="javascript">
function game_type_value(val){
	if(val==1){
		document.getElementById("mtid").value=1;
	}
	else if(val==2){
		document.getElementById("mtid").value=2;
	}
}
function Next(){
	frm = document.selectcomp;
	frm.compaction.value="next";
	frm.cont.value=1;
	frm.submit();
}

function Exit(){
	ans = confirm("Are you sure you want to exit? Match details have not been completed.");
	if (ans == true) {
		frm = document.selectcomp;
		frm.compaction.value="exit";
		frm.submit();
	}
}
</script>