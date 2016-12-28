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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>The Cricket Wizard</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<script type="text/javascript" src="selectPlayers.js">
</script>
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
if(isset($_SESSION['m']['cont_3'])){
	$continue = 1;
	if($_SESSION['m']['cont_3'] <> 1){
		set_variables();
	}
}
else {
	$continue = 0;
	set_variables();
}
//print_r($_REQUEST);
if(isset($_REQUEST['cont_3'])){
	if($_REQUEST['playerids'] <> ""){
		set_variables();
	}
}
$error=0;
$error1=0;

/*********************
Processing
********************/
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="next"){
		$errorobj = check_form_fields();
		$error = $errorobj->general;
		if($error==0){
			dbToArray("call deleteSelectedPlayers(".$user->teamid.")");
			$ids = $_SESSION['m']['playerids'];
			$theplayer = explode(',',$ids);
			for($i=0; $i<sizeof($theplayer); $i++){
				dbToArray("call insertSelectedPlayers(".$theplayer[$i].",".$user->teamid.", ".$i.")");
			}
			redirect_rel('process.php?id=2&np=matchperformancesbatting.php');
		}
	}
	else if($_REQUEST['action']=="exit"){
		unsetSessions();
		redirect_rel('matches.php');
		
	}
	else if($_REQUEST['action']=="add"){
		$errorobj = check_form_fields1();
		$error1 = $errorobj->general;
		if($error1==0){
			$newuserid = 0;
			$roleid = 2;
			//Need a unique insert id number, so we know what players were inserted at the same time.
			$uin = generate_randomKey(10);
			$password = dbToArray("call getTeamPassword(".$user->teamid.");");
			$logonname = generate_logonName($_REQUEST["firstname"], $_REQUEST["lastname"]);
			$sql="call insertUpdateUser(".$newuserid.",'".addslashes($_REQUEST['firstname'])."','".addslashes($_REQUEST['lastname'])."','','".$password[1]['teampassword']."',".$roleid.",'".$logonname."','".$uin."');";
			$result =dbToArray($sql);
			
			$sql2="call insertPlayerTeam(".$result[1]['userid'].",".$user->teamid.");";
			$result2 =dbToArray($sql2);
			//send email to Team Admin with login details for all the new players added.
			$player = dbToArray("call getPlayers('where uin=\"".$uin."\"','','');");
			emails("addplayers",$user->email,$user->firstname,$user->teamname,'',$password[1]['teampassword'],$player);		
			redirect_rel('process.php?id=2&np=matchselectplayers.php');
		}
	}
	else if($_REQUEST['action']=="reselect"){
		$r = dbToArray("call updateRetiredStatus(".$_REQUEST['id'].",0);");
		redirect_rel('process.php?id=2&np=matchselectplayers.php');
	}
}
?>
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
								<td align="left" valign="middle" style="font-size:24px; padding-left:20px; padding-top:10px;" id="whitetext" colspan="3"><strong><?php echo "Team selection - ".$_SESSION['m']['teamname']. " vs ".$_SESSION['m']['opponent']; ?></strong></td>
							</tr>
							<tr>
								<td style="padding-left:20px; padding-right:10px; padding-top:10px; font-size:18px;" id="whitetext" colspan="2">
								Below is a list of players available to select your team from.<br>
								Check through the list and use the Add new players form on the right to add players that are missing from the list.
								Once all players are available you can select your team.<br><br>
								<div id="goldtext">Selecting players</div>
								To select a player, click on the players name and click the <img src="images/Arrow3 Right_small.png"> button.
								To deselect a player, click on the players name and click on the <img src="images/Arrow3 Left_small.png"> button.
								</td>
							</tr>
							<tr>
								<td valign="top" style="padding-left:20px; padding-right:5px; padding-top:20px;" width="600"><?php teamSelection($user->teamid, $continue, $error); ?></td>
								<td valign="top" align="right" style="padding-right:40px; padding-top:0px; "><?php addPlayer($user,$error1); ?></td>
							</tr>
							<tr>
								<td style="padding-left:20px; padding-top:40px; padding-bottom:20px; ">
								<table cellpadding="0" cellspacing="0" id="whitetext">
									<tr>
										<td><input type="button" value="Next >>" onClick="Next();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold; height:35px;"></td>
										<td>&nbsp;|&nbsp;<a href="matchdetails.php">Back</a></td>
										<td>&nbsp;|&nbsp;<a href="#" onClick="Exit();">Exit</a></td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						</td>
						<td bgcolor="<?php echo $colour; ?>" width="30" height="10%" style="table-layout:fixed ">&nbsp;</td>
					</tr>
					<tr>
						<td valign="top" style="padding-top:10px;" width="175">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="center" style="padding-left:5px; padding-right:5px; "><?php steps(3); ?></td>
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
function teamSelection($teamid,$continue,$error){
?>
<form name="teamselection" action="matchselectplayers.php" method="post">
<input type="hidden" name="action">
<input type="hidden" name="playerids">
<input type="hidden" name="clicked">
<input type="hidden" name="cont_3">
<input type="hidden" name="numplayers">
<table cellpadding="5" cellspacing="0" width="100%" border="1">
	<tr>
		<td><?php selectPlayers($teamid,$continue,$error); ?></td>
	</tr>		
</table>
</form>
<script language="javascript">
function Next(){
	frm = document.teamselection;
	frm.action.value="next";
	frm.cont_3.value=1;
	frm.numplayers.value = document.getElementById('s').value;
	frm.submit();
}
function Exit(){
	ans = confirm("Are you sure you want to exit? Match details have not been completed.");
	if (ans == true) {
		frm = document.teamselection;
		frm.action.value="exit";
		frm.submit();
	}
}
</script>
<?php
}

