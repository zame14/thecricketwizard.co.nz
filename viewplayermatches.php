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
if(!isset($_REQUEST['id'])){
	redirect_rel('teamroster.php');
}
$mp = dbToArray("call getMatchesPlayedPlayer('where u.userid=".$_REQUEST['id']."','','')");
if(count($mp)==0){
	redirect_rel('teamroster.php');
}
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
								<td><?php matches_played($_REQUEST['id']); ?></td>
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
function matches_played($id){
$selected=0;
$criteria = "WHERE u.userid=".$id."";

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
			$criteria = "WHERE u.userid=".$id." AND opponent=".$opponent." AND m.compid=".$_REQUEST['compid']." AND season=".$season."";
			$selected=1;
		}
		else {
			//season not selected, but opponent and comp have been selected
			$criteria = "WHERE u.userid=".$id." AND opponent=".$opponent." AND m.compid=".$_REQUEST['compid']."";
			$selected=1;
		}
	}
	else if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
		//comp not select but opponent and season have been
		$season = '"'.$_REQUEST['season'].'"';
		$criteria = "WHERE u.userid=".$id." AND opponent=".$opponent." AND season=".$season."";			
		$selected=1;
	}
	else {
		//just opponent has been selected
		$criteria = "WHERE u.userid=".$id." AND opponent=".$opponent."";	
		$selected=1;
	}
}
//Opponent not selected but check comp and season
else if(isset($_REQUEST['compid'])&&$_REQUEST['compid']<>"Filter by competition"){
	//season and comp have been selected
	if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
		$season = '"'.$_REQUEST['season'].'"';
		$criteria = "WHERE u.userid=".$id." AND m.compid=".$_REQUEST['compid']." AND season=".$season."";
		$selected=1;
	}
	else {
		//just comp been selected
		$criteria = "WHERE u.userid=".$id." AND m.compid=".$_REQUEST['compid']."";
		$selected=1;
	}		
}
//just season has been selected
else if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
	$season = '"'.$_REQUEST['season'].'"';
	$criteria = "WHERE u.userid=".$id." AND season=".$season."";
	$selected=1;
}
$list = dbToArray("call getMatchesPlayedPlayer('".$criteria."','order by UNIX_TIMESTAMP(date) asc','');");
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

