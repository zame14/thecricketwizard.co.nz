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
								<td align="left" valign="middle" style="font-size:24px; padding-left:20px; padding-top:10px; padding-bottom:20px;" id="whitetext" colspan="3"><strong><?php echo $user->teamname; ?></strong></td>
							</tr>
							<tr>
								<td align="left" valign="middle" style="font-size:18px; padding-left:20px; padding-bottom:20px;" id="whitetext" colspan="3">A player needs to score a hundred or take six or more wickets to make the honours board.</td>
							</tr>
							<tr>
								<td align="left" style="font-size:18px; padding-left:20px; padding-bottom:20px;" id="whitetext">
								<?php
								$sql = "call getHonours('where h.teamid=".$user->teamid."','order by m.date desc','');";
								$result = dbToArray($sql);
								$count = count($result);
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
									$sql1 = "call getHonours('where h.teamid=".$user->teamid."','order by m.date desc','".$max."');";
									$result1 = dbToArray($sql1);
									$count1 = count($result1);
								}						
								?>
								<table cellpadding="0" cellspacing="0" border="0" width="100%" id="honours" height="500">
									<tr height="10">
										<td valign="top" align="center" height="10" style="padding-top:10px;"><strong>Honours Board</strong></td>
									</tr>
									<?php
									if($count<>0){ ?>
									<tr height="10">
										<td align="right" valign="middle" style=" padding-top:15px; padding-right:250px; ">
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
									<tr height="400">
										<td valign="top" style="padding-left:200px; ">
										<table cellpadding="0" cellspacing="0" border="0" width="405" style="table-layout:fixed; ">
											<?php
											if($count1){
												for($i=1;$i<=$count1;$i++){
													if($result1[$i]['runs']<>0){
														$performance = $result1[$i]['runs'];
														if($result1[$i]['did']==7){
															$vs = '* vs';
														}
														else {
															$vs = 'vs';
														}
													}
													else if($result1[$i]['wickets']<>0){
														$performance = $result1[$i]['wickets'].'/'.$result1[$i]['runsconceded'];
														$vs = 'vs';
													}
													?>
												<tr>
													<td style="padding-top:15px; font-size:15px;" id="goldtext"><?php echo $result1[$i]['date'].'&nbsp;&nbsp;&nbsp;'.$result1[$i]['player'].' &ndash; '.$performance.' '.$vs.' '.$result1[$i]['opponent'];?></td>
												</tr><?php					
												}
											}
											else { ?>
												<tr>
													<td style="padding-left:240px; padding-top:15px; font-size:15px;" id="goldtext">&nbsp;</td>
												</tr>									
											<?php
											} ?>
										</table>
										</td>				
									</tr>
									<tr>
										<td align="right" style="font-size:12px; padding-right:250px; "><?php echo "Page ".$pagenum." of ".$last; ?></td>
									</tr>	
									<?php }
									else { ?>
									<tr>
										<td style="padding-left:210px; padding-top:25px; font-size:15px;" id="goldtext" valign="top">No players have currently made the honours board.</td>
									</tr>
									<?php } ?>	
								</table>	
								</td>
							</tr>
							<tr>
								<td colspan="3" align="center" style="padding-bottom:10px; padding-top:30px; ">
								<?php
								if(isset($_REQUEST["p"])){ ?>
									<input type="button" value="Back" onClick="javascript:document.location='playerprofile.php?id=<?php echo $_REQUEST["pid"] ?>';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
								<?php
								}
								else { ?>
									<input type="button" value="Back" onClick="javascript:document.location='statshome.php';" style="color:#FFFFFF; background-color:#C80101; cursor:pointer; font-weight:bold;">
								<?php
								}
								?>
								</td>
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