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
<link rel="stylesheet" href="inc/calendar.css">
</head>
<script type="text/javascript" src="inc/calendar_eu.js"></script>
<script language="javascript">
var basefolder='<?php echo HOSTNAME; ?>';
</script>
<body>
<?php
/********************
Validation
********************/
//print_r($_REQUEST);
$user = getUserDetails($_SESSION['userid']);
$colour = "#F7C200";
$text = "Fixtures";

//Only team admins allowed to access this page.
if($user->role<>"Team Admin"){
	redirect_rel('matches.php');
}

if(isset($_REQUEST['rid'])){
	$_REQUEST['id'] = $_REQUEST['rid'];
}
if(!isset($_REQUEST['id'])){
	redirect_rel('matches.php');
}
$error=0;
$error1=0;
$errormsg='';
$action='';
if(isset($_REQUEST['action'])){
	$action = $_REQUEST['action'];
}
//queries
$sql = "call getMatch(".$_REQUEST['id'].");";
$result = dbToArray($sql);

$sql1 = "call getMatchPerformances('where p.matchid =".$_REQUEST['id']." and p.matchinnings=1', 'order by player.firstname, player.lastname');";
$result1 = dbToArray($sql1);

if($result[1]['mtid']==2){
	$sql2 = "call getMatchPerformances('where p.matchid =".$_REQUEST['id']." and p.matchinnings=2', 'order by player.firstname, player.lastname');";
	$result2 = dbToArray($sql2);
}
else {
	$result2='';
}