$count = count($list);
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
	$list1 = dbToArray("call getMatchesPlayedPlayer('".$criteria."','order by UNIX_TIMESTAMP(date) asc','".$max."')");	
	$count1 = count($list1);
}
?>
<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td align="left" valign="top" style="font-size:24px; padding-left:15px; padding-top:10px; padding-bottom:20px;" colspan="3" id="whitetext"><strong><?php echo $list[1]['firstname']." ".stripslashes($list[1]['lastname']); ?></strong></td>
	</tr>
	<tr>
		<td style="font-size:18px; padding-left:15px; padding-bottom:15px;" id="whitetext"><?php echo "Below is a list of all the matches played by ".$list[1]['firstname'].". To view the match in detail, click the scorecard link.
		<br>To filter them, select from the drop down menus below and click the View button. To remove the filter, click Clear.";?></td>
	</tr>
	<tr>
		<td style="padding-left:10px; ">
		<form name="tfrm" action="viewplayermatches.php?id=<?php echo $id ?>" method="post">
		<table cellpadding="5" cellspacing="0" border="0">
			<tr>
				<td>
				<?php 
				$opponents = dbToArray("call getMyOpponents('where playerid=".$id."')"); 
				print_dropDown("opponent", "Filter by opposition", $opponents,$o);
				?>
				</td>
				<td style="padding-left:20px;">
				<?php 
				$comps = dbToArray("call getMyComps('where playerid=".$id."')"); 
				print_dropDown("compid", "Filter by competition", $comps,$c);
				?>
				</td>
				<td style="padding-left:20px;">
				<?php 
				$seasons = dbToArray("call getMySeasons(".$id.")"); 
				print_dropDown("season", "Filter by season", $seasons,$s);
				?>								
				</td>
			</tr>
			<tr>
				<td style="padding-left:5px;" colspan="2">
				<input type="button" value="View" onClick="View();" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">&nbsp;&nbsp;
				<input type="button" value="Clear" onClick="javascript:document.location='viewplayermatches.php?id=<?php echo $id ?>'" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
				</td>
			</tr>						
		</table>
		</form>	
		</td>
	</tr>
	<tr>
		<td id="whitetext" style="padding-bottom:5px; padding-right:15px;" align="right"><strong><?php echo "Matches played: ".$count; ?></strong></td>
	</tr>
	<tr>
		<td style="padding-left:15px; padding-right:15px; padding-bottom:20px; ">
		<input type="hidden" name="id" value="<?php echo $list[1]['userid'];?>">
		<table width="100%" cellpadding="5" cellspacing="0" border="1" id="fixtures">
			<tr>
				<td align="right" valign="middle" colspan="5">
				<?php
				if ($pagenum == 1){
					echo "&nbsp;";
				} 
				else {
				$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
				if((isset($_REQUEST['opponent']))&&$_REQUEST['opponent']<>"Filter by opposition"){
					$opponent = '"'.$_REQUEST['opponent'].'"';
					//check if comp has been selected
					if((isset($_REQUEST['compid']))&&$_REQUEST['compid']<>"Filter by competition"){
						//check if season has been selected
						if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
							//all three have been selected so set criteria			
							$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
						}
						else {
							//season not selected, but opponent and comp have been selected
							$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
						}
					}
					else if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
						//comp not select but opponent and season have been
						$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
					}
					else {
						//just opponent has been selected
						$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
					}
				}
				//Opponent not selected but check comp and season
				else if(isset($_REQUEST['compid'])&&$_REQUEST['compid']<>"Filter by competition"){
					//season and comp have been selected
					if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
						$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
					}
					else {
						//just comp been selected
						$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&compid=".$_REQUEST['compid']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
					}		
				}
				//just season has been selected
				else if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
					$firstpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&season=".$_REQUEST['season']."&pagenum=1'><img src='images/Player FastRev.png' style='cursor:pointer' border='0' alt='First'></a> ";
				}				
				echo $firstpage;
				echo " ";
				$previous = $pagenum-1;
				$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";				
				//check if opponent has been selected
				if((isset($_REQUEST['opponent']))&&$_REQUEST['opponent']<>"Filter by opposition"){
					$opponent = '"'.$_REQUEST['opponent'].'"';
					//check if comp has been selected
					if((isset($_REQUEST['compid']))&&$_REQUEST['compid']<>"Filter by competition"){
						//check if season has been selected
						$comptext = $_REQUEST['compid'];
						if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
							//all three have been selected so set criteria			
							$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
						}
						else {
							//season not selected, but opponent and comp have been selected
							$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
						}
					}
					else if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
						//comp not select but opponent and season have been
							$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
					}
					else {
						//just opponent has been selected
						$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
					}
				}
				//Opponent not selected but check comp and season
				else if(isset($_REQUEST['compid'])&&$_REQUEST['compid']<>"Filter by competition"){
					//season and comp have been selected
					if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
						$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
					}
					else {
						//just comp been selected
						$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&compid=".$_REQUEST['compid']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
					}		
				}
				//just season has been selected
				else if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
					$prevpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&season=".$_REQUEST['season']."&pagenum=$previous'><img src='images/Player Previous.png' style='cursor:pointer' border='0' alt='Previous'></a>";
				}
				echo $prevpage;
				echo " ";
				}		
				if ($pagenum == $last) {
				} 
				else {
				$next = $pagenum+1;
				$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";				
				//check if opponent has been selected
				if((isset($_REQUEST['opponent']))&&$_REQUEST['opponent']<>"Filter by opposition"){
					$opponent = '"'.$_REQUEST['opponent'].'"';
					//check if comp has been selected
					if((isset($_REQUEST['compid']))&&$_REQUEST['compid']<>"Filter by competition"){
						//check if season has been selected
						$comptext = $_REQUEST['compid'];
						if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
							//all three have been selected so set criteria			
							$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
						}
						else {
							//season not selected, but opponent and comp have been selected
							$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
						}
					}
					else if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
						//comp not select but opponent and season have been
							$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
					}
					else {
						//just opponent has been selected
						$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
					}
				}
				//Opponent not selected but check comp and season
				else if(isset($_REQUEST['compid'])&&$_REQUEST['compid']<>"Filter by competition"){
					//season and comp have been selected
					if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
						$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
					}
					else {
						//just comp been selected
						$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&compid=".$_REQUEST['compid']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
					}		
				}
				//just season has been selected
				else if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
					$nextpage = "<a href='{$_SERVER['PHP_SELF']}?id=".$id."&season=".$_REQUEST['season']."&pagenum=$next'><img src='images/Player Next.png' style='cursor:pointer' border='0' alt='Next'></a> ";
				}				
				echo $nextpage;
				echo " ";
				$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$id."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
				//check if opponent has been selected
				if((isset($_REQUEST['opponent']))&&$_REQUEST['opponent']<>"Filter by opposition"){
					$opponent = '"'.$_REQUEST['opponent'].'"';
					//check if comp has been selected
					if((isset($_REQUEST['compid']))&&$_REQUEST['compid']<>"Filter by competition"){
						//check if season has been selected
						$comptext = $_REQUEST['compid'];
						if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
							//all three have been selected so set criteria			
							$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
						}
						else {
							//season not selected, but opponent and comp have been selected
							$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&compid".$_REQUEST['compid']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
						}
					}
					else if((isset($_REQUEST['season']))&&$_REQUEST['season']<>"Filter by season"){
						//comp not select but opponent and season have been
						$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
					}
					else {
						//just opponent has been selected
						$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$id."&opponent=".$_REQUEST['opponent']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
					}
				}
				//Opponent not selected but check comp and season
				else if(isset($_REQUEST['compid'])&&$_REQUEST['compid']<>"Filter by competition"){
					//season and comp have been selected
					if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
						$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$id."&compid=".$_REQUEST['compid']."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
					}
					else {
						//just comp been selected
						$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$id."&compid=".$_REQUEST['compid']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
					}		
				}
				//just season has been selected
				else if(isset($_REQUEST['season'])&&$_REQUEST['season']<>"Filter by season"){
					$lastpage = " <a href='{$_SERVER['PHP_SELF']}?id=".$id."&season=".$_REQUEST['season']."&pagenum=$last'><img src='images/Player FastFwd.png' style='cursor:pointer' border='0' alt='Last'></a> ";
				}				
				echo $lastpage;
				} 
				?>
				</td>
			</tr>
			<tr>
				<td style=" padding-left:10px;"><b>#</b></td>
				<td style=" padding-left:10px;" colspan="2"><b>Match</b></td>
			</tr>
				<?php
				for($i=1;$i<=$count1;$i++){ ?>
				<tr id="whitetext" style="font-size:18px;">
					<td style=" padding-left:10px;" width="10%"><?php echo $i; ?></td>
					<td style="padding-left:10px;">
					<?php echo $list1[$i]['teamname']." vs ".$list1[$i]['opponent']." at ".$list1[$i]['venue'].", ".$list1[$i]['date']."&nbsp;&nbsp;&nbsp;<a class=a2 style='font-size:12px;' href='viewmatch.php?id=".$list1[$i]['matchid']."&pid=".$id."'>scorecard</a>"; ?>
					</td>
				</tr>
		
			<?php } ?>
				<tr>
					<td colspan="5" align="right"><?php echo "Page ".$pagenum." of ".$last; ?></td>
				</tr>				
		</table> 
		</td>
	</tr>
	<tr>
		<td align="center" style="padding-top:40px; padding-bottom:10px; "><input type="button" value="Back" onClick="javascript:document.location='playerprofile.php?id=<?php echo $id ?>';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;"></td>
	</tr>
</table>
<?php
}
?>
<script language="javascript">
function View() {
	frm = document.tfrm;
	frm.submit();
}
</script>