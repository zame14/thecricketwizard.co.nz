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
<script type="text/javascript" src="inc/jquery-1.3.2.min.js">
</script>
<script type="text/javascript" src="inc/jquery.tablednd_0_5.js">
</script>
<script type="text/javascript">
$(document).ready(function() {

	// Initialise the first table (as before)
	$("#table-1").tableDnD();

	// Make a nice striped effect on the table
	
	$("#fixtures tr:even').addClass('alt')");

	// Initialise the second table specifying a dragClass and an onDrop function that will display an alert
	$("#fixtures").tableDnD({
	    onDragClass: "myDragClass",
	    onDrop: function(table, row) {
            var rows = table.tBodies[0].rows;
            var debugStr = "Row dropped was "+row.id+". New order: ";
            for (var i=0; i<rows.length; i++) {
                debugStr += rows[i].id+" ";
            }
	        //$(#debugArea).html(debugStr);
	    },
		onDragStart: function(table, row) {
			//$(#debugArea).html("Started dragging row "+row.id);
		}
	}); 
});
</script>
<body>
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
$sql = "call getSelectedPlayers(".$user->teamid.",1)";
$result = dbToArray($sql);
if(isset($_SESSION['m']['cont_7'])){
	if($_SESSION['m']['cont_7'] <> 1){
		set_variables($result);
	}
}
else {
	set_variables($result);
}
if(isset($_REQUEST['cont_7'])){
	set_variables($result);
}
$error=0;
$errormsg = '';