if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="save"){
		$errorobj = check_form_fields($result1,$result2,$result[1]['mtid']);
		$error = $errorobj->general;
		$error1 = $errorobj->details;
		$errormsg = $errorobj->message;
		
		if($error==0&&$error1==0){
			//update match
			$dbdate = dbDateDisplay($_REQUEST['date'],'Y-m-d');
			$competition = '"'.$_REQUEST['compid'].'"';
			$c = dbToArray("call getComps('where competition = ".$competition."');");		
			$grade = '"'.$_REQUEST['gradeid'].'"';
			$g = dbToArray("call getGrades('where grade = ".$grade."');");	
			$resultid = $_REQUEST['resultid'];
			if(!is_numeric($_REQUEST['resultid'])){
				$theresult = '"'.$_REQUEST['resultid'].'"';
				$r = dbToArray("call getResult('where result = ".$theresult."');");
				$resultid = $r[1]['resultid'];		
			}
			$sqlr = "call insertUpdateMatch(".$_REQUEST['id'].",0, '".addslashes($_REQUEST['opponent'])."', '".$c[1]['compid']."', '".addslashes($_REQUEST['venue'])."',
								'".$g[1]['gradeid']."', '".$dbdate."', '".$resultid."', '".addslashes($_REQUEST['summary'])."', '".addslashes($_REQUEST['season'])."');"; 
			$resultr =dbToArray($sqlr);	
			//update batting
			for($i=1;$i<=count($result1);$i++){	
				if($_REQUEST["battingid".$i]==""){ $_REQUEST["battingid".$i]=0; }
				if(isset($_REQUEST['runs'.$i]) && $_REQUEST['runs'.$i]<>""){
					$dismissalid = $_REQUEST['dismissal'.$i];
					if(!is_numeric($_REQUEST['dismissal'.$i])){
						$dismissal = '"'.$_REQUEST['dismissal'.$i].'"';
						$d = dbToArray("call getDismissal('where dismissal = ".$dismissal."');");
						$dismissalid = $d[1]['did'];
					}
					($_REQUEST['ballsfaced'.$i]=='' ) ? $_REQUEST['ballsfaced'.$i]='10101' : $_REQUEST['ballsfaced'.$i];
					($_REQUEST['boundaries'.$i]=='' ) ? $_REQUEST['boundaries'.$i]='10101' : $_REQUEST['boundaries'.$i];
					($_REQUEST['sixes'.$i]=='' ) ? $_REQUEST['sixes'.$i]='10101' : $_REQUEST['sixes'.$i];
					($result1[$i]['batorder']=='') ? $result1[$i]['batorder']=101 : $result1[$i]['batorder'];
					$sqlbat = "call insertUpdateBatting(".$_REQUEST['battingid'.$i].",".$_REQUEST['runs'.$i].",".$_REQUEST['ballsfaced'.$i].",'".$dismissalid."',".$_REQUEST['boundaries'.$i].",".$_REQUEST['sixes'.$i].",".$result1[$i]['batorder'].");";
					$resultbat =dbToArray($sqlbat);
					$_REQUEST['battingid'.$i] = $resultbat[1]['battingid'];		
					if($_REQUEST["runs".$i] >=100){
						//check to see if honours have already been inserted
						$check = dbToArray("call checkHonours(".$_REQUEST['battingid'.$i].",'bat');");
						if($check[1]['status']==0){
							//insert to honours
							$sqlh = "call insertHonoursPerformance(".$_REQUEST['playerid'.$i].",".$_REQUEST['id'].",'".$_REQUEST["runs".$i]."',0,0,".$_REQUEST['teamid'].",".$_REQUEST['dismissal'.$i].",".$_REQUEST['battingid'.$i].");";
							$resulth =dbToArray($sqlh);
						}
					}
				}
				//update batting second innings
				if($result[1]['mtid']==2){
					if($_REQUEST["battingid2".$i]==""){ $_REQUEST["battingid2".$i]=0; }
					if(isset($_REQUEST['runs2'.$i]) && $_REQUEST['runs2'.$i]<>""){
						$dismissalid = $_REQUEST['dismissal2'.$i];
						if(!is_numeric($_REQUEST['dismissal2'.$i])){
							$dismissal = '"'.$_REQUEST['dismissal2'.$i].'"';
							$d = dbToArray("call getDismissal('where dismissal = ".$dismissal."');");
							$dismissalid = $d[1]['did'];
						}
						($_REQUEST['ballsfaced2'.$i]=='' ) ? $_REQUEST['ballsfaced2'.$i]='10101' : $_REQUEST['ballsfaced2'.$i];
						($_REQUEST['boundaries2'.$i]=='' ) ? $_REQUEST['boundaries2'.$i]='10101' : $_REQUEST['boundaries2'.$i];
						($_REQUEST['sixes2'.$i]=='' ) ? $_REQUEST['sixes2'.$i]='10101' : $_REQUEST['sixes2'.$i];
						($result2[$i]['batorder']=='') ? $result2[$i]['batorder']=101 : $result2[$i]['batorder'];
						$sqlbat = "call insertUpdateBatting(".$_REQUEST['battingid2'.$i].",".$_REQUEST['runs2'.$i].",".$_REQUEST['ballsfaced2'.$i].",'".$dismissalid."',".$_REQUEST['boundaries2'.$i].",".$_REQUEST['sixes2'.$i].",".$result2[$i]['batorder'].");";
						$resultbat =dbToArray($sqlbat);
						$_REQUEST['battingid2'.$i] = $resultbat[1]['battingid'];		
						if($_REQUEST["runs2".$i] >=100){
							//check to see if honours have already been inserted
							$check = dbToArray("call checkHonours(".$_REQUEST['battingid2'.$i].",'bat');");
							if($check[1]['status']==0){
								//insert to honours
								$sqlh = "call insertHonoursPerformance(".$_REQUEST['playerid'.$i].",".$_REQUEST['id'].",'".$_REQUEST["runs2".$i]."',0,0,".$_REQUEST['teamid'].",".$_REQUEST['dismissal2'.$i].",".$_REQUEST['battingid2'.$i].");";
								$resulth =dbToArray($sqlh);
							}
						}
					}
				}
				//update bowling
				if($_REQUEST["bowlingid".$i]==""){ $_REQUEST["bowlingid".$i]=0; }
				if (isset($_REQUEST['overs'.$i]) && $_REQUEST['overs'.$i]<>""){
					($_REQUEST['maidens'.$i]=='' ) ? $_REQUEST['maidens'.$i]='10101' : $_REQUEST['maidens'.$i];
					($_REQUEST['wides'.$i]=='' ) ? $_REQUEST['wides'.$i]='10101' : $_REQUEST['wides'.$i];
					($_REQUEST['noballs'.$i]=='' ) ? $_REQUEST['noballs'.$i]='10101' : $_REQUEST['noballs'.$i];
					($result1[$i]['bowlorder']=='') ? $result1[$i]['bowlorder']=101 : $result1[$i]['bowlorder'];
					$sqlbowl = "call insertUpdateBowling(".$_REQUEST['bowlingid'.$i].",".$_REQUEST['maidens'.$i].",".$_REQUEST['runsconceded'.$i].",".$_REQUEST['wickets'.$i].",".$_REQUEST['wides'.$i].",".$_REQUEST['noballs'.$i].",".$_REQUEST['overs'.$i].",".$result1[$i]['bowlorder'].");";
					$resultbowl =dbToArray($sqlbowl);		
					$_REQUEST['bowlingid'.$i] = $resultbowl[1]['bowlingid'];		
					if($_REQUEST["wickets".$i] >= 6){
						//check to see if honours have already been inserted		
						$check = dbToArray("call checkHonours(".$_REQUEST['bowlingid'.$i].",'bowl');");
						if($check[1]['status']==0){
							//insert to honours
							$sqlh = "call insertHonoursPerformance(".$_REQUEST['playerid'.$i].",".$_REQUEST['id'].",0,".$_REQUEST["wickets".$i].",".$_REQUEST["runsconceded".$i].",".$_REQUEST['teamid'].",0,".$_REQUEST['bowlingid'].");";
							$resulth =dbToArray($sqlh);
						}
					}
				}
				//update bowling second innings
				if($result[1]['mtid']==2){
					if($_REQUEST["bowlingid2".$i]==""){ $_REQUEST["bowlingid2".$i]=0; }
					if (isset($_REQUEST['overs2'.$i]) && $_REQUEST['overs2'.$i]<>""){
						($_REQUEST['maidens2'.$i]=='' ) ? $_REQUEST['maidens2'.$i]='10101' : $_REQUEST['maidens2'.$i];
						($_REQUEST['wides2'.$i]=='' ) ? $_REQUEST['wides2'.$i]='10101' : $_REQUEST['wides2'.$i];
						($_REQUEST['noballs2'.$i]=='' ) ? $_REQUEST['noballs2'.$i]='10101' : $_REQUEST['noballs2'.$i];
						($result2[$i]['bowlorder']=='') ? $result2[$i]['bowlorder']=101 : $result2[$i]['bowlorder'];
						$sqlbowl = "call insertUpdateBowling(".$_REQUEST['bowlingid2'.$i].",".$_REQUEST['maidens2'.$i].",".$_REQUEST['runsconceded2'.$i].",".$_REQUEST['wickets2'.$i].",".$_REQUEST['wides2'.$i].",".$_REQUEST['noballs2'.$i].",".$_REQUEST['overs2'.$i].",".$result2[$i]['bowlorder'].");";
						$resultbowl =dbToArray($sqlbowl);		
						$_REQUEST['bowlingid2'.$i] = $resultbowl[1]['bowlingid'];		
						if($_REQUEST["wickets2".$i] >= 6){
							//check to see if honours have already been inserted		
							$check = dbToArray("call checkHonours(".$_REQUEST['bowlingid2'.$i].",'bowl');");
							if($check[1]['status']==0){
								//insert to honours
								$sqlh = "call insertHonoursPerformance(".$_REQUEST['playerid'.$i].",".$_REQUEST['id'].",0,".$_REQUEST["wickets2".$i].",".$_REQUEST["runsconceded2".$i].",".$_REQUEST['teamid'].",0,".$_REQUEST['bowlingid2'.$i].");";
								$resulth =dbToArray($sqlh);
							}
						}
					}
				}
				//update fielding
				if($_REQUEST["fieldingid".$i]==""){ $_REQUEST["fieldingid".$i]=0; }
				if((isset($_REQUEST["catches".$i])&&$_REQUEST["catches".$i]) || (isset($_REQUEST["stumpings".$i])&&$_REQUEST["stumpings".$i]<>"") || (isset($_REQUEST["byes".$i])&&$_REQUEST["byes".$i]<>"")){				
					($_REQUEST['catches'.$i]=='' ) ? $_REQUEST['catches'.$i]='0' : $_REQUEST['catches'.$i];
					($_REQUEST['stumpings'.$i]=='' ) ? $_REQUEST['stumpings'.$i]='0' : $_REQUEST['stumpings'.$i];
					($_REQUEST['byes'.$i]=='' ) ? $_REQUEST['byes'.$i]='0' : $_REQUEST['byes'.$i];		
					$sqlf =  "call insertUpdateFielding(".$_REQUEST['fieldingid'.$i].",".$_REQUEST['catches'.$i].",".$_REQUEST['stumpings'.$i].",".$_REQUEST["byes".$i].");";
					$resultf =dbToArray($sqlf);
					$_REQUEST['fieldingid'.$i] = $resultf[1]['fieldingid'];	
				}	
				//update fielding second innings
				if($result[1]['mtid']==2){
					if($_REQUEST["fieldingid2".$i]==""){ $_REQUEST["fieldingid2".$i]=0; }
					if((isset($_REQUEST["catches2".$i])&&$_REQUEST["catches2".$i]) || (isset($_REQUEST["stumpings2".$i])&&$_REQUEST["stumpings2".$i]<>"") || (isset($_REQUEST["byes2".$i])&&$_REQUEST["byes2".$i]<>"")){				
						($_REQUEST['catches2'.$i]=='' ) ? $_REQUEST['catches2'.$i]='0' : $_REQUEST['catches2'.$i];
						($_REQUEST['stumpings2'.$i]=='' ) ? $_REQUEST['stumpings2'.$i]='0' : $_REQUEST['stumpings2'.$i];
						($_REQUEST['byes2'.$i]=='' ) ? $_REQUEST['byes2'.$i]='0' : $_REQUEST['byes2'.$i];		
						$sqlf =  "call insertUpdateFielding(".$_REQUEST['fieldingid2'.$i].",".$_REQUEST['catches2'.$i].",".$_REQUEST['stumpings2'.$i].",".$_REQUEST["byes2".$i].");";
						$resultf =dbToArray($sqlf);
						$_REQUEST['fieldingid2'.$i] = $resultf[1]['fieldingid'];	
					}	
				}
				
				//update batting partnerships second innings
				//update performance table
				$sqlpf = "call insertUpdatePerformance(".$_REQUEST['performanceid'.$i].",".$_REQUEST['playerid'.$i].",".$_REQUEST['id'].",".$_REQUEST['battingid'.$i].",".$_REQUEST['bowlingid'.$i].",".$_REQUEST['fieldingid'.$i].",1);";
				$resultpf =dbToArray($sqlpf);
				
				$ten = dbToArray("call checkWickets10(".$_REQUEST['id'].",".$_REQUEST['playerid'.$i].");");		
				
				//update performance second innings
				if($result[1]['mtid']==2){
					$sqlpf = "call insertUpdatePerformance(".$_REQUEST['performanceid2'.$i].",".$_REQUEST['playerid'.$i].",".$_REQUEST['id'].",".$_REQUEST['battingid2'.$i].",".$_REQUEST['bowlingid2'.$i].",".$_REQUEST['fieldingid2'.$i].",2);";
					$resultpf =dbToArray($sqlpf);				
				}			
			}	
			for($n=1;$n<=10;$n++){
			//update batting partnerships
				//extra check to avoid error
				//if(!is_numeric($_REQUEST['batsmanid1_'.$n]) && $_REQUEST['batsmanid1_'.$n] <> "Select batsman 1"){
					//$_REQUEST['partnership'.$n] =10101;
				//}			
				(!is_numeric($_REQUEST['batsmanid1_'.$n])) ? $_REQUEST['batsmanid1_'.$n]=0 : $_REQUEST['batsmanid1_'.$n];
				(!is_numeric($_REQUEST['batsmanid2_'.$n])) ? $_REQUEST['batsmanid2_'.$n]=0 : $_REQUEST['batsmanid2_'.$n];
				($_REQUEST['partnership'.$n] != "") ? $_REQUEST['partnership'.$n] : $_REQUEST['partnership'.$n] =10101;
				

				$insert = dbToArray("call insertUpdatePartnership(".$_REQUEST['partnershipid'.$n].",".$_REQUEST['id'].",".$_REQUEST['wicket'.$n].",".$_REQUEST['batsmanid1_'.$n].",".$_REQUEST['batsmanid2_'.$n].",".$_REQUEST['partnership'.$n].",1);");

				if($result[1]['mtid']==2){
					//extra check to avoid error
					//if(!is_numeric($_REQUEST['batsmanid21_'.$n]) && $_REQUEST['batsmanid21_'.$n] <> "Select batsman 1"){
						//$_REQUEST['partnership2'.$n] =10101;
					//}				
					(!is_numeric($_REQUEST['batsmanid21_'.$n])) ? $_REQUEST['batsmanid21_'.$n]=0 : $_REQUEST['batsmanid21_'.$n];
					(!is_numeric($_REQUEST['batsmanid22_'.$n])) ? $_REQUEST['batsmanid22_'.$n]=0 : $_REQUEST['batsmanid22_'.$n];
					($_REQUEST['partnership2'.$n] != "") ? $_REQUEST['partnership2'.$n] : $_REQUEST['partnership2'.$n] =10101;
					
					$insert = dbToArray("call insertUpdatePartnership(".$_REQUEST['partnershipid2'.$n].",".$_REQUEST['id'].",".$_REQUEST['wicket'.$n].",".$_REQUEST['batsmanid21_'.$n].",".$_REQUEST['batsmanid22_'.$n].",".$_REQUEST['partnership2'.$n].",2);");
				}
			}			
			//process
			redirect_rel('process.php?id=2&np=matches.php'); 
		}
	}
	/*********************************
	Deleting a partnership
	*********************************/	
	else if($_REQUEST['action']=="remove"){
		$delete = dbToArray("call deletePartnership(".$_REQUEST['pshipid'].");");
		redirect_rel('process.php?id=2&np=matches.php');
	}
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
						<form name="thefrm" action="editmatch.php" method="post">
						<input type="hidden" name="action">
						<input type="hidden" name="id">
						<input type="hidden" name="t1" size="5">
						<input type="hidden" name="gid" size="5">
						<td rowspan="2" valign="top" bgcolor="#212121"; width="900" height="100%">						
						<table cellpadding="0" cellspacing="0" width="100%" border="0" id="mainfrm" height="100%">
							<tr>					
								<td align="left" valign="middle" style="font-size:24px; padding-left:10px; padding-top:10px;" id="whitetext" colspan="3"><?php echo "<strong>".$result[1]['thematch']."</strong> (edit match)"; ?></td>
							</tr>			
							<tr>
								<td colspan="2" id="whitetext" style="font-size:18px; padding-bottom:20px; padding-left:10px; padding-top:20px;">
								If you need to enter a new competition or grade, click the <img src="images/Tool_small.png"> button.  Once you have finished
								creating the new competition or grade, you will be able to select it from the drop down menu.<br><br>
								Note: You can only change the competition of a match to a competition of the same match type.  For example a limited
								overs competition to a limited overs competition.
								</td>
							</tr>
							<tr>
								<td colspan="5" width="100%" valign="top" style="padding-left:5px; padding-right:5px; padding-top:10px;">						
								<?php editMatch($_REQUEST['id'],$result,$result1,$result2,$error,$errormsg,$error1); ?>
								</td>
							</tr>
							<tr>
								<td align="center" style="padding-top:10px; padding-bottom:10px;">
								<input type="button" value="Save" onClick="Action('save',<?php echo $result[1]['matchid']; ?>);" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
								&nbsp;&nbsp;<input type="button" value="Cancel" onClick="javascript:document.location='matches.php'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
								</td>					
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
							<tr>
								<td>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td style="font-size:14px; padding-left:5px; padding-right:5px; padding-top:50px; " id="whitetext">
										<strong>Help advertise The Cricket Wizard</strong><br>
										<a href="PDF/The_Cricket_Wizard.pdf" target="_blank">Download</a> the following poster to display at your local clubrooms or school.<sup class="subcls">*</sup>
										</td>
									<tr>
										<td style="padding-left:5px; padding-right:5px; padding-top:10px;" align="center">
										<a href="PDF/The_Cricket_Wizard.pdf" target="_blank"><img src="images/poster.jpg" border="2" style="border-color:#CCCCCC; "></a>
										</td>
									</tr>
									<tr>
										<td style="font-size:14px; padding-left:5px; padding-right:5px; padding-top:10px;" id="whitetext"><sup class="subcls">*</sup> You will need <a href="http://www.adobe.com/products/reader/" target="_blank">Adobe Reader</a>.</td>
									</tr>
								</table>	
								</td>						
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
<script language="javascript">
function Action(actionIn, idIn) {
	frm = document.thefrm;
	frm.action.value = actionIn;
	frm.id.value = idIn;
	frm.submit();
}
</script>
<?php
function editMatch($mid,$result,$result1,$result2,$error,$errormsg,$error1){
if((isset($_REQUEST['t1'])&&$_REQUEST['t1']<>"")||$error1==1){
	$_REQUEST['opponent'] = set_param('opponent');
	$_REQUEST['date'] = set_param('date');
	$_REQUEST['season'] = set_param('season');
	$_REQUEST['compid'] = set_param('compid');
	$_REQUEST['gradeid'] = set_param('gradeid');
	$_REQUEST['venue'] = set_param('venue');
	$_REQUEST['result'] = set_param('result');
	$result[1]['opponent'] = $_REQUEST['opponent'];
	$result[1]['summary'] = $_REQUEST['summary'];
	$result[1]['date'] = $_REQUEST['date'];
	$result[1]['season'] = $_REQUEST['season'];
	$result[1]['competition'] = $_REQUEST['compid'];
	$result[1]['grade'] = $_REQUEST['gradeid'];
	$result[1]['venue'] = $_REQUEST['venue'];
	$result[1]['result'] = $_REQUEST['result'];
	$_REQUEST['gid'] = $result[1]['gradeid'];
}

?>
<table cellpadding="5" cellspacing="0" border="0" width="100%" id="whitetext">
	<?php
	if($error1==1){ ?>
	<tr>
		<td colspan="3"><?php validation("Please fill in all required fields."); ?></td>
	</tr>	
	<?php
	}
	?>
	<tr>
		<td width="180">VS:<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td width="320" colspan="2"><?php print_textBox("opponent", $result[1]['opponent'], "size='32' maxlength='32'",$error1); ?></td>
		<td rowspan="6" valign="top" width="400">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td id="whitetext">
				<?php 
				echo "Match summary: <br>";
				print_textArea('summary', stripslashes($result[1]['summary']), 'style= "width:400; height:150;"'); 
				?>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td width="180">Date (1st Innings):<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td width="320" colspan="2">
		<?php print_textBox("date", $result[1]['date'], "size='26' maxlength='26'",$error1); ?>&nbsp;&nbsp;
		<script language="JavaScript">
		new tcal ({
		// form name
		'formname': 'thefrm',
		// input name
		'controlname': 'date'
		});
		</script>
		</td>
	</tr>
	<tr>
		<td>Season:<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td><?php print_textBox("season", $result[1]['season'], "size='32' maxlength='32'",$error1); ?></td>
	</tr>
	<tr>
		<td>Competition:<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td><?php 
		if(isset($_REQUEST['t1']) && $_REQUEST['t1'] != ''){
			$result[1]['compid'] = $_REQUEST['t1'];
			$competition = dbToArray("call getComps('where compid = ".$result[1]['compid']."')");
			$result[1]['competition'] = $competition[1]['competition'];
		}
		$comp = dbToArray("call getComps('where teamid=".$result[1]['teamid']." and compid <> ".$result[1]['compid']." and mt.mtid = ".$result[1]['mtid']."')");
		print_dropDown("compid", $result[1]['competition'], $comp, $result[1]['competition'],'onChange=Result_Type('.$result[1]['matchid'].'); style= "width=180;"'); 
		?>
		</td>
		<td rowspan="2"><img src="images/Tool32.png" style="cursor:pointer" onClick="javascript:document.location='admin.php?id=<?php echo $_REQUEST['id'] ?>'"></td>
	</tr>
	<tr>
		<td>Grade:<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td><?php
		if(isset($_REQUEST['t1']) && $_REQUEST['t1'] != ''){
			$result[1]['gradeid'] = $_REQUEST['gid'];
			$grade = dbToArray("call getGrades('where gradeid = ".$result[1]['gradeid']."')");
			$result[1]['grade'] = $grade[1]['grade'];
		} 
		$grade = dbToArray("call getGrades('where teamid=".$result[1]['teamid']." and gradeid <> ".$result[1]['gradeid']."')");
		print_dropDown("gradeid", $result[1]['grade'], $grade, $result[1]['grade'],'style= "width=180;"'); 
		?>
		</td>
	</tr>
	<tr>	
		<td>Venue:<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td colspan="2"><?php print_textBox("venue", $result[1]['venue'], "size='32' maxlength='32'",$error1); ?></td>
	</tr>
	<tr>
		<td>Result:<font size="+1" color="#C80101";><sup>*</sup></font></td>
		<td colspan="2">
		<?php 
		$t1 = $result[1]['mtid'];
		$t2 = 3;		
		$default = $result[1]['result'];
		$selected = $result[1]['result'];	
		$criteria = "where resultid<>".$result[1]['resultid']." and type=".$t1." or type=".$t2;			
		if(isset($_REQUEST['t1'])&&$_REQUEST['t1']<>""){
			$criteria = "where type=".$t1." or type=".$t2;		
			$mtid = dbToArray("call getComps('where compid=".$_REQUEST['t1']."')");
			$t1 = $mtid[1]['mtid'];
			$default = "Select result";
			$selected = " ";
		}
		$r = dbToArray("call getResult('".$criteria."')");
		print_dropDown("resultid", $default, $r, $selected,'style= "width=180;"');
		?>
		</td>
	</tr>	
	<tr>
		<td colspan="4">
		<?php
		if($result[1]['mtid']==1){ 
			editPerformances($mid,$result1,$result2,$error,$errormsg,1); 
		}
		else if($result[1]['mtid']==2){ 
			editPerformances($mid,$result1,$result2,$error,$errormsg,2);
		}
		?>
		</td>
	</tr>
</table>
<script language="javascript">
function Result_Type(idIn){
	frm = document.thefrm;
	frm.id.value = idIn;
	frm.action.value = "edit";
	frm.t1.value = document.getElementById('compid').value;
	frm.gid.value = document.getElementById('gradeid').value;
	alert("Please re-select the match result.");
	frm.submit();
}
</script>
<?php
}



