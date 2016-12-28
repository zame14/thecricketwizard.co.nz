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
if($user->role != "Team Admin"){
	redirect_rel('home.php');
}
$colour = "#F7C200";
$text = "Fixtures";
$e1 = 0;
$e2 = 0;
$e3 = 0;
$e4 = 0;
$errormsg = '';
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="newcomp"){
		if($_REQUEST['newcomp']<>"" && isset($_REQUEST['matchtype'])){
			$_REQUEST['newcomp'] = str_replace("'","",$_REQUEST['newcomp']);
			//check if comp exists for this team.
			$check = dbToArray("call adminCheck(".$user->teamid.",'".$_REQUEST['newcomp']."','comp');");
			if($check[1]['status']==0){
				//add new comp and redirect back to view match page.
				$compid = 0;
				$sql="call insertComp(".$compid.", '".$_REQUEST['newcomp']."',".$user->teamid.",".$_REQUEST['matchtype'].");";
				$result =dbToArray($sql);
				redirect_rel("process.php?id=2&np=viewmatch.php?rid=".$_REQUEST['id']);	
			}
			else {
				//Need to display message that competition already exists.
				$e1 = 1;
				$errormsg = "The competition you entered already exists.";
			}
		}
		else {
			$e1 = 1;
			$errormsg = "Please fill in all required fields.";
		}			
	}
	else if($_REQUEST['action']=="newgrade"){
		if($_REQUEST['newgrade']<>""){
			$_REQUEST['newgrade'] = str_replace("'","",$_REQUEST['newgrade']);
			//check if grade already exists for this team
			$check = dbToArray("call adminCheck(".$user->teamid.",'".$_REQUEST['newgrade']."','grade');");
			if($check[1]['status']==0){		
				//add new grade and redirect back to view match page.
				$gradeid = 0;
				$sql="call insertGrade(".$gradeid.", '".$_REQUEST['newgrade']."',".$user->teamid.");";
				$result =dbToArray($sql);
				redirect_rel("process.php?id=2&np=viewmatch.php?rid=".$_REQUEST['id']);	
			}
			else {
				//Need to display message that grade already exists.
				$e2 = 1;
				$errormsg = "The grade you entered already exists.";
			}
		}
		else {
			$e2 = 1;
			$errormsg = "Please fill in all required fields.";		
		}
	}
	else if($_REQUEST['action']=="editcomp"){
		//check if comp already exists for this team
		$i = $_REQUEST['compid'];
		if($_REQUEST['comp'.$i]<>""){
			$_REQUEST['comp'.$i] = str_replace("'","",$_REQUEST['comp'.$i]);
			$check = dbToArray("call adminCheck(".$user->teamid.",'".$_REQUEST['comp'.$i]."','comp');");
			if($check[1]['status']==0){		
				//add new comp and redirect back to view match page.
				$update = dbToArray("call adminUpdate(".$_REQUEST['compid'].",'".$_REQUEST['comp'.$i]."','comp');");
				redirect_rel("process.php?id=2&np=viewmatch.php?rid=".$_REQUEST['id']);	
			}
			else {
				$e3 = 1;
				$errormsg = "The competition you entered already exists.";
			}
		}
		else {
			$e3 = 1;
			$errormsg = '';
		}
	}
	else if($_REQUEST['action']=="editgrade"){
		//check if grade already exists for this team
		$i = $_REQUEST['gradeid'];
		if($_REQUEST['grade'.$i]<>""){
			$_REQUEST['grade'.$i] = str_replace("'","",$_REQUEST['grade'.$i]);
			$check = dbToArray("call adminCheck(".$user->teamid.",'".$_REQUEST['grade'.$i]."','grade');");
			if($check[1]['status']==0){	
				//add new grade and redirect back to view match page.	
				$update = dbToArray("call adminUpdate(".$_REQUEST['gradeid'].",'".$_REQUEST['grade'.$i]."','grade');");
				redirect_rel("process.php?id=2&np=viewmatch.php?rid=".$_REQUEST['id']);	
			}
			else {
				$e4 = 1;
				$errormsg = "The grade you entered already exists.";
			}
		}
		else {
			$e4 = 1;
			$errormsg = '';
		}				
	}
	else if($_REQUEST['action']=="deletecomp"){
		$delete = dbToArray("call adminDelete(".$_REQUEST['compid'].", 'comp');");
		redirect_rel("process.php?id=2&np=viewmatch.php?rid=".$_REQUEST['id']);
	}
	else if($_REQUEST['action']=="deletegrade"){
		$delete = dbToArray("call adminDelete(".$_REQUEST['gradeid'].", 'grade');");
		redirect_rel("process.php?id=2&np=viewmatch.php?rid=".$_REQUEST['id']);
	}
	else {
		redirect_rel('matches.php');
	}
}
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px; ">
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
								<td align="left" valign="top" style="font-size:24px; padding-left:15px; padding-top:10px;" id="whitetext" colspan="3"><strong><?php echo "Edit match"; ?></strong></td>
							</tr>
							<tr>
								<td style="font-size:18px; padding-left:15px; padding-top:10px; padding-right:10px;" id="whitetext" colspan="3">
								<div id="goldtext">Enter a new competition or grade</div>
								To enter a new competition or grade, enter the name of the new competition or grade and click the <img src="images/Ok_small.png"> button.<br><br>
								<div id="goldtext">Edit an existing competition or grade</div>
								To edit an existing competition or grade, change the text and click the <img src="images/Write_small.png"> button.<br><br>
								<div id="goldtext">Delete a competition or grade</div>
								To delete a competition or grade, click the <img src="images/Cancel_small.png"> button.  <br>Note: you can only delete a competition or grade that
								has not been used in a match.
								</td>
							</tr>
							<tr>
								<td style="padding-left:15px; padding-right:15px; padding-top:20px; "><?php thefrm($user,$e1,$e2,$e3,$e4,$errormsg); ?></td>
							</tr>
							<tr>
								<td style="padding-top:20px; padding-bottom:20px;" align="center"><input type="button" value="Back" onClick="javascript:document.location='editmatch.php?id=<?php echo $_REQUEST['id'] ?>';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
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
function thefrm($user,$e1,$e2,$e3,$e4,$errormsg){
if($e1==1){
	$newcomp = $_REQUEST['newcomp'];
	$mt = '';
	if(isset($_REQUEST['matchtype']))$mt = $_REQUEST['matchtype'];
}
else {
	$newcomp = '';
	$mt = '';
}
?>
<form name="thefrm" action="admin.php" method="post">
<input type="hidden" name="action">
<input type="hidden" name="compid">
<input type="hidden" name="gradeid">
<input type="hidden" name="competition">
<input type="hidden" name="grade">
<input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>">
<table border="0" width="100%" cellpadding="0" cellspacing="0" id="fixtures">
	<tr>
		<td valign="top" style="padding-left:10px; " width="50%">
		<table border="0" width="100%" cellpadding="5" cellspacing="0" id="whitetext">
			<tr>
				<td style="font-size:18px;"><strong>Enter new competition</strong></td>
			</tr>
			<?php
			if($e1==1){ ?>
			<tr>
				<td><?php validation($errormsg); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td style="font-size:18px;" colspan="2">
				Competition name:<br>
				<?php print_textBox("newcomp", $newcomp, "size='40' maxlength='40'",$e1); ?>
				</td>
			</tr>
			<tr>
				<td valign="top" style="font-size:18px; padding-top:30px;">
				Choose the match format:<br>
				<?php 
				$type = dbToArray("call getMatchType()");
				print_radioGroup2('matchtype', $type, $mt,"onClick='game_type_value(this.value);'"); 
				?>				
				</td>
			</tr>
			<tr>
				<td><img src="images/Ok32.png" style="cursor:pointer;" alt="Submit" onClick="New('comp');"></td>
			</tr>
		</table>
		</td>
		<td height="100%" style="padding-left:5px; padding-right:5px; padding-top:5px; padding-bottom:5px;" align="center"><div class="line2"> &nbsp;</div></td>
		<td valign="top" style="padding-right:10px; " width="50%">
		<table border="0" width="100%" cellpadding="5" cellspacing="0" id="whitetext">
			<tr>
				<td colspan="2" style="font-size:18px;"><strong>Enter new grade</strong></td>
			</tr>
			<?php
			if($e2==1){ ?>
			<tr>
				<td><?php validation($errormsg); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td style="font-size:18px;">
				Grade name:<br>
				<?php print_textBox("newgrade", '', "size='40' maxlength='40'",$e2); ?>
				</td>
			</tr>
			<tr>
				<td><img src="images/Ok32.png" style="cursor:pointer;" alt="Submit" onClick="New('grade');"></td>
			</tr>
		</table>				
		</td>
	</tr>
	<tr>
		<td valign="top" style="padding-left:10px; padding-top:30px; padding-bottom:10px; " width="50%">
		<table border="0" width="100%" cellpadding="5" cellspacing="0" id="whitetext">
			<tr>
				<td colspan="3" style="font-size:18px;"><strong>Edit or delete competitions</strong></td>
			</tr>
			<?php
			if($e3==1 && $errormsg<>''){ ?>
			<tr>
				<td><?php validation($errormsg); ?></td>
			</tr>
			<?php }
			$comps = dbToArray("call getComps('where teamid = ".$user->teamid."')");
			for($i=1;$i<=count($comps);$i++){?>
			<tr>
				<td><?php print_textBox("comp".$comps[$i]['compid'], $comps[$i]['competition'], "size='40' maxlength='40'"); ?></td>
				<td><img src="images/Write.png" style="cursor:pointer;" alt="Edit" onClick="Edit(<?php echo $comps[$i]['compid'] ?>,'comp');"></td>
				<td>
				<?php
				$del = dbToArray("call adminCheckIfUsed(".$user->teamid.",'".$comps[$i]['competition']."', 'comp');");
				if($del[1]['status']==0){ ?> 
					<img src="images/Cancel.png" style="cursor:pointer;" alt="Delete" onClick="Delete(<?php echo $comps[$i]['compid'] ?>,'comp');">
				<?php } ?>
				</td>
			</tr>
			<?php 
			}
			?>		
		</table>
		</td>
		<td height="100%" style="padding-left:5px; padding-right:5px; padding-top:30px; padding-bottom:5px;" align="center"><div class="line2"> &nbsp;</div></td>
		<td valign="top" style="padding-right:10px; padding-top:30px; " width="50%">
		<table border="0" width="100%" cellpadding="5" cellspacing="0" id="whitetext">
			<tr>
				<td colspan="3" style="font-size:18px;"><strong>Edit or delete grades</strong></td>
			</tr>
			<?php
			if($e4==1 && $errormsg<>''){ ?>
			<tr>
				<td><?php validation($errormsg); ?></td>
			</tr>
			<?php }	
			$grades = dbToArray("call getGrades('where teamid = ".$user->teamid."')");
			for($i=1;$i<=count($grades);$i++){?>	
			<tr>
				<td><?php print_textBox("grade".$grades[$i]['gradeid'], $grades[$i]['grade'], "size='40' maxlength='40'"); ?></td>
				<td><img src="images/Write.png" style="cursor:pointer;" alt="Edit" onClick="Edit(<?php echo $grades[$i]['gradeid'] ?>,'grade');"></td>
				<td>
				<?php
				$del = dbToArray("call adminCheckIfUsed(".$user->teamid.",'".$grades[$i]['grade']."', 'grade');");
				if($del[1]['status']==0){ ?> 
					<img src="images/Cancel.png" style="cursor:pointer;" alt="Delete" onClick="Delete(<?php echo $grades[$i]['gradeid'] ?>,'grade');">
				<?php } ?>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
		</td>
	</tr>
</table>
</form>
<script language="javascript">
function New(id){
	frm = document.thefrm;
	if(id=="comp"){
		frm.action.value="newcomp";
	}
	else {
		frm.action.value="newgrade";
	}
	frm.submit();
}

function Edit(theid,id){
	frm = document.thefrm;
	if(id=="comp"){
		ans = confirm("Are you sure you want to edit this competition?");
		if (ans == true ) {
			frm.action.value="editcomp";
			frm.compid.value = theid;
			frm.submit();
		}
	}
	else {
		ans = confirm("Are you sure you want to edit this grade?");
		if (ans == true ) {
			frm.action.value="editgrade";
			frm.gradeid.value = theid;
			frm.submit();
		}
	}	
}

function Delete(theid,id){
	frm = document.thefrm;
	if(id=="comp"){
		ans = confirm("Are you sure you want to delete this competition?");
		if (ans == true ) {
			frm.action.value="deletecomp";
			frm.compid.value = theid;
			frm.submit();
		}
	}
	else {
		ans = confirm("Are you sure you want to delete this grade?");
		if (ans == true ) {	
			frm.action.value="deletegrade";
			frm.gradeid.value = theid;
			frm.submit();
		}
	}	
}
</script>
</body>
</html>
<?php
}

function print_radioGroup2($name, $arrList, $selected='', $htmlAttrib='', $vertical=true, $error=0, $errorMsg='',$particularError=0,$particularErrorMsg='',$return=false) {
	//print_array_debug('arrList', $arrList);
	$outputStr = "";
	foreach($arrList as $arr1){
		$text = current($arr1);
		//echo_nl('text', $text);
		$b = next($arr1);
		$value = '';
		if($b!==false){
			$value= $b;
		}else{
			$value = $text;
		}//end if
				
		//$outputStr .= ($error!=0 && $selected=="")? "<span class='inputError'>": "<span>";
		$outputStr .= "<table border='0' width='100%' id='whitetext'>";
		$outputStr .= "<tr>";
		$outputStr .= "<td width='90%' style='font-size:18px;'>";
		$outputStr .= "".$text;
		$outputStr .= "</td>";
		$outputStr .= "<td>";
		$outputStr .= "<input ";
		$outputStr .= " type='radio' name='" . $name . "' id='".$name."-id'";
		$outputStr .= " value='".$value."'";
					
		if($selected != ""){
			$outputStr .=  $value==$selected ? " checked ": "";
		}
		$outputStr .= $htmlAttrib . ">";	
		
		//if($vertical==true){
			//$outputStr .= "<br>";
		//}
		$outputStr .= "</input></td></tr></table></span>";
		

	}

	if ($particularError != 0) {
		$outputStr .= "<br>&nbsp;&nbsp;&nbsp;<span class=redText>" . ucfirst($particularErrorMsg) . "</span>";
	} 
	else if ($error != 0 && $selected=="") {
		$outputStr .=  "<br>&nbsp;&nbsp;&nbsp;<span class=redText>" . ucfirst($errorMsg) . "</span>";
	}
	if(!$return) echo $outputStr;
	else return $outputStr;
	
} // end print_radioGroup
?>