function selectPlayers($teamid,$continue,$error) {
?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" id="standard">
	<?php
	if($error==1){?>
	<tr>
		<td colspan="3" align="right"><?php validation("Please select players for your team."); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td style="padding-left:10px; padding-bottom:5px;">Available players</td>
		<td width="100">&nbsp;</td>
		<td style="padding-bottom:5px; padding-left:10px;">Selected players</td>
	</tr>
	<?php
	if($continue==0){ ?>
	<tr>
		<td width="200" style="padding-left:10px; padding-bottom:10px;">
		<?php
		$sql = "call getPlayers('where pt.teamid=".$teamid." and u.retired=0 and u.deleted=0','order by firstname, lastname','')";
		$result = dbToArray($sql);
		?>
		<select id="players" name="players" size="15">	
		<?php
		$count = count($result);
		for($i=1; $i<=$count; $i++){
		?>
		<option name="players" value="<?php echo $result[$i]['userid'];?>"><?php echo stripslashes($result[$i]['player']); ?></option>		
		<?php } ?>
		</select>
		</td>
		<td width="50">
		<img src="images/Arrow3 Right.png" onClick="moveSelectedOptions(document.getElementById('players'),document.getElementById('selectedplayers'),'select');"style="cursor:pointer;" alt="select"><br><br><img src="images/Arrow3 Left.png" onClick="moveSelectedOptions(document.getElementById('selectedplayers'),document.getElementById('players'),'drop');"style="cursor:pointer;" alt="deselect">
		</td>
		<td style="padding-bottom:10px; padding-left:10px;">
		<select id="selectedplayers" size="15" name="selectedplayers">		
		</select>
		</td>
	</tr>	
	<?php }
	else { ?>
	<tr>
		<td width="180" style="padding-left:10px; padding-bottom:10px;">
		<?php
		$sql = "call getSelectedPlayers(".$teamid.", 0)";
		$result = dbToArray($sql);
		?>
		<select id="players" name="players" size="15">	
		<?php
		$count = count($result);
		for($i=1; $i<=$count; $i++){
		?>
		<option name="players" value="<?php echo $result[$i]['userid'];?>"><?php echo stripslashes($result[$i]['player']); ?></option>		
		<?php } ?>
		</select>
		</td>
		<td width="50">
		<img src="images/Arrow3 Right.png" onClick="moveSelectedOptions(document.getElementById('players'),document.getElementById('selectedplayers'),'select');" style="cursor:pointer;" alt="select"><br><br><img src="images/Arrow3 Left.png" onClick="moveSelectedOptions(document.getElementById('selectedplayers'),document.getElementById('players'),'drop');"style="cursor:pointer;" alt="deselect">
		</td>
		<td style="padding-bottom:10px; padding-left:10px;">
		<?php
		$sql1 = "call getSelectedPlayers(".$teamid.", 1)";
		$result1 = dbToArray($sql1);
		?>
		<select id="selectedplayers" size="15" name="selectedplayers">		
		<?php
		$count = count($result1);
		for($i=1; $i<=$count; $i++){
		?>
		<option name="selectedplayers" value="<?php echo $result1[$i]['playerid'];?>"><?php echo stripslashes($result1[$i]['player']); ?></option>		
		<?php } ?>
		</select>
		</td>
	</tr>
	<?php } ?>	
	<tr>
		<?php
		if(isset($_SESSION['m']['numplayers'])){
			$n = $_SESSION['m']['numplayers'];
		}
		else {
			$n=0;
		} ?>
		<td align="right" colspan="3">Number of players selected:&nbsp;<input type="text" id="s" value="<?php echo $n; ?>" style="border-style:none; background-color:#212121; color:#FFFFFF; width:20px; "></td>
	</tr>		
</table>
<?php
}
function addPlayer($user,$error1){
?>
<table cellpadding="0" cellspacing="0" border="0" id="whitetext">
	<tr>
		<td style="padding-bottom:5px; ">Add a new player to the roster</td>
	</tr>
	<tr>
		<td>
		<form name="addplayer" action="matchselectplayers.php" method="post">
		<input type="hidden" name="action">
		<input type="hidden" name="id">
		<table cellpadding="5" cellspacing="0" border="0" id="addplayer" width="100%">
			<?php
			if($error1==1){?>
			<tr>
				<td><?php validation("Please fill in all required fields."); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td>
				<?php
				echo "Firstname<br>";
				print_textBox("firstname", "", "size='32' maxlength='32'",$error1);
				?>
				</td>
			</tr>
			<tr>
				<td>
				<?php
				echo "Lastname<br>";
				print_textBox("lastname", "", "size='32' maxlength='32'",$error1);
				?>
				</td>
			</tr>
			<tr>
				<td align="right"><input type="button" value="Add" onClick="Add();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding-bottom:5px; ">
		<strong>OR</strong><br><br>
		Select a player from the list of retired players.
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="whitetext">
			<?php
			$sql1 = "call getPlayers('where pt.teamid=".$user->teamid." and u.retired=1','order by firstname, lastname','')";
			$result1 = dbToArray($sql1);	
			$rows = count($result1);
			if(count($result1)<>0){
					//echo "here";			
				if(!isset($_GET['pagenum'])){
					$pagenum = 1;
				}
				else {
					$pagenum = $_GET['pagenum'];
				}		
				$page_rows = 5;
				$last = ceil($rows/$page_rows);			
				if ($pagenum < 1) { 
					$pagenum = 1; 
				} 
				elseif ($pagenum > $last) { 
					$pagenum = $last; 
				} 
				$max = 'limit ' .($pagenum - 1) * $page_rows .',' .$page_rows;
	
					$sql2 = "call getPlayers('where pt.teamid=".$user->teamid." and u.retired=1', 'order by firstname, lastname','".$max."')";
					$result2 = dbToArray($sql2);
				//}?>
				<tr>
					<td colspan="2" align="right">
					<?php
					if ($pagenum == 1){
						echo "&nbsp;";
					} 
					else {
					echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=1'><img src='images/Player FastRev16.png' style='cursor:pointer' border='0' alt='First'></a> ";
					echo " ";
					$previous = $pagenum-1;
					echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=$previous'><img src='images/Player Previous16.png' style='cursor:pointer' border='0' alt='Previous'></a> ";
					}		
					if ($pagenum == $last) {
					} 
					else {
					$next = $pagenum+1;
					echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=$next'><img src='images/Player Next16.png' style='cursor:pointer' border='0' alt='Next'></a> ";
					echo " ";
					echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=$last'><img src='images/Player FastFwd16.png' style='cursor:pointer' border='0' alt='Last'></a> ";
					} 		
					?>
					</td>
				</tr>			
				<?php
				if(count($result2)>=1){
					for($i=1;$i<=count($result2);$i++){
					?>	
					<tr>			
						<td style="padding-bottom:5px;" width="70%"><?php echo stripslashes($result2[$i]['player']);  ?></td>
						<td align="center" style="padding-bottom:5px;"><img src="images/Refresh.png" onClick="Reselect(<?php echo $result2[$i]['userid']; ?>);" style="cursor:pointer;" alt="Bring player out of retirement."></td>
					</tr>
	
					<?php
					}
					?>
					<tr>
						<td align="right" colspan="2"><?php echo "Page ".$pagenum." of ".$last; ?></td>
					</tr>						
				<?php
				}
			}
			else {	?>
				<tr>
					<td>No retired players to choose from.</td>
				</tr>	
			<?php } ?>	
		</table>
		</form>
		</td>
	</tr>
</table>
<script language="javascript">
function Add(){
	frm = document.addplayer;
	frm.action.value="add";
	frm.submit();
}
function Reselect(theid){
	frm = document.addplayer;
	frm.action.value="reselect";
	frm.id.value=theid;
	frm.submit();
}
</script>
<?php
}
function set_variables() {
	$_REQUEST['cont_3'] 		= set_param('cont_3');
	$_SESSION['m']['cont_3'] 		= set_session_param('cont_3');
	$_SESSION['m']['cont_3'] 		= $_REQUEST['cont_3'];
	$_REQUEST['playerids'] 		= set_param('playerids');
	$_SESSION['m']['playerids'] 		= set_session_param('playerids');
	$_SESSION['m']['playerids'] 		= $_REQUEST['playerids'];
	$_REQUEST['numplayers'] 	= set_param('numplayers');
	$_SESSION['m']['numplayers'] 		= set_session_param('numplayers');
	$_SESSION['m']['numplayers'] 		= $_REQUEST['numplayers'];
}

function check_form_fields(){
	$error->general = 0;
	$error->message = '';
	if(!isset($_REQUEST['numplayers']) || $_REQUEST['numplayers']==""){
		if($_REQUEST['playerids']=="") {$error->general = 1;}
	}
	return $error;
}

function check_form_fields1(){
	$error1->general = 0;
	$error1->message = '';
	if($_REQUEST['firstname']=="" || $_REQUEST['lastname']=="") $error1->general = 1;

	return $error1;
}
?>