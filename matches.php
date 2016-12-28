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
$colour = "#F7C200";
$text = "Fixtures";
//print_r($_REQUEST);
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#CCCCCC" border="0" height="100%">
	<tr>
		<td valign="top" style="padding-top:5px; ">
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
						<td rowspan="2" valign="top" bgcolor="#212121"; width="900" height="100%" id="mainfrm">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr>
								<td align="left" valign="top" style="font-size:24px; padding-left:15px; padding-top:10px;" id="whitetext" colspan="3"><strong><?php echo $user->teamname; ?></strong></td>
							</tr>
							<?php
							if($user->role=="Team Admin"){ ?>
							<tr>
								<td align="right" valign="top" colspan="2" style="padding-right:20px; padding-bottom:10px; "><input type="button" value="Enter a new match" onClick="javascript:document.location='matchselectcomp.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
							</tr>
							<?php } ?>
							<tr>
								<td colspan="5" width="100%" valign="top" style="padding-left:15px; padding-right:15px; padding-top:10px; padding-bottom:20px;"><?php matches($user); ?></td>
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
function matches($user){
$criteria = "WHERE m.teamid=".$user->teamid."";
$selected=0;
//check if opponent has been selected
if((isset($_REQUEST['opponent']))&&$_REQUEST['opponent']<>"Filter by opposition"){
	$opponent = '"'.$_REQUEST['opponent'].'"';
	$selected=1;
	//check if comp has been selected
	if((isset($_REQUEST['compid']))&&$_REQUEST['compid']<>"Filter by competition"){
		//check if season has been selected
		if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
			//all three have been selected so set criteria			
			$season = '"'.$_REQUEST['season'].'"';
			$criteria = "WHERE m.teamid=".$user->teamid." AND opponent=".$opponent." AND m.compid=".$_REQUEST['compid']." AND season=".$season."";
			$selected=1;
		}
		else {
			//season not selected, but opponent and comp have been selected
			$criteria = "WHERE m.teamid=".$user->teamid." AND opponent=".$opponent." AND m.compid=".$_REQUEST['compid']."";
			$selected=1;
		}
	}
	else if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
		//comp not select but opponent and season have been
		$season = '"'.$_REQUEST['season'].'"';
		$criteria = "WHERE m.teamid=".$user->teamid." AND opponent=".$opponent." AND season=".$season."";			
		$selected=1;
	}
	else {
		//just opponent has been selected
		$criteria = "WHERE m.teamid=".$user->teamid." AND opponent=".$opponent."";	
		$selected=1;
	}
}
//Opponent not selected but check comp and season
else if(isset($_REQUEST['compid'])&&$_REQUEST['compid']<>"Filter by competition"){
	//season and comp have been selected
	if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
		$season = '"'.$_REQUEST['season'].'"';
		$criteria = "WHERE m.teamid=".$user->teamid." AND m.compid=".$_REQUEST['compid']." AND season=".$season."";
		$selected=1;
	}
	else {
		//just comp been selected
		$criteria = "WHERE m.teamid=".$user->teamid." AND m.compid=".$_REQUEST['compid']."";
		$selected=1;
	}		
}
//just season has been selected
else if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
	$season = '"'.$_REQUEST['season'].'"';
	$criteria = "WHERE m.teamid=".$user->teamid." AND season=".$season."";
	$selected=1;
}

$sql = "call getMatchesPlayed('".$criteria."','','')";
$result = dbToArray($sql);	
$count = count($result);

$o="Filter by opponent";
if(isset($_REQUEST['opponent'])){
	$o = $_REQUEST['opponent'];
}
$c="Filter by competition";
if(isset($_REQUEST['compid'])){
	$c = $_REQUEST['compid'];
}
$s="Filter by season";
if(isset($_REQUEST['season'])){
	$s = $_REQUEST['season'];
}
/*********************************
Deleting a match
*********************************/
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=="remove"){
		$delete = dbToArray("call deleteMatch(".$_REQUEST['id'].");");
		redirect_rel('process.php?id=2&np=matches.php');
	}
}
$rows = $count;
if(!isset($_GET['pagenum'])){
	$pagenum = 1;
}
else {
	$pagenum = $_GET['pagenum'];
}

$page_rows = 10;
$last = ceil($rows/$page_rows);
if ($pagenum < 1) { 
	$pagenum = 1; 
} 
elseif ($pagenum > $last) { 
	$pagenum = $last; 
} 
$max = 'limit ' .($pagenum - 1) * $page_rows .',' .$page_rows;