/**********************************
Processing
**********************************/
if(isset($_REQUEST['actionsubmit'])){
	if($_REQUEST['actionsubmit']=="true"){
		$errorobj = check_form_fields($result,$_SESSION['m']['mt']);
		$error = $errorobj->general;
		$errormsg = $errorobj->message;
		if($error==0){
			redirect_rel('processnewmatch.php');
		}
	}
	else if($_REQUEST['actionsubmit']=="exit"){
		unsetSessions();
		redirect_rel('matches.php');		
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
						<td rowspan="2" valign="top" bgcolor="#212121"; width="900" height="100%">
						<table cellpadding="0" cellspacing="0" width="100%" border="0" id="mainfrm" height="100%">
							<tr>
								<td valign="top" style="padding-left:20px; padding-right:20px;" colspan="2">
								<?php 
								if($_SESSION['m']['mt']==1){
									performances1($user->teamid, $result,$error,$errormsg,1); 
								}
								else if($_SESSION['m']['mt']==2){
									performances2($user->teamid, $result,$error,$errormsg,2); 
								}
								?>
								</td>
							</tr>
							<tr>
								<td style="padding-left:20px; padding-top:40px; padding-bottom:20px; ">
								<table cellpadding="0" cellspacing="0" id="whitetext">
									<tr>
										<td><input type="button" value="Submit" onClick="SubmitForm();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold; height:35px;"></td>
										<td>&nbsp;|&nbsp;<a href="matchperformancesbowling.php">Back</a></td>
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
								<td align="center" style="padding-left:5px; padding-right:5px; "><?php steps(7); ?></td>
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
function performances1($teamid, $result,$error,$errormsg,$mtid){
$count = count($result);
?>
<form name="submitmatch" action="matchperformancesfielding.php" method="post">
<input type="hidden" name="actionsubmit">
<input type="hidden" name="cont_7">
<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td style="font-size:24px; padding-top:10px;" id="whitetext"><strong><?php echo "Enter fielding performances"; ?></strong></td>
	</tr>
	<tr>
		<td style="padding-top:10px; padding-bottom:10px; font-size:18px;" id="whitetext">
		To change the order, click and drag the players name to the position you want the player in.
		</td>
	</tr>
	<?php
	if($error==1){?>
	<tr>
		<td style="padding-bottom:20px; "><?php validation($errormsg); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" width="100%" border="1" id="fixtures">
			<tr>
				<td>&nbsp;</td>
				<td>Catches</td>
				<td>Stumpings</td>
				<td>Byes</td>			
			</tr>	
			<?php
			//$dismissal = dbToArray("call getDismissal()");		
			for($i=1; $i<=$count; $i++){ 
			//$name = "playerid".$i;
			?>			
			<tr>
				<td><?php echo stripslashes($result[$i]['player']); ?></td>
				<td>
				<?php
				$catches = array();
				$catches[1]['catches']='';$catches[2]['catches']=1;$catches[3]['catches']=2;$catches[4]['catches']=3;
				$catches[5]['catches']=4;$catches[6]['catches']=5;$catches[7]['catches']=6;$catches[8]['catches']=7;
				$catches[9]['catches']=8;$catches[10]['catches']=9;$catches[11]['catches']=10;
				print_dropDown("catches".$i,$_SESSION['m']['catches'.$i], $catches, "size='15'"); 
				?>		
				</td>
				<td>
				<?php 
				$stumpings = array();
				$stumpings[1]['stumpings']='';$stumpings[2]['stumpings']=1;$stumpings[3]['stumpings']=2;$stumpings[4]['stumpings']=3;
				$stumpings[5]['stumpings']=4;$stumpings[6]['stumpings']=5;$stumpings[7]['stumpings']=6;$stumpings[8]['stumpings']=7;
				$stumpings[9]['stumpings']=8;$stumpings[10]['stumpings']=9;$stumpings[11]['stumpings']=10;
				print_dropDown("stumpings".$i, $_SESSION['m']['stumpings'.$i], $stumpings, "size='15'"); 
				?>	
				</td>
				<td><?php print_textBox("byes".$i, $_SESSION['m']['byes'.$i], "size='4' maxlength='4'"); ?></td>
			</tr>	
			<?php } ?>
		</table>			
		</td>
	</tr>
</table>
</form>
<?php
}
function performances2($teamid, $result,$error,$errormsg,$mtid){
$count = count($result);
if(isset($_REQUEST['actionsubmit'])){
	if($_REQUEST['actionsubmit']=="true"){
		$errorobj = check_form_fields($result,$mtid);
		$error = $errorobj->general;
		$errormsg = $errorobj->message;
		if($error==0){
			redirect_rel('processnewmatch.php');
		}
	}
	else if($_REQUEST['actionsubmit']=="exit"){
		redirect_rel('home.php');
		unsetSessions();
	}
}
?>
<form name="submitmatch" action="matchperformancesfielding.php" method="post">
<input type="hidden" name="actionsubmit">
<input type="hidden" name="cont_7">
<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td style="font-size:24px; padding-top:10px;" id="whitetext"><strong><?php echo "Enter fielding performances"; ?></strong></td>
	</tr>
	<tr>
		<td style="padding-top:10px; padding-bottom:10px; font-size:18px;" id="whitetext">
		To change the order, click and drag the players name to the position you want the player in.
		</td>
	</tr>
	<?php
	if($error==1){?>
	<tr>
		<td style="padding-bottom:20px; "><?php validation($errormsg); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" width="100%" border="1" id="fixtures">
			<tr class="nodrop nodrag">
				<td colspan="4"><strong>First Innings</strong></td>
			</tr>		
			<tr class="nodrop nodrag">
				<td>&nbsp;</td>
				<td>Catches</td>
				<td>Stumpings</td>
				<td>Byes</td>			
			</tr>	
			<?php
			//$dismissal = dbToArray("call getDismissal()");		
			for($i=1; $i<=$count; $i++){ 
			//$name = "playerid".$i;
			?>			
			<tr>
				<td><?php echo stripslashes($result[$i]['player']); ?></td>
				<td>
				<?php
				$catches = array();
				$catches[1]['catches']='';$catches[2]['catches']=1;$catches[3]['catches']=2;$catches[4]['catches']=3;
				$catches[5]['catches']=4;$catches[6]['catches']=5;$catches[7]['catches']=6;$catches[8]['catches']=7;
				$catches[9]['catches']=8;$catches[10]['catches']=9;$catches[11]['catches']=10;
				print_dropDown("catches".$i,$_SESSION['m']['catches'.$i], $catches, "size='15'"); 
				?>		
				</td>
				<td>
				<?php 
				$stumpings = array();
				$stumpings[1]['stumpings']='';$stumpings[2]['stumpings']=1;$stumpings[3]['stumpings']=2;$stumpings[4]['stumpings']=3;
				$stumpings[5]['stumpings']=4;$stumpings[6]['stumpings']=5;$stumpings[7]['stumpings']=6;$stumpings[8]['stumpings']=7;
				$stumpings[9]['stumpings']=8;$stumpings[10]['stumpings']=9;$stumpings[11]['stumpings']=10;
				print_dropDown("stumpings".$i, $_SESSION['m']['stumpings'.$i], $stumpings, "size='15'"); 
				?>	
				</td>
				<td><?php print_textBox("byes".$i, $_SESSION['m']['byes'.$i], "size='4' maxlength='4'"); ?></td>
			</tr>	
			<?php } ?>
			<tr class="nodrop nodrag">
				<td colspan="4"><strong>Second Innings</strong></td>
			</tr>
			<tr class="nodrop nodrag">
				<td>&nbsp;</td>
				<td>Catches</td>
				<td>Stumpings</td>
				<td>Byes</td>			
			</tr>	
			<?php
			//$dismissal = dbToArray("call getDismissal()");		
			for($i=1; $i<=$count; $i++){ 
			//$name = "playerid".$i;
			?>			
			<tr>
				<td><?php echo stripslashes($result[$i]['player']); ?></td>
				<td>
				<?php
				$catches = array();
				$catches[1]['catches2']='';$catches[2]['catches2']=1;$catches[3]['catches2']=2;$catches[4]['catches2']=3;
				$catches[5]['catches2']=4;$catches[6]['catches2']=5;$catches[7]['catches2']=6;$catches[8]['catches2']=7;
				$catches[9]['catches2']=8;$catches[10]['catches2']=9;$catches[11]['catches2']=10;
				print_dropDown("catches2".$i,$_SESSION['m']['catches2'.$i], $catches, "size='15'"); 
				?>		
				</td>
				<td>
				<?php 
				$stumpings = array();
				$stumpings[1]['stumpings2']='';$stumpings[2]['stumpings2']=1;$stumpings[3]['stumpings2']=2;$stumpings[4]['stumpings2']=3;
				$stumpings[5]['stumpings2']=4;$stumpings[6]['stumpings2']=5;$stumpings[7]['stumpings2']=6;$stumpings[8]['stumpings2']=7;
				$stumpings[9]['stumpings2']=8;$stumpings[10]['stumpings2']=9;$stumpings[11]['stumpings2']=10;
				print_dropDown("stumpings2".$i, $_SESSION['m']['stumpings2'.$i], $stumpings, "size='15'"); 
				?>	
				</td>
				<td><?php print_textBox("byes2".$i, $_SESSION['m']['byes2'.$i], "size='4' maxlength='4'"); ?></td>
			</tr>	
			<?php } ?>
		</table>			
		</td>
	</tr>
</table>
</form>
<?php
}
function set_variables($result) {
	$count = count($result);
	for($i=1; $i<=$count; $i++){
	$_REQUEST['catches'.$i] 		= set_param('catches'.$i);
	$_SESSION['m']['catches'.$i] 		= set_session_param('catches'.$i);
	$_SESSION['m']['catches'.$i] 		= $_REQUEST['catches'.$i];
	$_REQUEST['stumpings'.$i] 		= set_param('stumpings'.$i);
	$_SESSION['m']['stumpings'.$i] 		= set_session_param('stumpings'.$i);
	$_SESSION['m']['stumpings'.$i] 		= $_REQUEST['stumpings'.$i];
	$_REQUEST['byes'.$i] 		= set_param('byes'.$i);
	$_SESSION['m']['byes'.$i] 		= set_session_param('byes'.$i);
	$_SESSION['m']['byes'.$i] 		= $_REQUEST['byes'.$i];
	$_REQUEST['catches2'.$i] 		= set_param('catches2'.$i);
	$_SESSION['m']['catches2'.$i] 		= set_session_param('catches2'.$i);
	$_SESSION['m']['catches2'.$i] 		= $_REQUEST['catches2'.$i];
	$_REQUEST['stumpings2'.$i] 		= set_param('stumpings2'.$i);
	$_SESSION['m']['stumpings2'.$i] 		= set_session_param('stumpings2'.$i);
	$_SESSION['m']['stumpings2'.$i] 		= $_REQUEST['stumpings2'.$i];
	$_REQUEST['byes2'.$i] 		= set_param('byes2'.$i);
	$_SESSION['m']['byes2'.$i] 		= set_session_param('byes2'.$i);
	$_SESSION['m']['byes2'.$i] 		= $_REQUEST['byes2'.$i];
	$_REQUEST['cont_7'] 		= set_param('cont_7');
	$_SESSION['m']['cont_7'] 		= set_session_param('cont_7');
	$_SESSION['m']['cont_7'] 		= $_REQUEST['cont_7'];
	}
}

function check_form_fields($result,$mtid){
	$count = count($result);
	$error->general = 0;
	$error->message = '';
	for($i=1; $i<=$count; $i++){
		//user has entered byes
		if($_REQUEST['byes'.$i]<>""){
			//check that byes entered are valid
			if(!is_numeric($_REQUEST['byes'.$i])){
				$error->general = 1; 
				$error->message = "The byes for ".$result[$i]['player']." are not valid, please check.";
				return $error;	
			}
		}
		if($mtid==2){
			//user has entered byes
			if($_REQUEST['byes2'.$i]<>""){
				//check that byes entered are valid
				if(!is_numeric($_REQUEST['byes2'.$i])){
					$error->general = 1; 
					$error->message = "The byes for ".$result[$i]['player']." in the second innings are not valid, please check.";
					return $error;	
				}
			}	
		}
		if($i==$count && $error->general==0){
			return $error;
		}
	}
}				
?>
<script language="javascript">
function SubmitForm(){
	frm = document.submitmatch;
	frm.actionsubmit.value="true";
	frm.cont_7.value=1;
	frm.submit();
}
function Exit(){
	ans = confirm("Are you sure you want to exit? Match details have not been completed.");
	if (ans == true) {
		frm = document.submitmatch
		frm.actionsubmit.value="exit";
		frm.submit();
	}
}
</script>