function editPerformances($mid,$result,$result2,$error,$errormsg,$mtid){
$p = dbToArray("call getPartnerships('WHERE m.matchid=".$mid." AND inningsid=1','ORDER BY wicket');");
$p2 = dbToArray("call getPartnerships('WHERE m.matchid=".$mid." AND inningsid=2','ORDER BY wicket');");
$wicket = array(1 => "1st", 2 => "2nd", 3 => "3rd", 4 => "4th", 5 => "5th", 6 => "6th", 7 => "7th", 8 => "8th", 9 => "9th", 10 => "10th");
?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<?php
	if($error<>0){?>
	<tr>
		<td style="padding-bottom:10px; padding-top:10px;"><?php validation("You have an error, please see below."); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td id="greytext">Batting Performances</td>
	</tr>
	<?php
	if($error==1){?>
	<tr>
		<td style="padding-bottom:20px; "><?php validation($errormsg); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="fixtures">
			<?php
			if($mtid==2){ ?>
			<tr>
				<td colspan="6"><strong>First Innings</strong></td>
			</tr>
			<?php } ?>
			<tr>
				<td>&nbsp;</td>
				<td>Runs scored</td>
				<td>Balls faced</td>
				<td>How out</td>
				<td>Fours</td>
				<td>Sixes</td>
			</tr>
			<?php
			for($i=1;$i<=count($result);$i++){ 
				if($result[$i]['did']==""){
					$dismissal = dbToArray("call getDismissal('')");
				}
				else {
					$dismissal = dbToArray("call getDismissal('where did <> ".$result[$i]['did']."')");
				}	
				if((isset($_REQUEST['t1'])&&$_REQUEST['t1']<>"") || $error<>0){
					$_REQUEST['dismissal'.$i] = set_param('dismissal'.$i);
					$result[$i]['dismissal'] = $_REQUEST['dismissal'.$i];
					$_REQUEST['runs'.$i] = set_param('runs'.$i);
					$result[$i]['runs'] = $_REQUEST['runs'.$i];
					$_REQUEST['ballsfaced'.$i] = set_param('ballsfaced'.$i);
					$result[$i]['ballsfaced'] = $_REQUEST['ballsfaced'.$i];
					$_REQUEST['boundaries'.$i] = set_param('boundaries'.$i);
					$result[$i]['boundaries'] = $_REQUEST['boundaries'.$i];
					$_REQUEST['sixes'.$i] = set_param('sixes'.$i);
					$result[$i]['sixes'] = $_REQUEST['sixes'.$i];
					if(is_numeric($_REQUEST['dismissal'.$i])){
						$dismissal = dbToArray("call getDismissal('where did <> ".$_REQUEST['dismissal'.$i]."')");
						$d = dbToArray("call getDismissal('where did = ".$_REQUEST['dismissal'.$i]."')");
						$result[$i]['dismissal'] = $d[1]['dismissal'];
					}
				}
				?>
				<input type="hidden" name="<?php echo 'playerid'.$i; ?>" value="<?php echo $result[$i]['playerid']; ?>">
				<input type="hidden" name="<?php echo 'battingid'.$i; ?>" value="<?php echo $result[$i]['battingid']; ?>">
				<input type="hidden" name="<?php echo 'bowlingid'.$i; ?>" value="<?php echo $result[$i]['bowlingid']; ?>">
				<input type="hidden" name="<?php echo 'fieldingid'.$i; ?>" value="<?php echo $result[$i]['fieldingid']; ?>">
				<input type="hidden" name="<?php echo 'performanceid'.$i; ?>" value="<?php echo $result[$i]['pid']; ?>">	
				<tr>
					<td><?php echo stripslashes($result[$i]['player']); ?></td>
					<td><?php print_textBox("runs".$i, $result[$i]['runs'], "size='4' maxlength='4'"); ?></td>
					<td>
					<?php
					if($result[$i]['ballsfaced']=="-"){
						 $result[$i]['ballsfaced'] = "";
					}
					print_textBox("ballsfaced".$i, $result[$i]['ballsfaced'], "size='4' maxlength='4'"); 
					?>
					</td>
					<td>
					<?php 
					if($result[$i]['dismissal']==""){ $result[$i]['dismissal']="Select mode of dismissal";}

					print_dropDown("dismissal".$i, $result[$i]['dismissal'], $dismissal, $result[$i]['dismissal'], 'style= ""'); 
					?></td>
					<td>
					<?php 
					if($result[$i]['boundaries']=="-"){
						 $result[$i]['boundaries'] = "";
					}
					print_textBox("boundaries".$i, $result[$i]['boundaries'], "size='4' maxlength='4'"); 
					?>
					</td>
					<td>
					<?php 
					if($result[$i]['sixes']=="-"){
						 $result[$i]['sixes'] = "";
					}
					print_textBox("sixes".$i, $result[$i]['sixes'], "size='4' maxlength='4'"); 
					?>
					</td>
				</tr>
			<?php } //loop finishes
			if($mtid==2){?>
			<tr>
				<td colspan="6"><strong>Second Innings</strong></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Runs scored</td>
				<td>Balls faced</td>
				<td>How out</td>
				<td>Fours</td>
				<td>Sixes</td>
			</tr>
			<?php	
			for($i=1;$i<=count($result2);$i++){ 
				if($result2[$i]['did']==""){
					$dismissal2 = dbToArray("call getDismissal('')");
				}
				else {
					$dismissal2 = dbToArray("call getDismissal('where did <> ".$result2[$i]['did']."')");
				}				
				if((isset($_REQUEST['t1'])&&$_REQUEST['t1']<>"") || $error<>0){
					$_REQUEST['dismissal2'.$i] = set_param('dismissal2'.$i);
					$result2[$i]['dismissal'] = $_REQUEST['dismissal2'.$i];
					$_REQUEST['runs2'.$i] = set_param('runs2'.$i);
					$result2[$i]['runs'] = $_REQUEST['runs2'.$i];
					$_REQUEST['ballsfaced2'.$i] = set_param('ballsfaced2'.$i);
					$result2[$i]['ballsfaced'] = $_REQUEST['ballsfaced2'.$i];
					$_REQUEST['boundaries2'.$i] = set_param('boundaries2'.$i);
					$result2[$i]['boundaries'] = $_REQUEST['boundaries2'.$i];
					$_REQUEST['sixes2'.$i] = set_param('sixes2'.$i);
					$result2[$i]['sixes'] = $_REQUEST['sixes2'.$i];
				}
				?>
				<input type="hidden" name="<?php echo 'playerid2'.$i; ?>" value="<?php echo $result2[$i]['playerid']; ?>">
				<input type="hidden" name="<?php echo 'battingid2'.$i; ?>" value="<?php echo $result2[$i]['battingid']; ?>">
				<input type="hidden" name="<?php echo 'bowlingid2'.$i; ?>" value="<?php echo $result2[$i]['bowlingid']; ?>">
				<input type="hidden" name="<?php echo 'fieldingid2'.$i; ?>" value="<?php echo $result2[$i]['fieldingid']; ?>">
				<input type="hidden" name="<?php echo 'performanceid2'.$i; ?>" value="<?php echo $result2[$i]['pid']; ?>">	
				<tr>
					<td><?php echo stripslashes($result2[$i]['player']); ?></td>
					<td><?php print_textBox("runs2".$i, $result2[$i]['runs'], "size='4' maxlength='4'"); ?></td>
					<td>
					<?php
					if($result2[$i]['ballsfaced']=="-"){
						 $result2[$i]['ballsfaced'] = "";
					}
					print_textBox("ballsfaced2".$i, $result2[$i]['ballsfaced'], "size='4' maxlength='4'"); 
					?>
					</td>
					<td>
					<?php 
					if($result2[$i]['dismissal']==""){ $result2[$i]['dismissal']="Select mode of dismissal";}
					print_dropDown("dismissal2".$i, $result2[$i]['dismissal'], $dismissal2, $result2[$i]['dismissal'], 'style= ""'); 
					?></td>
					<td>
					<?php 
					if($result2[$i]['boundaries']=="-"){
						 $result2[$i]['boundaries'] = "";
					}
					print_textBox("boundaries2".$i, $result2[$i]['boundaries'], "size='4' maxlength='4'"); 
					?>
					</td>
					<td>
					<?php 
					if($result2[$i]['sixes']=="-"){
						 $result2[$i]['sixes'] = "";
					}
					print_textBox("sixes2".$i, $result2[$i]['sixes'], "size='4' maxlength='4'"); 
					?>
					</td>
				</tr>
			<?php } 
			} ?>
		</table>
		</td>
	<tr>
		<td id="greytext">Bowling Performances</td>
	</tr>
	<?php
	if($error==2){?>
	<tr>
		<td style="padding-bottom:20px; "><?php validation($errormsg); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="fixtures">
			<?php
			if($mtid==2){ ?>
			<tr>
				<td colspan="7"><strong>First Innings</strong></td>
			</tr>
			<?php } ?>
			<tr>
				<td>&nbsp;</td>
				<td>Overs</td>
				<td>Maidens</td>
				<td>Runs conceded</td>
				<td>Wickets</td>
				<td>Wides</td>
				<td>No balls</td>
			</tr>
			<?php
			for($i=1;$i<=count($result);$i++){ 
				if((isset($_REQUEST['t1'])&&$_REQUEST['t1']<>"") || $error<>0){
					$_REQUEST['overs'.$i] = set_param('overs'.$i);
					$result[$i]['overs'] = $_REQUEST['overs'.$i];
					$_REQUEST['maidens'.$i] = set_param('maidens'.$i);
					$result[$i]['maidens'] = $_REQUEST['maidens'.$i];
					$_REQUEST['runsconceded'.$i] = set_param('runsconceded'.$i);
					$result[$i]['runsconceded'] = $_REQUEST['runsconceded'.$i];
					$_REQUEST['wickets'.$i] = set_param('wickets'.$i);
					$result[$i]['wickets'] = $_REQUEST['wickets'.$i];
					$_REQUEST['wides'.$i] = set_param('wides'.$i);
					$result[$i]['wides'] = $_REQUEST['wides'.$i];
					$_REQUEST['noballs'.$i] = set_param('noballs'.$i);
					$result[$i]['noballs'] = $_REQUEST['noballs'.$i];
				} ?>
				<tr>
					<td><?php echo stripslashes($result[$i]['player']); ?></td>
					<td><?php print_textBox("overs".$i, $result[$i]['overs'], "size='4' maxlength='4'"); ?></td>			
					<td>
					<?php 
					if($result[$i]['maidens']=='-'){ $result[$i]['maidens']=""; }
					print_textBox("maidens".$i, $result[$i]['maidens'], "size='4' maxlength='4'"); 
					?></td>
					<td><?php print_textBox("runsconceded".$i, $result[$i]['runsconceded'], "size='4' maxlength='4'"); ?></td>
					<td>
					<?php 
					$wickets = array();
					$wickets[1]['wickets']=0;$wickets[2]['wickets']=1;$wickets[3]['wickets']=2;$wickets[4]['wickets']=3;
					$wickets[5]['wickets']=4;$wickets[6]['wickets']=5;$wickets[7]['wickets']=6;$wickets[8]['wickets']=7;
					$wickets[9]['wickets']=8;$wickets[10]['wickets']=9;$wickets[11]['wickets']=10;
					print_dropDown("wickets".$i, $result[$i]['wickets'], $wickets, $result[$i]['wickets']);
					?></td>
					<td>
					<?php 
					if($result[$i]['wides']=='-'){ $result[$i]['wides']=""; }
					print_textBox("wides".$i, $result[$i]['wides'], "size='4' maxlength='4'"); 
					?></td>
					<td>
					<?php 
					if($result[$i]['noballs']=='-'){ $result[$i]['noballs']=""; }
					print_textBox("noballs".$i, $result[$i]['noballs'], "size='4' maxlength='4'"); 
					?></td>
				</tr>
			<?php } 
			if($mtid==2){?>
			<tr>
				<td colspan="7"><strong>Second Innings</strong></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Overs</td>
				<td>Maidens</td>
				<td>Runs conceded</td>
				<td>Wickets</td>
				<td>Wides</td>
				<td>No balls</td>
			</tr>
			<?php
			for($n=1;$n<=count($result2);$n++){ 
				if((isset($_REQUEST['t1'])&&$_REQUEST['t1']<>"") || $error<>0){
					$_REQUEST['overs2'.$n] = set_param('overs2'.$n);
					$result2[$n]['overs'] = $_REQUEST['overs2'.$n];
					$_REQUEST['maidens2'.$n] = set_param('maidens2'.$n);
					$result2[$n]['maidens'] = $_REQUEST['maidens2'.$n];
					$_REQUEST['runsconceded2'.$n] = set_param('runsconceded2'.$n);
					$result2[$n]['runsconceded'] = $_REQUEST['runsconceded2'.$n];
					$_REQUEST['wickets2'.$n] = set_param('wickets2'.$n);
					$result2[$n]['wickets'] = $_REQUEST['wickets2'.$n];
					$_REQUEST['wides2'.$n] = set_param('wides2'.$n);
					$result2[$n]['wides'] = $_REQUEST['wides2'.$n];
					$_REQUEST['noballs2'.$n] = set_param('noballs2'.$n);
					$result2[$n]['noballs'] = $_REQUEST['noballs2'.$n];
				} ?>
				<tr>
					<td><?php echo stripslashes($result2[$n]['player']); ?></td>
					<td><?php print_textBox("overs2".$n, $result2[$n]['overs'], "size='4' maxlength='4'"); ?></td>			
					<td>
					<?php 
					if($result2[$n]['maidens']=='-'){ $result2[$n]['maidens']=""; }
					print_textBox("maidens2".$n, $result2[$n]['maidens'], "size='4' maxlength='4'"); 
					?></td>
					<td><?php print_textBox("runsconceded2".$n, $result2[$n]['runsconceded'], "size='4' maxlength='4'"); ?></td>
					<td>
					<?php 
					$wickets2 = array();
					$wickets2[1]['wickets']=0;$wickets2[2]['wickets']=1;$wickets2[3]['wickets']=2;$wickets2[4]['wickets']=3;
					$wickets2[5]['wickets']=4;$wickets2[6]['wickets']=5;$wickets2[7]['wickets']=6;$wickets2[8]['wickets']=7;
					$wickets2[9]['wickets']=8;$wickets2[10]['wickets']=9;$wickets2[11]['wickets']=10;
					print_dropDown("wickets2".$n, $result2[$n]['wickets'], $wickets2, $result2[$n]['wickets']);
					?></td>
					<td>
					<?php 
					if($result2[$n]['wides']=='-'){ $result2[$n]['wides']=""; }
					print_textBox("wides2".$n, $result2[$n]['wides'], "size='4' maxlength='4'"); 
					?></td>
					<td>
					<?php 
					if($result2[$n]['noballs']=='-'){ $result2[$n]['noballs']=""; }
					print_textBox("noballs2".$n, $result2[$n]['noballs'], "size='4' maxlength='4'"); 
					?></td>
				</tr>
			<?php } 
			} ?>
		</table>
		</td>
	</tr>
	<tr>
		<td id="greytext">Fielding Performances</td>
	</tr>
	<?php
	if($error==3){?>
	<tr>
		<td style="padding-bottom:20px; "><?php validation($errormsg); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td>
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="fixtures">
			<?php
			if($mtid==2) { ?>
			<tr>
				<td colspan="4"><strong>First Innings</strong></td>
			</tr>
			<?php } ?>
			<tr>
				<td>&nbsp;</td>
				<td>Catches</td>
				<td>Stumpings</td>
				<td>Byes</td>
			</tr>
			<?php
			for($i=1;$i<=count($result);$i++){
				if((isset($_REQUEST['t1'])&&$_REQUEST['t1']<>"") || $error<>0){
					$_REQUEST['catches'.$i] = set_param('catches'.$i);
					$result[$i]['catches'] = $_REQUEST['catches'.$i];
					$_REQUEST['stumpings'.$i] = set_param('stumpings'.$i);
					$result[$i]['stumpings'] = $_REQUEST['stumpings'.$i];
					$_REQUEST['byes'.$i] = set_param('byes'.$i);
					$result[$i]['byes'] = $_REQUEST['byes'.$i];
				} ?>
				<tr>
					<td><?php echo stripslashes($result[$i]['player']); ?></td>
					<td>
					<?php 
					$catches = array();
					$catches[1]['catches']='';$catches[2]['catches']=1;$catches[3]['catches']=2;$catches[4]['catches']=3;
					$catches[5]['catches']=4;$catches[6]['catches']=5;$catches[7]['catches']=6;$catches[8]['catches']=7;
					$catches[9]['catches']=8;$catches[10]['catches']=9;$catches[11]['catches']=10;
					print_dropDown("catches".$i,$result[$i]['catches'], $catches, "size='15'"); 
					?></td>
					<td>
					<?php
					$stumpings = array();
					$stumpings[1]['stumpings']='';$stumpings[2]['stumpings']=1;$stumpings[3]['stumpings']=2;$stumpings[4]['stumpings']=3;
					$stumpings[5]['stumpings']=4;$stumpings[6]['stumpings']=5;$stumpings[7]['stumpings']=6;$stumpings[8]['stumpings']=7;
					$stumpings[9]['stumpings']=8;$stumpings[10]['stumpings']=9;$stumpings[11]['stumpings']=10;
					print_dropDown("stumpings".$i, $result[$i]['stumpings'], $stumpings, "size='15'"); 
					?></td>
					<td><?php print_textBox("byes".$i, $result[$i]['byes'], "size='4' maxlength='4'"); ?></td>
				</tr>
			<?php } 
			if($mtid==2) { ?>
			<tr>
				<td colspan="4"><strong>Second Innings</strong></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Catches</td>
				<td>Stumpings</td>
				<td>Byes</td>
			</tr>
			<?php
			for($n=1;$n<=count($result2);$n++){
				if((isset($_REQUEST['t1'])&&$_REQUEST['t1']<>"") || $error<>0){
					$_REQUEST['catches2'.$n] = set_param('catches2'.$n);
					$result2[$n]['catches'] = $_REQUEST['catches2'.$n];
					$_REQUEST['stumpings2'.$i] = set_param('stumpings2'.$n);
					$result2[$n]['stumpings'] = $_REQUEST['stumpings2'.$n];
					$_REQUEST['byes2'.$i] = set_param('byes2'.$n);
					$result2[$n]['byes'] = $_REQUEST['byes2'.$n];
				} ?>
				<tr>
					<td><?php echo stripslashes($result2[$n]['player']); ?></td>
					<td>
					<?php 
					$catches2 = array();
					$catches2[1]['catches']='';$catches2[2]['catches']=1;$catches2[3]['catches']=2;$catches2[4]['catches']=3;
					$catches2[5]['catches']=4;$catches2[6]['catches']=5;$catches2[7]['catches']=6;$catches2[8]['catches']=7;
					$catches2[9]['catches']=8;$catches2[10]['catches']=9;$catches2[11]['catches']=10;
					print_dropDown("catches2".$n,$result2[$n]['catches'], $catches2, "size='15'"); 
					?></td>
					<td>
					<?php
					$stumpings2 = array();
					$stumpings2[1]['stumpings']='';$stumpings2[2]['stumpings']=1;$stumpings2[3]['stumpings']=2;$stumpings2[4]['stumpings']=3;
					$stumpings2[5]['stumpings']=4;$stumpings2[6]['stumpings']=5;$stumpings2[7]['stumpings']=6;$stumpings2[8]['stumpings']=7;
					$stumpings2[9]['stumpings']=8;$stumpings2[10]['stumpings']=9;$stumpings2[11]['stumpings']=10;
					print_dropDown("stumpings2".$n, $result2[$n]['stumpings'], $stumpings, "size='15'"); 
					?></td>
					<td><?php print_textBox("byes2".$n, $result2[$n]['byes'], "size='4' maxlength='4'"); ?></td>
				</tr>
			<?php } 
			}?>
		</table>
		</td>
	</tr>
	<tr>
		<td id="greytext">Batting Partnerships</td>
	</tr>
	<tr>
		<td id="whitetext" style="font-size:18px; padding-top:10px; ">
		<div id="goldtext">Delete a partnership</div>
		To delete a partnership, click the <img src="images/Cancel_small.png"> button.
		</td>
	</tr>
	<?php
	if($error==4){?>
	<tr>
		<td style="padding-bottom:20px; padding-top:10px; "><?php validation($errormsg); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td style="padding-top:10px; ">
		<table cellpadding="5" cellspacing="0" border="1" width="100%" id="fixtures">
		<?php
		if($mtid==2){ ?>
			<tr>
				<td colspan="3"><strong>First Innings</strong></td>
			</tr>
		<?php } ?>
			<tr>
				<td>Wicket</td>
				<td>Runs</td>
				<td>Partners</td>
			</tr>
			<?php
			for($i=1;$i<=10;$i++){ 
				if(isset($p[$i]["partnership"]) && $p[$i]["partnership"]==10101){ $p[$i]["partnership"]=""; }
				if((isset($_REQUEST['t1'])&&$_REQUEST['t1']<>"") || $error<>0){
					$_REQUEST['batsmanid1_'.$i] 		= set_param('batsmanid1_'.$i);
					$p[$i]["player1"]		= set_session_param('batsmanid1_'.$i);
					$p[$i]["player1"]		= $_REQUEST['batsmanid1_'.$i];
					$_REQUEST['batsmanid2_'.$i] 		= set_param('batsmanid2_'.$i);
					$p[$i]["player2"] 		= set_session_param('batsmanid2_'.$i);
					$p[$i]["player2"] 		= $_REQUEST['batsmanid2_'.$i];
					$_REQUEST['partnership'.$i] 		= set_param('partnership'.$i);
					$p[$i]["partnership"]	= $_REQUEST['partnership'.$i];		
					if(is_numeric($_REQUEST['batsmanid1_'.$i])){
						$p[$i]['p1id'] = $_REQUEST['batsmanid1_'.$i];
						$get = dbToArray("call getPlayers('WHERE u.userid=".$_REQUEST['batsmanid1_'.$i]."','','');");
						$p[$i]["player1"] = $get[1]['player'];
					}
					//else if(!is_numeric($_REQUEST['batsmanid1_'.$i]) && $_REQUEST['batsmanid1_'.$i] <> "Select batsman 1"){
						//$p[$i]["player1"] = "Select batsman 1";
					//}
					if(is_numeric($_REQUEST['batsmanid2_'.$i])){
						$p[$i]['p2id'] = $_REQUEST['batsmanid2_'.$i];
						$get2 = dbToArray("call getPlayers('WHERE u.userid=".$_REQUEST['batsmanid2_'.$i]."','','');");
						$p[$i]["player2"] = $get2[1]['player'];
					}
					if(is_numeric($_REQUEST['batsmanid2_'.$i])) { $p[$i]['p2id'] = $_REQUEST['batsmanid2_'.$i]; }
				}
				$batsman1 = dbToArray("call getMatchPerformances('WHERE p.matchid=".$mid." AND p.matchinnings=1', 'ORDER BY player.firstname, player.lastname');");
				$batsman2 = $batsman1;		
				if(isset($p[$i]["partnership"]) && $p[$i]["partnership"]<>""){
					if($p[$i]["player1"]<>"Select batsman 1"){
						$batsman1 = dbToArray("call getMatchPerformances('WHERE p.matchid=".$mid." AND p.playerid<>".$p[$i]['p1id']." AND p.matchinnings=1', 'ORDER BY player.firstname, player.lastname');");
					}
					if($p[$i]["player2"]<>"Select batsman 2"){
						$batsman2 = dbToArray("call getMatchPerformances('WHERE p.matchid=".$mid." AND p.playerid<>".$p[$i]['p2id']." AND p.matchinnings=1', 'ORDER BY player.firstname, player.lastname');");
					}
				}
				?>
				<tr>
					<input type="hidden" name="partnershipid<?php echo $i; ?>" value="<?php echo (isset($p[$i]['partnershipid'])) ? $p[$i]['partnershipid'] : 0; ?>">
					<input type="hidden" name="wicket<?php echo $i; ?>" value="<?php echo (isset($p[$i]['wicket'])) ? $p[$i]['wicket'] : $i; ?>">	
					<td><?php echo $wicket[$i]; ?></td>
					<td>
					<?php 
					print_textBox("partnership".$i, (isset($p[$i]['partnership'])) ? $p[$i]["partnership"] : "", "size='6' maxlength='6'"); 
					?>
					</td>
					<td>
					<?php 
					print_dropDown("batsmanid1_".$i, (isset($p[$i]["player1"]) && $p[$i]['player1']<>"") ? stripslashes($p[$i]["player1"]) : "Select batsman 1", unstrip_array($batsman1), (isset($p[$i]["player1"]) && $p[$i]['player1']<>"") ? stripslashes($p[$i]["player1"]) : "Select batsman 1", 'style= "width:150px;"'); 
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					print_dropDown("batsmanid2_".$i, (isset($p[$i]["player2"]) && $p[$i]['player2']<>"") ? $p[$i]["player2"] : "Select batsman 2", unstrip_array($batsman2), (isset($p[$i]["player2"]) && $p[$i]['player2']<>"") ? $p[$i]["player2"] : "Select batsman 2", 'style= "width:150px;"'); 
					if(isset($_REQUEST['partnershipid'.$i])){
						if($_REQUEST['partnershipid'.$i]<>0){
							echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							echo "<img src='images/Cancel_small.png' style='cursor:pointer;' alt='delete' onClick='DeletePartnership(".$p[$i]["partnershipid"].");'></div>";
						}
					}
					else {
						if($p[$i]["partnership"]<>""){
							echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							echo "<img src='images/Cancel_small.png' style='cursor:pointer;' alt='delete' onClick='DeletePartnership(".$p[$i]["partnershipid"].");'></div>";	
						}
					}
					?>
					</td>
				</tr>
				<?php
			}
			if($mtid==2){ ?>
				<tr>
					<td colspan="3"><strong>Second Innings</strong></td>
				</tr>
				<tr>
					<td>Wicket</td>
					<td>Runs</td>
					<td>Partners</td>
				</tr>		
				<?php
				for($i=1;$i<=10;$i++){ 
					if($p2[$i]["partnership"]==10101){ $p2[$i]["partnership"]=""; }
					if((isset($_REQUEST['t1'])&&$_REQUEST['t1']<>"") || $error<>0){
						$_REQUEST['batsmanid21_'.$i] 		= set_param('batsmanid21_'.$i);
						$p2[$i]["player1"]		= set_session_param('batsmanid21_'.$i);
						$p2[$i]["player1"]		= $_REQUEST['batsmanid21_'.$i];
						$_REQUEST['batsmanid2_'.$i] 		= set_param('batsmanid22_'.$i);
						$p2[$i]["player2"] 		= set_session_param('batsmanid22_'.$i);
						$p2[$i]["player2"] 		= $_REQUEST['batsmanid22_'.$i];
						$_REQUEST['partnership2'.$i] 		= set_param('partnership'.$i);
						$p2[$i]["partnership"]	= $_REQUEST['partnership2'.$i];	
						if(is_numeric($_REQUEST['batsmanid21_'.$i])){
							$p2[$i]['p1id'] = $_REQUEST['batsmanid21_'.$i];
							$get = dbToArray("call getPlayers('WHERE u.userid=".$_REQUEST['batsmanid21_'.$i]."','','');");
							$p2[$i]["player1"] = $get[1]['player'];
						}
						if(is_numeric($_REQUEST['batsmanid22_'.$i])){
							$p[$i]['p2id'] = $_REQUEST['batsmanid22_'.$i];
							$get2 = dbToArray("call getPlayers('WHERE u.userid=".$_REQUEST['batsmanid22_'.$i]."','','');");
							$p2[$i]["player2"] = $get2[1]['player'];
						}
						if(is_numeric($_REQUEST['batsmanid22_'.$i])) { $p2[$i]['p2id'] = $_REQUEST['batsmanid22_'.$i]; }
					}
					$batsman21 = dbToArray("call getMatchPerformances('WHERE p.matchid=".$mid." AND p.matchinnings=2', 'ORDER BY player.firstname, player.lastname');");
					$batsman22 = $batsman21;		
					if($p2[$i]["partnership"]<>""){
						if($p2[$i]["player1"]<>"Select batsman 1"){
							$batsman1 = dbToArray("call getMatchPerformances('WHERE p.matchid=".$mid." AND p.playerid<>".$p2[$i]['p1id']." AND p.matchinnings=2', 'ORDER BY player.firstname, player.lastname');");
						}
						if($p2[$i]["player2"]<>"Select batsman 2"){
							$batsman2 = dbToArray("call getMatchPerformances('WHERE p.matchid=".$mid." AND p.playerid<>".$p2[$i]['p2id']." AND p.matchinnings=2', 'ORDER BY player.firstname, player.lastname');");
						}
					}
					?>
					<tr>
						<input type="hidden" name="partnershipid2<?php echo $i; ?>" value="<?php echo (isset($p2[$i]['partnershipid'])) ? $p2[$i]['partnershipid'] : 0; ?>">
						<input type="hidden" name="wicket2<?php echo $i; ?>" value="<?php echo (isset($p2[$i]['wicket'])) ? $p2[$i]['wicket'] : $i; ?>">	
						<td><?php echo $wicket[$i]; ?></td>
						<td>
						<?php 
						print_textBox("partnership2".$i, (isset($p2[$i]['wicket'])) ? $p2[$i]["partnership"] : "", "size='6' maxlength='6'"); 
						?>
						</td>
						<td>
						<?php 				
						print_dropDown("batsmanid21_".$i, ($p2[$i]['player1']<>"") ? $p2[$i]["player1"] : "Select batsman 1", unstrip_array($batsman21), (isset($p[$i]["player1"]) && $p[$i]['player1']<>"") ? stripslashes($p[$i]["player1"]) : "Select batsman 1", 'style= "width:150px;"'); 
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						print_dropDown("batsmanid22_".$i, ($p2[$i]['player2']<>"") ? $p2[$i]["player2"] : "Select batsman 2", unstrip_array($batsman22), (isset($p[$i]["player2"]) && $p[$i]['player2']<>"") ? stripslashes($p[$i]["player2"]) : "Select batsman 2", 'style= "width:150px;"'); 
						if(isset($_REQUEST['partnershipid2'.$i])){
							if($_REQUEST['partnershipid2'.$i]<>0){
								echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
								echo "<img src='images/Cancel_small.png' style='cursor:pointer;' alt='delete' onClick='DeletePartnership(".$p[$i]["partnershipid"].");'></div>";
							}
						}
						else {
							if($p2[$i]["partnership"]<>""){
								echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
								echo "<img src='images/Cancel_small.png' style='cursor:pointer;' alt='delete' onClick='DeletePartnership(".$p[$i]["partnershipid"].");'></div>";	
							}
						}					
						?>
						</td>
					</tr>
					<?php
				} // for loop
			} //mid=2
		?>		
		</table>
		</td>
	</tr>
</table>
<?php
}

function check_form_fields($result,$result2,$mtid){	
	$error->general = 0;
	$error->message = '';
	$error->details = 0;
	
	if($_REQUEST['opponent']=="") $error->details = 1;
	if($_REQUEST['date']=="") $error->details = 1;
	if($_REQUEST['season']=="") $error->details = 1;
	if($_REQUEST['venue']=="") $error->details = 1;
	
	/***************************************
	Batting Partnerships
	***************************************/		
	for($i=1; $i<=10; $i++){
		if($_REQUEST['batsmanid1_'.$i]<>"Select batsman 1" && $_REQUEST['batsmanid2_'.$i]<>"Select batsman 2"){ //user has selected both batsman
			if(is_numeric($_REQUEST['batsmanid1_'.$i])){
				$player1 = dbToArray("call getPlayers('WHERE u.userid=".$_REQUEST['batsmanid1_'.$i]."','','');");
				$player1 = $player1[1]['player'];
			}
			else {
				$player1 = $_REQUEST['batsmanid1_'.$i];
			}
			if(is_numeric($_REQUEST['batsmanid2_'.$i])){
				$player2 = dbToArray("call getPlayers('WHERE u.userid=".$_REQUEST['batsmanid2_'.$i]."','','');");
				$player2 = $player2[1]['player'];
			}
			else {
				$player2 = $_REQUEST['batsmanid2_'.$i];
			}
			//$player2 = dbToArray("call getPlayers('WHERE player=".$_REQUEST['batsmanid2_'.$i]." AND pt.teamid=".$result2[1]['teamid']."','','');");
			//check that the same player has not been selected for the same partnership.
			if($_REQUEST['batsmanid1_'.$i]==$_REQUEST['batsmanid2_'.$i]){
				//same player selected, display error
				$error->general = 4;
				$error->message = stripslashes($player1)." cannot be selected twice for the same partnership.";
				return $error;			
			}
			//check that the partnership has been entered
			if($_REQUEST['partnership'.$i]<>""){ // user has entered partnership.  
				//check that it is a valid entry
				if(!is_numeric($_REQUEST['partnership'.$i])){
					//not a valid entry, display error
					$error->general = 4;
					$error->message = $_REQUEST['partnership'.$i]." is not a valid partnership entry.";
					return $error;
				}
			}
			else {
				//user has not entered how many runs the partnership was worth
				//$player1 = dbToArray("call getPlayers('where u.userid=".$_REQUEST[
				$error->general = 4;
				$error->message = "Please enter how many runs the partnership between ".stripslashes($player1)." and ".stripslashes($player2)." was worth.";
				return $error;
			}
		}
		else if($_REQUEST['batsmanid1_'.$i]<>"Select batsman 1" && $_REQUEST['batsmanid2_'.$i]=="Select batsman 2"){ //user has only selected batsman 1
			$player1 = dbToArray("call getPlayers('where u.userid=".$_REQUEST['batsmanid1_'.$i]."','','');");
			//display error
			$error->general = 4;
			$error->message = "Please select ". stripslashes($player1[1]['player'])."'s batting partner.";
			return $error;
		}
		else if($_REQUEST['batsmanid1_'.$i]=="Select batsman 1" && $_REQUEST['batsmanid2_'.$i]<>"Select batsman 2"){ //user has only selected batsman 2
			$player2 = dbToArray("call getPlayers('where u.userid=".$_REQUEST['batsmanid2_'.$i]."','','');");
			//display error
			$error->general = 4;
			$error->message = "Please select ". stripslashes($player2[1]['player'])."'s batting partner.";
			return $error;
		}
		else if($_REQUEST['batsmanid1_'.$i]=="Select batsman 1" && $_REQUEST['batsmanid2_'.$i]=="Select batsman 2"){
			//check that the partnership has been entered
			if($_REQUEST['partnership'.$i]<>""){ // user has entered partnership.  
				$error->general = 4;
				$error->message = "You have enter a partnership of ".$_REQUEST['partnership'.$i]." runs, but have not selected the batting partners.";
				return $error;
			}			
		}
		if($mtid==2){
			if($_REQUEST['batsmanid21_'.$i]<>"Select batsman 1" && $_REQUEST['batsmanid22_'.$i]<>"Select batsman 2"){ //user has selected both batsman
				if(is_numeric($_REQUEST['batsmanid21_'.$i])){
					$player1 = dbToArray("call getPlayers('WHERE u.userid=".$_REQUEST['batsmanid21_'.$i]."','','');");
					$player1 = $player1[1]['player'];
				}
				else {
					$player1 = $_REQUEST['batsmanid21_'.$i];
				}
				if(is_numeric($_REQUEST['batsmanid22_'.$i])){
					$player2 = dbToArray("call getPlayers('WHERE u.userid=".$_REQUEST['batsmanid22_'.$i]."','','');");
					$player2 = $player2[1]['player'];
				}
				else {
					$player2 = $_REQUEST['batsmanid22_'.$i];
				}
				if($_REQUEST['batsmanid21_'.$i]==$_REQUEST['batsmanid22_'.$i]){
					//same player selected, display error
					$error->general = 4;
					$error->message = stripslashes($player1)." cannot be selected twice for the same partnership.";
					return $error;			
				}
				//check that the partnership has been entered
				if($_REQUEST['partnership2'.$i]<>""){ // user has entered partnership.  
					//check that it is a valid entry
					if(!is_numeric($_REQUEST['partnership2'.$i])){
						//not a valid entry, display error
						$error->general = 4;
						$error->message = $_REQUEST['partnership2'.$i]." is not a valid partnership entry.";
						return $error;
					}
				}
				else {
					//user has not entered how many runs the partnership was worth
					//$player1 = dbToArray("call getPlayers('where u.userid=".$_REQUEST[
					$error->general = 4;
					$error->message = "Please enter how many runs the partnership between ".stripslashes($player1)." and ".stripslashes($player2)." was worth.";
					return $error;
				}
			}
			else if($_REQUEST['batsmanid21_'.$i]<>"Select batsman 1" && $_REQUEST['batsmanid22_'.$i]=="Select batsman 2"){ //user has only selected batsman 1
				$player1 = dbToArray("call getPlayers('where u.userid=".$_REQUEST['batsmanid21_'.$i]."','','');");
				//display error
				$error->general = 4;
				$error->message = "Please select ". stripslashes($player1[1]['player'])."'s batting partner.";
				return $error;
			}
			else if($_REQUEST['batsmanid21_'.$i]=="Select batsman 1" && $_REQUEST['batsmanid22_'.$i]<>"Select batsman 2"){ //user has only selected batsman 2
				$player2 = dbToArray("call getPlayers('where u.userid=".$_REQUEST['batsmanid22_'.$i]."','','');");
				//display error
				$error->general = 4;
				$error->message = "Please select ". stripslashes($player2[1]['player'])."'s batting partner.";
				return $error;
			}		
			else if($_REQUEST['batsmanid21_'.$i]=="Select batsman 1" && $_REQUEST['batsmanid22_'.$i]=="Select batsman 2"){
				//check that the partnership has been entered
				if($_REQUEST['partnership2'.$i]<>""){ // user has entered partnership.  
					$error->general = 4;
					$error->message = "You have enter a partnership of ".$_REQUEST['partnership2'.$i]." runs, but have not selected the batting partners.";
					return $error;
				}			
			}
		}
	}
	$count = count($result);
	for($i=1; $i<=$count; $i++){
		/*****************************************
		Batting
		*****************************************/
		//user has entered runs scored.
		if($_REQUEST['runs'.$i]<>""){
			//check that it is a valid entry eg a number
			if(is_numeric($_REQUEST['runs'.$i])){
				//check if balls faced have been entered, if they have then check that they are valid
				if($_REQUEST['ballsfaced'.$i]<>""){ 
					if(is_numeric($_REQUEST['ballsfaced'.$i])){
						//if runs scored > 0 then balls faced cannot be 0
						if($_REQUEST['runs'.$i]>0 && $_REQUEST['ballsfaced'.$i]==0){
							$error->general = 1; 
							$error->message = $result[$i]['player']." cannot score ".$_REQUEST['runs'.$i]." runs off 0 balls faced, please check.";
							return $error;							
						}				
					}
					else {
						//not a number so display error
						$error->general = 1; 
						$error->message = "The balls faced for ".$result[$i]['player']." are not valid, please check.";
						return $error;							
					}
				}
				//check that user has selected mode of dismissal
				if($_REQUEST['dismissal'.$i]=="Select mode of dismissal"){
					$error->general = 1; 
					$error->message = "Please select how ".$result[$i]['player']." was dismissed.";
					return $error;			
				} 
				//check to see if user has enter has entered boundaries
				if($_REQUEST['boundaries'.$i]<>"" && $_REQUEST['sixes'.$i]<>""){
					//and check they are valid
					if(is_numeric($_REQUEST['boundaries'.$i]) && is_numeric($_REQUEST['sixes'.$i])){
						//check runs entered compared to the number of boundaries scored.
						$t4 = $_REQUEST['boundaries'.$i]*4;
						$t6 = $_REQUEST['sixes'.$i]*6;
					
						if(($t4+$t6)>$_REQUEST['runs'.$i]){
							$error->general = 1; 
							$error->message = "The boundaries ".$result[$i]['player']." has hit, equals more than the number of runs ".$result[$i]['firstname']." scored.  Please check."; 
							return $error;
						}
					}
					else {
						$error->general = 1; 
						$error->message = "The boundaries for ".$result[$i]['player']." are not valid, please check.";
						return $error;						
					}
				}
				else if($_REQUEST['boundaries'.$i]<>"" && $_REQUEST['sixes'.$i]==""){
					if(is_numeric($_REQUEST['boundaries'.$i])){
						$t4 = $_REQUEST['boundaries'.$i]*4;
					
						if($t4 > $_REQUEST['runs'.$i]){
							$error->general = 1; 
							$error->message = "The boundaries ".$result[$i]['player']." has hit, equals more than the number of runs ".$result[$i]['firstname']." scored.  Please check."; 
							return $error;
						}
					}
					else {
						$error->general = 1; 
						$error->message = "The boundaries for ".$result[$i]['player']." are not valid, please check.";
						return $error;								
					}					
				}
				else if($_REQUEST['boundaries'.$i]=="" && $_REQUEST['sixes'.$i]<>""){
					if(is_numeric($_REQUEST['sixes'.$i])){
						$t6 = $_REQUEST['sixes'.$i]*6;
					
						if($t6 > $_REQUEST['runs'.$i]){
							$error->general = 1; 
							$error->message = "The boundaries ".$result[$i]['player']." has hit, equals more than the number of runs ".$result[$i]['firstname']." scored.  Please check."; 
							return $error;		
						}			
					}
					else {
						$error->general = 1; 
						$error->message = "The boundaries for ".$result[$i]['player']." are not valid, please check.";
						return $error;								
					}
				}
			}
			else {
				//not a number so display error
				$error->general = 1; 
				$error->message = "The runs for ".$result[$i]['player']." are not valid, please check.";
				return $error;				
			}
		}
		else {
			if($_REQUEST['ballsfaced'.$i]<>"" || $_REQUEST['dismissal'.$i]<>"Select mode of dismissal" || $_REQUEST['boundaries'.$i]<>"" || $_REQUEST['sixes'.$i]<>""){
				$error->general = 1; 
				$error->message = "Please enter how many runs ".$result[$i]['player']." scored.";
				return $error;			
			}
		}
		if($mtid==2){
			//user has entered runs scored.
			if($_REQUEST['runs2'.$i]<>""){
				//check that it is a valid entry eg a number
				if(is_numeric($_REQUEST['runs2'.$i])){
					//check if balls faced have been entered, if they have then check that they are valid
					if($_REQUEST['ballsfaced2'.$i]<>""){ 
						if(is_numeric($_REQUEST['ballsfaced2'.$i])){
							//if runs scored > 0 then balls faced cannot be 0
							if($_REQUEST['runs2'.$i]>0 && $_REQUEST['ballsfaced2'.$i]==0){
								$error->general = 1; 
								$error->message = $result2[$i]['player']." cannot score ".$_REQUEST['runs2'.$i]." runs off 0 balls faced, please check.";
								return $error;							
							}				
						}
						else {
							//not a number so display error
							$error->general = 1; 
							$error->message = "The balls faced for ".$result2[$i]['player']." are not valid, please check.";
							return $error;							
						}
					}
					//check that user has selected mode of dismissal
					if($_REQUEST['dismissal2'.$i]=="Select mode of dismissal"){
						$error->general = 1; 
						$error->message = "Please select how ".$result2[$i]['player']." was dismissed in the second innings.";
						return $error;			
					} 
					//check to see if user has enter has entered boundaries
					if($_REQUEST['boundaries2'.$i]<>"" && $_REQUEST['sixes2'.$i]<>""){
						//and check they are valid
						if(is_numeric($_REQUEST['boundaries2'.$i]) && is_numeric($_REQUEST['sixes2'.$i])){
							//check runs entered compared to the number of boundaries scored.
							$t4 = $_REQUEST['boundaries2'.$i]*4;
							$t6 = $_REQUEST['sixes2'.$i]*6;
						
							if(($t4+$t6)>$_REQUEST['runs2'.$i]){
								$error->general = 1; 
								$error->message = "The boundaries ".$result2[$i]['player']." has hit, equals more than the number of runs ".$result2[$i]['firstname']." scored.  Please check."; 
								return $error;
							}
						}
						else {
							$error->general = 1; 
							$error->message = "The boundaries for ".$result2[$i]['player']." are not valid, please check.";
							return $error;						
						}
					}
					else if($_REQUEST['boundaries2'.$i]<>"" && $_REQUEST['sixes2'.$i]==""){
						if(is_numeric($_REQUEST['boundaries2'.$i])){
							$t4 = $_REQUEST['boundaries2'.$i]*4;
						
							if($t4 > $_REQUEST['runs2'.$i]){
								$error->general = 1; 
								$error->message = "The boundaries ".$result2[$i]['player']." has hit, equals more than the number of runs ".$result2[$i]['firstname']." scored.  Please check."; 
								return $error;
							}
						}
						else {
							$error->general = 1; 
							$error->message = "The boundaries for ".$result2[$i]['player']." are not valid, please check.";
							return $error;								
						}					
					}
					else if($_REQUEST['boundaries2'.$i]=="" && $_REQUEST['sixes2'.$i]<>""){
						if(is_numeric($_REQUEST['sixes2'.$i])){
							$t6 = $_REQUEST['sixes2'.$i]*6;
						
							if($t6 > $_REQUEST['runs2'.$i]){
								$error->general = 1; 
								$error->message = "The boundaries ".$result2[$i]['player']." has hit, equals more than the number of runs ".$result2[$i]['firstname']." scored.  Please check."; 
								return $error;		
							}			
						}
						else {
							$error->general = 1; 
							$error->message = "The boundaries for ".$result2[$i]['player']." are not valid, please check.";
							return $error;								
						}
					}
				}
				else {
					//not a number so display error
					$error->general = 1; 
					$error->message = "The runs for ".$result2[$i]['player']." are not valid, please check.";
					return $error;				
				}
			}
			else {
				if($_REQUEST['ballsfaced2'.$i]<>"" || $_REQUEST['dismissal2'.$i]<>"Select mode of dismissal" || $_REQUEST['boundaries2'.$i]<>"" || $_REQUEST['sixes2'.$i]<>""){
					$error->general = 1; 
					$error->message = "Please enter how many runs ".$result2[$i]['player']." scored in the second innings.";
					return $error;			
				}
			}
		}	
		/***************************************
		Bowling
		**************************************/
		//user has entered overs
		if($_REQUEST['overs'.$i]<>""){
			//check that overs entered are valid
			if(is_numeric($_REQUEST['overs'.$i])){
				//check if maidens entered and are valid
				if($_REQUEST['maidens'.$i]<>""){
					if(is_numeric($_REQUEST['maidens'.$i])){
						//cannot bowl more maidens than overs
						if($_REQUEST['maidens'.$i] > $_REQUEST['overs'.$i]){
							$error->general = 2; 
							$error->message = $result[$i]['player']." cannot bowl more maidens than overs, please check.";
							return $error;							
						}
					}
					else {
						$error->general = 2; 
						$error->message = "The maidens for ".$result[$i]['player']." are not valid, please check.";
						return $error;									
					}
				}
				//check if runs conceded have been entered, and are valid.
				if($_REQUEST['runsconceded'.$i]<>""){
					if(is_numeric($_REQUEST['runsconceded'.$i])){
						//if overs = maidens, then runs conceded must be 0.
						if($_REQUEST['overs'.$i] == $_REQUEST['maidens'.$i] && $_REQUEST['runsconceded'.$i] <> 0){
							$error->general = 2; 
							$error->message = $result[$i]['player']." has bowled ".$_REQUEST['overs'.$i]." overs and bowled ".$_REQUEST['maidens'.$i]." maidens, which means runs conceded must be 0.  Please check";
							return $error;								
						}
						//if runs conceded = 0, the maidens bowled must = the number of overs bowled.
						if($_REQUEST['runsconceded'.$i]==0 && $_REQUEST['overs'.$i]<>$_REQUEST['maidens'.$i]){
							$error->general = 2; 
							$error->message = $result[$i]['player']." has conceded 0 runs from ".$_REQUEST['overs'.$i]." overs, which means the number of maidens bowled must match the number of overs bowled.  Please check.";
							return $error;								
						}
					}
					else {
						$error->general = 2; 
						$error->message = "The runs conceded for ".$result[$i]['player']." are not valid, please check.";
						return $error;							
					}
				}
				else {
					$error->general = 2; 
					$error->message = "Please enter how many runs ".$result[$i]['player']." went for.";
					return $error;						
				}
				//check to see if user has enter has entered extras
				if($_REQUEST['wides'.$i]<>"" && $_REQUEST['noballs'.$i]<>""){
					//and check they are valid
					if(is_numeric($_REQUEST['wides'.$i]) && is_numeric($_REQUEST['noballs'.$i])){
						//check runs conceded compared to the number of extras.				
						if(($_REQUEST['wides'.$i]+$_REQUEST['noballs'.$i])>$_REQUEST['runsconceded'.$i]){
							$error->general = 2; 
							$error->message = "The extras ".$result[$i]['player']." conceded, equals more than the number of runs ".$result[$i]['firstname']." conceded.  Please check."; 
							return $error;
						}
					}
					else {
						$error->general = 2; 
						$error->message = "The extras for ".$result[$i]['player']." are not valid, please check.";
						return $error;						
					}
				}
				else if($_REQUEST['wides'.$i]<>"" && $_REQUEST['noballs'.$i]==""){
					if(is_numeric($_REQUEST['wides'.$i])){								
						if($_REQUEST['wides'.$i] > $_REQUEST['runsconceded'.$i]){
							$error->general =2; 
							$error->message = "The extras ".$result[$i]['player']." conceded, equals more than the number of runs ".$result[$i]['firstname']." conceded.  Please check."; 
							return $error;
						}
					}
					else {
						$error->general = 2; 
						$error->message = "The extras for ".$result[$i]['player']." are not valid, please check.";
						return $error;								
					}					
				}
				else if($_REQUEST['wides'.$i]=="" && $_REQUEST['noballs'.$i]<>""){
					if(is_numeric($_REQUEST['noballs'.$i])){					
						if($_REQUEST['noballs'.$i] > $_REQUEST['runsconceded'.$i]){
							$error->general = 2; 
							$error->message = "The extras ".$result[$i]['player']." conceded, equals more than the number of runs ".$result[$i]['firstname']." conceded.  Please check."; 
							return $error;		
						}			
					}
					else {
						$error->general = 2; 
						$error->message = "The boundaries for ".$result[$i]['player']." are not valid, please check.";
						return $error;								
					}
				}
			}
			else {
				$error->general = 2; 
				$error->message = "The overs for ".$result[$i]['player']." are not valid, please check.";
				return $error;				
			}
		}
		else {
			if($_REQUEST['maidens'.$i]<>"" || $_REQUEST['runsconceded'.$i]<>"" || $_REQUEST['wides'.$i]<>"" || $_REQUEST['noballs'.$i]<>""){
				$error->general = 2; 
				$error->message = "Please enter how many overs ".$result[$i]['player']." bowled.";
				return $error;			
			}
		}
		if($mtid==2){
			//user has entered overs
			if($_REQUEST['overs2'.$i]<>""){
				//check that overs entered are valid
				if(is_numeric($_REQUEST['overs2'.$i])){
					//check if maidens entered and are valid
					if($_REQUEST['maidens2'.$i]<>""){
						if(is_numeric($_REQUEST['maidens2'.$i])){
							//cannot bowl more maidens than overs
							if($_REQUEST['maidens2'.$i] > $_REQUEST['overs2'.$i]){
								$error->general = 2; 
								$error->message = $result[$i]['player']." cannot bowl more maidens than overs, please check.";
								return $error;							
							}
						}
						else {
							$error->general = 2; 
							$error->message = "The maidens for ".$result[$i]['player']." are not valid, please check.";
							return $error;									
						}
					}
					//check if runs conceded have been entered, and are valid.
					if($_REQUEST['runsconceded2'.$i]<>""){
						if(is_numeric($_REQUEST['runsconceded2'.$i])){
							//if overs = maidens, then runs conceded must be 0.
							if($_REQUEST['overs2'.$i] == $_REQUEST['maidens2'.$i] && $_REQUEST['runsconceded2'.$i] <> 0){
								$error->general = 2; 
								$error->message = $result[$i]['player']." has bowled ".$_REQUEST['overs2'.$i]." overs and bowled ".$_REQUEST['maidens2'.$i]." maidens, which means runs conceded must be 0.  Please check";
								return $error;								
							}
							//if runs conceded = 0, the maidens bowled must = the number of overs bowled.
							if($_REQUEST['runsconceded2'.$i]==0 && $_REQUEST['overs2'.$i]<>$_REQUEST['maidens2'.$i]){
								$error->general = 2; 
								$error->message = $result[$i]['player']." has conceded 0 runs from ".$_REQUEST['overs2'.$i]." overs, which means the number of maidens bowled must match the number of overs bowled.  Please check.";
								return $error;								
							}
						}
						else {
							$error->general = 2; 
							$error->message = "The runs conceded for ".$result[$i]['player']." are not valid, please check.";
							return $error;							
						}
					}
					else {
						$error->general = 2; 
						$error->message = "Please enter how many runs ".$result[$i]['player']." went for in the second innings.";
						return $error;						
					}
					//check to see if user has enter has entered extras
					if($_REQUEST['wides2'.$i]<>"" && $_REQUEST['noballs2'.$i]<>""){
						//and check they are valid
						if(is_numeric($_REQUEST['wides2'.$i]) && is_numeric($_REQUEST['noballs2'.$i])){
							//check runs conceded compared to the number of extras.				
							if(($_REQUEST['wides2'.$i]+$_REQUEST['noballs2'.$i])>$_REQUEST['runsconceded2'.$i]){
								$error->general = 2; 
								$error->message = "The extras ".$result[$i]['player']." conceded, equals more than the number of runs ".$result[$i]['firstname']." conceded.  Please check."; 
								return $error;
							}
						}
						else {
							$error->general = 2; 
							$error->message = "The extras for ".$result[$i]['player']." are not valid, please check.";
							return $error;						
						}
					}
					else if($_REQUEST['wides2'.$i]<>"" && $_REQUEST['noballs2'.$i]==""){
						if(is_numeric($_REQUEST['wides2'.$i])){								
							if($_REQUEST['wides2'.$i] > $_REQUEST['runsconceded2'.$i]){
								$error->general = 2; 
								$error->message = "The extras ".$result[$i]['player']." conceded, equals more than the number of runs ".$result[$i]['firstname']." conceded.  Please check."; 
								return $error;
							}
						}
						else {
							$error->general = 2; 
							$error->message = "The extras for ".$result[$i]['player']." are not valid, please check.";
							return $error;								
						}					
					}
					else if($_REQUEST['wides2'.$i]=="" && $_REQUEST['noballs2'.$i]<>""){
						if(is_numeric($_REQUEST['noballs2'.$i])){					
							if($_REQUEST['noballs2'.$i] > $_REQUEST['runsconceded2'.$i]){
								$error->general = 2; 
								$error->message = "The extras ".$result[$i]['player']." conceded, equals more than the number of runs ".$result[$i]['firstname']." conceded.  Please check."; 
								return $error;		
							}			
						}
						else {
							$error->general = 2; 
							$error->message = "The boundaries for ".$result[$i]['player']." are not valid, please check.";
							return $error;								
						}
					}
				}
				else {
					$error->general = 2; 
					$error->message = "The overs for ".$result[$i]['player']." are not valid, please check.";
					return $error;				
				}
			}
			else {
				if($_REQUEST['maidens2'.$i]<>"" || $_REQUEST['runsconceded2'.$i]<>"" || $_REQUEST['wides2'.$i]<>"" || $_REQUEST['noballs2'.$i]<>""){
					$error->general = 2; 
					$error->message = "Please enter how many overs ".$result[$i]['player']." bowled in the second innings.";
					return $error;			
				}
			}
		}
		/***************************************
		Fielding
		***************************************/
		//user has entered byes
		if($_REQUEST['byes'.$i]<>""){
			//check that byes entered are valid
			if(!is_numeric($_REQUEST['byes'.$i])){
				$error->general = 3; 
				$error->message = "The byes for ".$result[$i]['player']." are not valid, please check.";
				return $error;	
			}
		}
		if($mtid==2){
			//user has entered byes
			if($_REQUEST['byes2'.$i]<>""){
				//check that byes entered are valid
				if(!is_numeric($_REQUEST['byes2'.$i])){
					$error->general = 3; 
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
<form name="deletepship" action="viewmatch.php?id=<?php echo $_REQUEST['id'] ?>" method="post">
<input type="hidden" name="pshipid">
<input type="hidden" name="action">
</form>
<script language="javascript">
function DeletePartnership(theid){
	frm = document.deletepship;
	ans = confirm("Are you sure you want to permanently remove this partnership from the system?\nNote: all other changes on this page will be lost.");
	if (ans == true ) {
		frm.action.value = "remove";
		frm.pshipid.value = theid;
		frm.submit();
	}	
}
</script>