if($count<>0){
	$sql1 = "call getMatchesPlayed('".$criteria."','order by m.date desc','".$max."')";
	$result1 = dbToArray($sql1);	
	$count1 = count($result1);
}
?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td id="whitetext" style="font-size:18px; padding-bottom:20px;">
		<?php
		if($count==0 && $selected==0){
			echo "<em>No fixtures have been played.</em>";
		}
		else {
		?>
			Below is a list of fixtures played by your team.  <br>To filter your fixtures, select from the drop down menus below and click the View button.
			To remove the filter, click Clear.<br><br>
			<?php
			if($user->role=="Team Admin"){ ?>
				<div id="goldtext">Enter a new match</div>
				To enter a new match, click the Enter a new match button.<br><br>
				<div id="goldtext">View a match</div>
				To view a match in detail, click on the opponents name.<br><br>
				<div id="goldtext">Edit a match</div>
				To edit a match, click the <img src="images/Write_small.png"> button.<br><br>
				<div id="goldtext">Delete a match</div>
				To delete a match from the system, click the <img src="images/Cancel_small.png"> button.
				Deleting a match will delete all the player performances associated with the match from the system.<br><br>
			<?php 
			}
			else { ?>
				<div id="goldtext">View a match</div>
				To view a match in detail, click on the opponents name.<br><br>			
			<?php
			}  
		}
		?> 
		</td>
	</tr>
	<?php
	//check if games have been played.
	if($count<>0){?>
	<tr>
		<td>
		<form name="tfrm" action="matches.php" method="post">
		<table cellpadding="5" cellspacing="0" border="0">
			<tr>
				<td style="padding-left:5px;">
				<?php 
				$opponents = dbToArray("call getOpponents('where teamid=".$user->teamid."')"); 
				print_dropDown("opponent", "Filter by opposition", $opponents,$o);
				?>
				</td>
				<td style="padding-left:20px;">
				<?php 
				$comps = dbToArray("call getComps('where teamid=".$user->teamid."')"); 
				print_dropDown("compid", "Filter by competition", $comps,$c);
				?>
				</td>
				<td style="padding-left:20px;">
				<?php 
				$seasons = dbToArray("call getSeasons(".$user->teamid.")"); 
				print_dropDown("season", "Filter by season", $seasons,$s);
				?>								
				</td>
			</tr>
			<tr>
				<td style="padding-left:5px;" colspan="2">
				<input type="button" value="View" onClick="View();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">&nbsp;&nbsp;
				<input type="button" value="Clear" onClick="javascript:document.location='matches.php'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
				</td>
			</tr>						
		</table>
		</form>	
		</td>
	</tr>
	<tr>
		<td id="whitetext" style="padding-bottom:5px;" align="right"><strong><?php echo "Matches played: ".$count; ?></strong></td>
	</tr>	
	<tr>
		<td>
		<form name="viewmatch" action="viewmatch.php" method="post">
		<input type="hidden" name="id">
		<input type="hidden" name="action">
		<input type="hidden" id="t" value="<?php echo $user->teamname; ?>">		
		<table width="100%" cellpadding="5" cellspacing="0" border="1" id="fixtures">
			<tr>
				<td align="right" valign="middle" colspan="6">
				<?php				
				if ($pagenum == 1){
					echo "&nbsp;";
				} 
				else {
				$firstpage = "<a href='{$_SERVER['PHP_SELF']}?pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
				if((isset($_REQUEST['opponent']))&&$_REQUEST['opponent']<>"Filter by opposition"){
					$opponent = '"'.$_REQUEST['opponent'].'"';
					//check if comp has been selected
					if((isset($_REQUEST['compid']))&&$_REQUEST['compid']<>"Filter by competition"){
						//check if season has been selected
						if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
							//all three have been selected so set criteria			
							$firstpage = "<a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
						}
						else {
							//season not selected, but opponent and comp have been selected
							$firstpage = "<a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
						}
					}
					else if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
						//comp not select but opponent and season have been
						$firstpage = "<a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
					}
					else {
						//just opponent has been selected
						$firstpage = "<a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
					}
				}
				//Opponent not selected but check comp and season
				else if(isset($_REQUEST['compid'])&&$_REQUEST['compid']<>"Filter by competition"){
					//season and comp have been selected
					if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
						$firstpage = "<a href='{$_SERVER['PHP_SELF']}?compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
					}
					else {
						//just comp been selected
						$firstpage = "<a href='{$_SERVER['PHP_SELF']}?compid=".$_REQUEST['compid']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
					}		
				}
				//just season has been selected
				else if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
					$firstpage = "<a href='{$_SERVER['PHP_SELF']}?season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
				}				
				echo $firstpage;
				echo " ";
				$previous = $pagenum-1;
				$prevpage = "<a href='{$_SERVER['PHP_SELF']}?pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";				
				//check if opponent has been selected
				if((isset($_REQUEST['opponent']))&&$_REQUEST['opponent']<>"Filter by opposition"){
					$opponent = '"'.$_REQUEST['opponent'].'"';
					//check if comp has been selected
					if((isset($_REQUEST['compid']))&&$_REQUEST['compid']<>"Filter by competition"){
						//check if season has been selected
						$comptext = $_REQUEST['compid'];
						if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
							//all three have been selected so set criteria			
							$prevpage = "<a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
						}
						else {
							//season not selected, but opponent and comp have been selected
							$prevpage = "<a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
						}
					}
					else if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
						//comp not select but opponent and season have been
							$prevpage = "<a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
					}
					else {
						//just opponent has been selected
						$prevpage = "<a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
					}
				}
				//Opponent not selected but check comp and season
				else if(isset($_REQUEST['compid'])&&$_REQUEST['compid']<>"Filter by competition"){
					//season and comp have been selected
					if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
						$prevpage = "<a href='{$_SERVER['PHP_SELF']}?compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
					}
					else {
						//just comp been selected
						$prevpage = "<a href='{$_SERVER['PHP_SELF']}?compid=".$_REQUEST['compid']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
					}		
				}
				//just season has been selected
				else if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
					$prevpage = "<a href='{$_SERVER['PHP_SELF']}?season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
				}
				echo $prevpage;
				echo " ";
				}		
				if ($pagenum == $last) {
				} 
				else {
				$next = $pagenum+1;
				$nextpage = "<a href='{$_SERVER['PHP_SELF']}?pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";				
				//check if opponent has been selected
				if((isset($_REQUEST['opponent']))&&$_REQUEST['opponent']<>"Filter by opposition"){
					$opponent = '"'.$_REQUEST['opponent'].'"';
					//check if comp has been selected
					if((isset($_REQUEST['compid']))&&$_REQUEST['compid']<>"Filter by competition"){
						//check if season has been selected
						$comptext = $_REQUEST['compid'];
						if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
							//all three have been selected so set criteria			
							$nextpage = "<a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
						}
						else {
							//season not selected, but opponent and comp have been selected
							$nextpage = "<a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
						}
					}
					else if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
						//comp not select but opponent and season have been
							$nextpage = "<a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
					}
					else {
						//just opponent has been selected
						$nextpage = "<a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
					}
				}
				//Opponent not selected but check comp and season
				else if(isset($_REQUEST['compid'])&&$_REQUEST['compid']<>"Filter by competition"){
					//season and comp have been selected
					if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
						$nextpage = "<a href='{$_SERVER['PHP_SELF']}?compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
					}
					else {
						//just comp been selected
						$nextpage = "<a href='{$_SERVER['PHP_SELF']}?compid=".$_REQUEST['compid']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
					}		
				}
				//just season has been selected
				else if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
					$nextpage = "<a href='{$_SERVER['PHP_SELF']}?season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
				}				
				echo $nextpage;
				echo " ";
				$lastpage = " <a href='{$_SERVER['PHP_SELF']}?pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
				//check if opponent has been selected
				if((isset($_REQUEST['opponent']))&&$_REQUEST['opponent']<>"Filter by opposition"){
					$opponent = '"'.$_REQUEST['opponent'].'"';
					//check if comp has been selected
					if((isset($_REQUEST['compid']))&&$_REQUEST['compid']<>"Filter by competition"){
						//check if season has been selected
						$comptext = $_REQUEST['compid'];
						if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
							//all three have been selected so set criteria			
							$lastpage = " <a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
						}
						else {
							//season not selected, but opponent and comp have been selected
							$lastpage = " <a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
						}
					}
					else if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
						//comp not select but opponent and season have been
						$lastpage = " <a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
					}
					else {
						//just opponent has been selected
						$lastpage = " <a href='{$_SERVER['PHP_SELF']}?opponent=".$_REQUEST['opponent']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
					}
				}
				//Opponent not selected but check comp and season
				else if(isset($_REQUEST['compid'])&&$_REQUEST['compid']<>"Filter by competition"){
					//season and comp have been selected
					if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
						$lastpage = " <a href='{$_SERVER['PHP_SELF']}?compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
					}
					else {
						//just comp been selected
						$lastpage = " <a href='{$_SERVER['PHP_SELF']}?compid=".$_REQUEST['compid']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
					}		
				}
				//just season has been selected
				else if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
					$lastpage = " <a href='{$_SERVER['PHP_SELF']}?season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
				}				
				echo $lastpage;
				} 		
				?>
				</td>
			</tr>
			<tr>
				<td style=" padding-left:10px;"><b>Opponent</b></td>
				<td style=" padding-left:10px;"><b>Competition</b></td>
				<td style=" padding-left:10px;"><b>Date</b></td>
				<td style=" padding-left:10px;" ><b>Result</b></td>
				<?php
				if($user->role=="Team Admin"){ ?>
				<td style=" padding-left:10px; font-size:14px;"><b>Edit</b></td>
				<td style=" padding-left:10px; font-size:14px;"><b>Delete</b></td>
				<?php } ?>
			</tr>
				<?php
				for($i=1;$i<=$count1;$i++){ ?>
				<input type="hidden" id="<?php echo $result1[$i]['matchid']; ?>" value="<?php echo $result1[$i]['game']; ?>">
				<tr id="whitetext" style="font-size:18px;">
					<td style=" padding-left:10px;" width="245"><a href="viewmatch.php?id=<?php echo $result1[$i]['matchid']?>" style="cursor:pointer"><?php echo $result1[$i]['game']; ?></a></td>
					<td style=" padding-left:10px;" width="245"><?php echo $result1[$i]['competition']; ?></td>
					<td style=" padding-left:10px;" width="120"><?php echo $result1[$i]['date']; ?></td>
					<td style=" padding-left:10px;" width="190"><?php echo $result1[$i]['result']; ?></td>
					<?php
					if($user->role=="Team Admin"){ ?>
					<td align="center">
					<img src="images/Write.png" style="cursor:pointer; " alt="edit" onClick="javascript:document.location='editmatch.php?id=<?php echo $result1[$i]['matchid']; ?>'">
					</td>					
					<td align="center">
					<img src="images/Cancel.png" style="cursor:pointer; " alt="delete" onClick="DeleteMatch(<?php echo $result1[$i]['matchid']; ?>);">
					</td>
					<?php } ?>
				</tr>

			<?php } ?>

				<tr>
					<td colspan="6" align="right"><?php echo "Page ".$pagenum." of ".$last; ?></td>
				</tr>				
		</table>
		</form>
		</td>
	</tr>
	<?php } 
	else {
	//No games played.  Check if filter has been used.
		if($selected==1){
		//filter has been used, display message
		?>
	<tr>
		<td>
		<form name="tfrm" action="matches.php" method="post">
		<table cellpadding="5" cellspacing="0" border="0">
			<tr>
				<td style="padding-left:5px;">
				<?php 
				$opponents = dbToArray("call getOpponents('where teamid=".$user->teamid."')"); 
				print_dropDown("opponent", "Filter by opposition", $opponents,$o);
				?>
				</td>
				<td style="padding-left:20px;">
				<?php 
				$comps = dbToArray("call getComps('where teamid=".$user->teamid."')"); 
				print_dropDown("compid", "Filter by competition", $comps,$c);
				?>
				</td>
				<td style="padding-left:20px;">
				<?php 
				$seasons = dbToArray("call getSeasons(".$user->teamid.")"); 
				print_dropDown("season", "Filter by season", $seasons,$s);
				?>								
				</td>
			</tr>
			<tr>
				<td style="padding-left:5px;" colspan="2">
				<input type="button" value="view" onClick="View();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">&nbsp;&nbsp;
				<input type="button" value="clear" onClick="javascript:document.location='matches.php'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
				</td>
			</tr>						
		</table>
		</form>	
		</td>
	</tr>
	<tr>
		<td id="whitetext" style="padding-bottom:5px;" align="right"><strong><?php echo "Matches played: ".$count; ?></strong></td>
	</tr>	
	<tr>
		<td>	
		<table width="100%" cellpadding="5" cellspacing="0" border="0" id="whitetext">	
			<tr>
				<td style="font-size:18px; ">No matches have been played with the selected criteria.  Click on the Clear button above to reset back to all matches played.</td>
			</tr>
		</table>
		<?php
		}
	}?>
</table>

<form name="deletematch" action="matches.php" method="post">
<input type="hidden" name="id">
<input type="hidden" name="action">
</form>
<script language="javascript">
function ViewMatch(theid) {
	frm = document.viewmatch;
	frm.id.value = theid;
	frm.submit();
}
function DeleteMatch(theid){
	frm = document.deletematch;
	var team = document.getElementById('t').value;
	var opponent = document.getElementById(theid).value;
	ans = confirm("Are you sure you want to permanently remove the fixture: "+ team +" vs "+ opponent +" from the system?");
	if (ans == true ) {
		frm.action.value = "remove";
		frm.id.value = theid;
		frm.submit();
	}	
}
function View() {
	frm = document.tfrm;
	frm.submit();
}
</script>
<?php
}
?>